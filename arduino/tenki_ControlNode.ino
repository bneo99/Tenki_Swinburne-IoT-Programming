#include <ESP8266WiFi.h>
#include <ESP8266mDNS.h>
#include <WiFiUdp.h>
#include <ArduinoOTA.h>
#include <NTPClient.h>
#include <MqttClient.h>
#include <ArduinoJson.h>
#include <FS.h>
#include <LittleFS.h>

/************************* WiFi Access Point *********************************/

#define WLAN_SSID       "-"
#define WLAN_PASS       "-"
#define HOSTNAME        "tenki-control"

/************************* MQTT Server Setup *********************************/

#define MQTT_SERVER      "192.168.0.100"
#define MQTT_SERVERPORT  1883
#define MQTT_USERNAME    ""
#define MQTT_KEY         ""

#define STATUS_LED           12
#define PUMP_CONTROL         0

//sensor values packet (define the format of packet to send)
const char controlTeleFormat[] = "{\"wateringPeriod\":%s}";

static WiFiClient network;

/****************************** MQTT Topic Definition ***************************************/

// Following Tasmota paths (stat,tele,cmdn)/{room_name}/{topic}

static MqttClient *mqtt = NULL;

// ============== Object to supply system functions ============================
class System: public MqttClient::System {
  public:

    unsigned long millis() const {
      return ::millis();
    }

    //supposed to be yielding (so we can run background keepalive tasks)
    //but i need to keep OTA alive also, so OTA handling is placed here
    void yield(void) {
      //handle OTA
      ArduinoOTA.handle();

      //actually yield now
      ::yield();
    }
};

// Init topics for sending when watering is done and for receiving new schedule
const char* cmndTopic = "cmnd/tenki/control";
const char* statTopic = "stat/tenki/control";
const char* teleTopic = "tele/tenki/control";

/*************************** Default watering schedule ************************/
//we can update the schedule over mqtt but hardcoding default schedule
//proper way is store it in eeprom/flash so we always get updated
//but lazy/no time to test storing it in eeprom/flash

String waterConfigDefault = "{\"schedule\":[{\"hour\":07,\"minute\":30},{\"hour\":12,\"minute\":45},{\"hour\":17,\"minute\":30}],\"duration\":1,\"revision\":0}";

// Json document and the object for the config
DynamicJsonDocument jsonDoc(2048);
JsonObject waterConfig;


//init NTP (needed to know the time)
//if we are running on a system that might not have reliable internet
//a RTC chip might be better, or a local NTP server
//but lazy to set one up so NTP

//timezone will be default (UTC)
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP);

/*************************** Sketch Code ************************************/

// callbacks for MQTT

void processMessage(MqttClient::MessageData& md) {
  //task start, set status high
  digitalWrite(STATUS_LED, HIGH);

  const MqttClient::Message& msg = md.message;

  //init json document
  DynamicJsonDocument messageDoc(1024);

  char payload[msg.payloadLen + 1];
  memcpy(payload, msg.payload, msg.payloadLen);
  payload[msg.payloadLen] = '\0';

  //parse the message
  deserializeJson(messageDoc, payload);
  JsonObject message = messageDoc.as<JsonObject>();

  String messageCommand = message["config"];

  //only handle if new config is packaged in the message
  if (messageCommand) {

    String command = message["command"];

    if (command.equals("show config")) {
      //send the current config back
      String jsonString;
      serializeJson(jsonDoc, jsonString);
      int stringLength = jsonString.length() + 1;
      char payloadBuf[stringLength];

      jsonString.toCharArray(payloadBuf, stringLength);
      mqttPublish(statTopic, payloadBuf);
    }

    if (command.equals("update config")) {
      JsonVariant messageConfig = message["config"];

      //only handle if new config is packaged in the message
      if (!messageConfig.isNull()) {
        //get the new schedule and update local copy
        waterConfig.set(messageConfig);

        String jsonString;
        serializeJson(jsonDoc, jsonString);
        //save to flash
        writeConfig(jsonString);

        //send the updated config back
        int stringLength = jsonString.length() + 1;
        char payloadBuf[stringLength];

        jsonString.toCharArray(payloadBuf, stringLength);
        mqttPublish(statTopic, payloadBuf);
      }
    }

    if (command.equals("format littlefs")) {
      Serial.println("Formatting LittleFS filesystem");
      LittleFS.format();
      die();
    }
  }

  //task done, set status low
  digitalWrite(STATUS_LED, LOW);
}

void setup() {
  Serial.begin(115200);

  pinMode(STATUS_LED, OUTPUT);
  pinMode(PUMP_CONTROL, OUTPUT);

  //keep status led high while we setup
  digitalWrite(STATUS_LED, HIGH);

  //set as STA mode (act as client)
  WiFi.mode(WIFI_STA);
  WiFi.begin(WLAN_SSID, WLAN_PASS);

  //auto reconnect if wifi dies
  WiFi.setAutoReconnect (true);

  //wait for wifi to connect
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
  }

  //set hostname
  ArduinoOTA.setHostname(HOSTNAME);

  //setup ota
  ArduinoOTA.onStart([]() {
    //task start, set status high
    digitalWrite(STATUS_LED, HIGH);
    String type;
    if (ArduinoOTA.getCommand() == U_FLASH) {
      type = "sketch";
    } else { // U_SPIFFS
      type = "filesystem";
    }
  });
  ArduinoOTA.onEnd([]() {
    //task end, set status low
    digitalWrite(STATUS_LED, LOW);
  });
  ArduinoOTA.onError([](ota_error_t error) {
    die();
  });
  ArduinoOTA.begin();

  // Setup MqttClient
  MqttClient::System *mqttSystem = new System;
  MqttClient::Logger *mqttLogger = new MqttClient::LoggerImpl<HardwareSerial>(Serial);
  MqttClient::Network * mqttNetwork = new MqttClient::NetworkClientImpl<WiFiClient>(network, *mqttSystem);
  //// Make 128 bytes send buffer
  MqttClient::Buffer *mqttSendBuffer = new MqttClient::ArrayBuffer<1500>();
  //// Make 1024 bytes receive buffer (we're sending big packets)
  MqttClient::Buffer *mqttRecvBuffer = new MqttClient::ArrayBuffer<1500>();
  //// Allow up to 2 subscriptions simultaneously
  MqttClient::MessageHandlers *mqttMessageHandlers = new MqttClient::MessageHandlersImpl<3>();
  //// Configure client options
  MqttClient::Options mqttOptions;
  ////// Set command timeout to 500 milliseconds
  mqttOptions.commandTimeoutMs = 500;
  //// Make client object
  mqtt = new MqttClient(
    mqttOptions, *mqttLogger, *mqttSystem, *mqttNetwork, *mqttSendBuffer,
    *mqttRecvBuffer, *mqttMessageHandlers
  );

  //init NTP
  timeClient.begin();

  //init littleFS
  Serial.println("Mount LittleFS");
  if (!LittleFS.begin()) {
    Serial.println("LittleFS mount failed");
    Serial.println("Formatting LittleFS filesystem");
    LittleFS.format();
    die();
  }

  readConfig();

  //init json stuff
  //deserializeJson(jsonDoc, waterConfigRaw);
  waterConfig = jsonDoc.as<JsonObject>();

  //init over, turn status off
  digitalWrite(STATUS_LED, LOW);
}

void loop() {

  //handle NTP
  timeClient.update();

  //handle MQTT
  if (!mqtt->isConnected()) {
    // Close connection if exists
    network.stop();
    // Re-establish TCP connection with MQTT broker
    network.connect(MQTT_SERVER, MQTT_SERVERPORT);
    if (!network.connected()) {
      delay(5000);
      ESP.reset();
    }
    // Start new MQTT connection
    MqttClient::ConnectResult connectResult;
    // Connect
    {
      MQTTPacket_connectData options = MQTTPacket_connectData_initializer;
      options.MQTTVersion = 4;
      options.clientID.cstring = (char*)HOSTNAME;
      options.cleansession = true;
      options.keepAliveInterval = 15; // 15 seconds
      MqttClient::Error::type rc = mqtt->connect(options, connectResult);
      if (rc != MqttClient::Error::SUCCESS) {
        return;
      }
    }
    {
      // Add subscribe here if required
      mqtt->subscribe(cmndTopic, MqttClient::QOS0, processMessage);
    }
  } else {
    {

      //so this part actually runs once every 60 secs
      //thanks to the yield

      //check if its time to water the plants
      //get the time schedule and water duration


      //since the yield will cause this to run once a minute, the units in the duration will be in minutes
      JsonVariant timeSchedule = waterConfig["schedule"];
      int waterDuration = (int)waterConfig["duration"]; //minutes

      int currentHour = timeClient.getHours();
      int currentMinute = timeClient.getMinutes();

      //cycle through all the schedule, check if theres a kena
      for (int x = 0; x < timeSchedule.size(); x++) {
        int scheduleHour = (int)timeSchedule[x]["hour"];
        int scheduleMinute = (int)timeSchedule[x]["minute"];

        // hour/minute overflow checking
        int scheduleNextHour = scheduleHour + 1;
        if (scheduleNextHour > 23) scheduleNextHour = 0;

        int scheduleNextMinute = scheduleMinute + waterDuration;
        if (scheduleNextMinute > 59) {
          int offset = scheduleNextMinute - 60;
          scheduleNextMinute = 0 + offset;
        }

        char lol[20];
        sprintf(lol, "curr: % d, next: % d", currentMinute, scheduleNextMinute);
        mqttPublish(teleTopic, lol);

        if (currentHour >= scheduleHour && currentHour < scheduleNextHour) {
          mqttPublish(teleTopic, "aaaa");
          if (currentMinute >= scheduleMinute && currentMinute < scheduleNextMinute) {
            mqttPublish(teleTopic, "bbbb");
            //turn on pump
            digitalWrite(PUMP_CONTROL, HIGH);
            break;
          }
          //turn off pump
          else digitalWrite(PUMP_CONTROL, LOW);
        }
        //turn off pump
        else digitalWrite(PUMP_CONTROL, LOW);
      }

    }
    // Idle for 60 seconds
    mqtt->yield(60000L);
  }
}

//publish a message to the given topic
void mqttPublish(const char* topic, const char* buf) {
  MqttClient::Message message;
  message.qos = MqttClient::QOS0;
  message.retained = false;
  message.dup = false;
  message.payload = (void *) buf;
  message.payloadLen = strlen(buf);
  mqtt->publish(topic, message);
}

void die() {
  for (int i = 0; i < 5; i++) {
    digitalWrite(STATUS_LED, HIGH);
    delay(500);
    digitalWrite(STATUS_LED, LOW);
    delay(500);
  }
  ESP.restart();
}

void readConfig() {
  Serial.println("Reading config...");

  File file = LittleFS.open("config", "r");
  if (!file) {
    Serial.println("Failed to open file for reading, restore from default");
    writeConfig(waterConfigDefault);

    file = LittleFS.open("config", "r");
  }

  String readConfig;

  Serial.println("Read from file...");
  while (file.available()) {
    char c = file.read();
    readConfig += c;
  }

  DeserializationError derr = deserializeJson(jsonDoc, readConfig);

  if (derr != DeserializationError::Ok) {
    Serial.println(derr.c_str());
    Serial.print("data: ");
    Serial.println(readConfig);
    Serial.println("read null... writing defaults");
    writeConfig(waterConfigDefault);
    deserializeJson(jsonDoc, waterConfigDefault);
  }
  else {
    Serial.print("data: ");
    Serial.println(readConfig);
  }

  file.close();
}

void writeConfig(String configStr) {
  Serial.print("Writing config: ");
  Serial.println(configStr);

  File file = LittleFS.open("config", "w");
  if (!file) {
    Serial.println("Failed to open file for writing");
    return;
  }
  if (file.print(configStr)) {
    Serial.println("File written");
  } else {
    Serial.println("Write failed");
  }
  file.close();
}
