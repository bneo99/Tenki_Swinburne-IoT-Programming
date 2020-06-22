#include <ESP8266WiFi.h>
#include <ESP8266mDNS.h>
#include <WiFiUdp.h>
#include <ArduinoOTA.h>

#include "Adafruit_MQTT.h"
#include "Adafruit_MQTT_Client.h"

#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>

/************************* WiFi Access Point *********************************/

#define WLAN_SSID       "-"
#define WLAN_PASS       "-"

/************************* MQTT Server Setup *********************************/

#define MQTT_SERVER      "192.168.0.100"
#define MQTT_SERVERPORT  1883
#define MQTT_USERNAME    ""
#define MQTT_KEY         ""

/************************* Sensor setup **************************/
Adafruit_BME280 bme; //use i2c

/************************* Pinouts setup **************************/
#define STATUS_LED          2   //esp8266 onboard led
#define RAIN_SENSOR_ENABLE  16  //high enables the rain sensor

//i2c pinouts for the sensor
#define BME280_SDA          12
#define BME280_SCL          14


//sensor values packet (define the format of packet to send)
char sensorFormat[100] = "{\"temperature\":%f, \"humidity\":%f, \"pressure\":%f, \"rain\":%d}";

/************ Global State (you don't need to change this!) ******************/

// Create an ESP8266 WiFiClient class to connect to the MQTT server.
WiFiClient client;

// Setup the MQTT client class by passing in the WiFi client and MQTT server and login details.
Adafruit_MQTT_Client mqtt(&client, MQTT_SERVER, MQTT_SERVERPORT, MQTT_USERNAME, MQTT_KEY);

/****************************** MQTT Topic Definition ***************************************/

// Following Tasmota paths (stat,tele,cmdn)/{room_name}/{topic}

// For sensor
// using tele(metry) prefix in this case as sensor data is reported at constant intervals
Adafruit_MQTT_Publish sensorTeleTopic = Adafruit_MQTT_Publish(&mqtt, "tele/tenki/sensor");

/*************************** Sketch Code ************************************/

// Bug workaround for Arduino 1.6.6, it seems to need a function declaration
// for some reason (only affects ESP8266, likely an arduino-builder bug).
void MQTT_connect();

void setup() {
  Serial.begin(115200);
  delay(10);

  Serial.println();
  Serial.println("Tenki to Shokubutsu Sensor Node");
  pinMode(STATUS_LED, OUTPUT);
  pinMode(RAIN_SENSOR_ENABLE, OUTPUT);

  // init sensor
  Wire.begin(BME280_SDA, BME280_SCL);
  if (!bme.begin()) {
    Serial.println("sensor init failed :(    restarting...");
    delay(1000);
    ESP.restart();
  }

  // Connect to WiFi access point.
  Serial.print("Connecting to ");
  Serial.println(WLAN_SSID);

  //set as STA mode (act as client)
  WiFi.mode(WIFI_STA);
  WiFi.begin(WLAN_SSID, WLAN_PASS);

  //auto reconnect if wifi dies
  WiFi.setAutoReconnect (true);
  
  //wait for wifi to connect
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  //wifi connected, show ip
  Serial.println("WiFi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  //set hostname
  ArduinoOTA.setHostname("tenki-sensor");

  //setup ota
  ArduinoOTA.onStart([]() {
    String type;
    if (ArduinoOTA.getCommand() == U_FLASH) {
      type = "sketch";
    } else { // U_SPIFFS
      type = "filesystem";
    }

    // NOTE: if updating SPIFFS this would be the place to unmount SPIFFS using SPIFFS.end()
    Serial.println("Begin OTA update: " + type);
  });
  ArduinoOTA.onEnd([]() {
    Serial.println("OTA End");
  });
  ArduinoOTA.onProgress([](unsigned int progress, unsigned int total) {
    Serial.printf("Progress: %u%%\r", (progress / (total / 100)));
  });
  ArduinoOTA.onError([](ota_error_t error) {
    Serial.printf("OTA Error[%u]: ", error);
    if (error == OTA_AUTH_ERROR) {
      Serial.println("Auth Failed");
    } else if (error == OTA_BEGIN_ERROR) {
      Serial.println("Begin Failed");
    } else if (error == OTA_CONNECT_ERROR) {
      Serial.println("Connect Failed");
    } else if (error == OTA_RECEIVE_ERROR) {
      Serial.println("Receive Failed");
    } else if (error == OTA_END_ERROR) {
      Serial.println("End Failed");
    }
  });
  ArduinoOTA.begin();

}

void loop() {
  //handle OTA
  ArduinoOTA.handle();
  
  // Ensure the connection to the MQTT server is alive (this will make the first
  // connection and automatically reconnect when disconnected).  See the MQTT_connect
  // function definition further below.
  MQTT_connect();

  // this is our 'wait for incoming subscription packets' busy subloop
  // try to spend your time here

  Adafruit_MQTT_Subscribe *subscription;
  while ((subscription = mqtt.readSubscription(10000))) {

    //code should generally run here?
  }

  // Now we can publish stuff!
  digitalWrite(STATUS_LED, LOW);
  Serial.print("Sending sensor values: ");
  char sensorPacket[1000];
  sprintf(sensorPacket, sensorFormat, bme.readTemperature(), bme.readHumidity(), bme.readPressure(), checkRain());
  Serial.print(sensorPacket);
  if (! sensorTeleTopic.publish(sensorPacket)) {
    Serial.println(" Failed");
  } else {
    Serial.println(" OK!");
  }
  digitalWrite(STATUS_LED, HIGH);
}

// Function to connect and reconnect as necessary to the MQTT server.
// Should be called in the loop function and it will take care if connecting.
void MQTT_connect() {
  int8_t ret;

  // Stop if already connected.
  if (mqtt.connected()) {
    return;
  }

  Serial.print("Connecting to MQTT... ");

  uint8_t retries = 3;
  while ((ret = mqtt.connect()) != 0) { // connect will return 0 for connected
    Serial.println(mqtt.connectErrorString(ret));
    Serial.println("Retrying MQTT connection in 5 seconds...");
    mqtt.disconnect();
    delay(5000);  // wait 5 seconds
    retries--;
    if (retries == 0) {
      Serial.println("Can't connect to MQTT broker, restarting...");
      ESP.restart();
    }
  }
  Serial.println("MQTT Connected!");
}

//check the rain sensor if there is rain detected
unsigned int checkRain() {
  //enable the sensor
  digitalWrite(RAIN_SENSOR_ENABLE, HIGH);
  delay(100); //wait for sensor to stabilise?

  int rainValue = analogRead(A0);

  //disable the sensor
  digitalWrite(RAIN_SENSOR_ENABLE, LOW);

  //return value
  return rainValue;
}
