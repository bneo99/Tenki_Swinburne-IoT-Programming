import pymysql
import json
import boto3
from datetime import datetime
from datetime import timezone

#rds settings
rds_host  = "RDS_HOST"
name = "DB_USERNAME"
password = "DB_PASSWORD"
db_name = "DB_NAME"

print("Connecting to RDS...")
try:
    conn = pymysql.connect(rds_host, user=name, passwd=password, db=db_name, connect_timeout=5)
except pymysql.MySQLError as e:
    print("ERROR: Unexpected error: Could not connect to MySQL instance.")
    print(e)
    sys.exit()

def lambda_handler(event, context):
    # check if we need to remove the schedule
    #default rule: if schedule is 1-3hr from now we remove it
    #lazy checking: we only check the hour property if its within 2 hour
    # which means that the actual time range is 1hr 1m - 2hr 59m

    #use dict cursor so we can access the row as a dict
    with conn.cursor(pymysql.cursors.DictCursor) as cur:
        # fetch only current schedule
        cur.execute("select schedule from config where id = 2")
        current_config = cur.fetchone()
    conn.commit()

    datetime_now = datetime.now()
    print("current time: ", datetime_now)
    #schedule stored as json string, decode it into dict first
    current_schedule = json.loads(current_config["schedule"]);
    to_remove = []
    for time in current_schedule:
        diff_hour = time["hour"] - datetime_now.hour
        if diff_hour >= 0 and diff_hour <= 2:
            #add this to be removed
            print("found! ", time, "  removing...")
            to_remove.append(time)

    #if theres stuff to remove
    if to_remove:
        #remove them now
        for time in to_remove:
            current_schedule.remove(time)

        print("updated schedule: ", current_schedule)
        print("pushing to database")
        #update the database
        with conn.cursor() as cur:
            # fetch only current schedule
            cur.execute("update config set schedule='{}' where id = 2".format(json.dumps(current_schedule)))
        conn.commit()

        client = boto3.client('lambda')

        #send trigger to push changes to node
        client.invoke(
            FunctionName='PushCurrentScheduleToMQTT',
            InvocationType='Event',
            LogType='None',
            ClientContext='',
            Payload='',
            Qualifier='1'
        )
        response = {
            "StatusCode": 200,
            'message' : "schedule found and removed"
        }

    else:
        #nothing to do
        response = {
            "StatusCode": 200,
            'message' : "no schedule within next 0-3 hours"
        }

    return response
