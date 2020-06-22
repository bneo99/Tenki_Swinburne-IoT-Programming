import boto3
import json
import pymysql

client = boto3.client('lambda')

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

    #replace current with default config
    with conn.cursor() as cur:
        cur.execute("update config as current_schedule, config as default_schedule set current_schedule.schedule = default_schedule.schedule where current_schedule.id = 2 and default_schedule.id = 1")
    conn.commit()

    #invoke lambda to push changes to node
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
        'message' : "schedule refreshed"
    }

    return response
