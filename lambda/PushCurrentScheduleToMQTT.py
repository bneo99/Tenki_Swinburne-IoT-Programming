import boto3
import json
import pymysql
from datetime import datetime

#rds settings
rds_host  = "RDS_HOST"
name = "DB_USERNAME"
password = "DB_PASSWORD"
db_name = "DB_NAME"

def lambda_handler(event, context):
    print("Connecting to RDS...")
    try:
        conn = pymysql.connect(rds_host, user=name, passwd=password, db=db_name, connect_timeout=5)
    except pymysql.MySQLError as e:
        print("ERROR: Unexpected error: Could not connect to MySQL instance.")
        print(e)
        sys.exit()
    print("Connected to RDS...")

    #get current config from rds
    with conn.cursor(pymysql.cursors.DictCursor) as cur:
        cur.execute("select * from config where id = 2")
        config = cur.fetchone()

    #modify the config so we only send the relevant info to the node
    device_config = {}
    device_config["schedule"] = json.loads(config["schedule"])
    device_config["duration"] = config["duration"]
    device_config["revision"] = int(config["revision"].timestamp())

    iotclient = boto3.client('iot-data', region_name='us-east-1')

    # publish it
    response = iotclient.publish(
        topic='cmnd/tenki/control',
        qos=1,
        payload=json.dumps({"command":"update config", "config":device_config})
    )
    return response
