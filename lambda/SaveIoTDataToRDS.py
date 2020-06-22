import json
import sys
import pymysql

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

    # Parse the JSON message
    eventText = json.dumps(event)

    # Print the parsed JSON message to the console. You can view this text in the Monitoring tab in the AWS Lambda console or in the Amazon CloudWatch Logs console.
    print('Received event: ', eventText)

    print('Saving to RDS...')
    with conn.cursor() as cur:
        cur.execute('insert into sensor_data (temperature, humidity, pressure, rain) values({}, {}, {}, {})'.format(event["temperature"], event["humidity"], event["pressure"], event["rain"]))
        conn.commit()
    conn.commit()
    print('Save to RDS done...')
