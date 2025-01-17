#include <WiFi.h>
#include <PubSubClient.h>
#include <DHT.h>

// Thông tin WiFi
const char* ssid = "your-wifi-name";
const char* password = "your-wifi-password";

// Thông tin MQTT
const char* mqtt_server = "Your-Broker-IP";   // Địa chỉ IP của broker
const int mqtt_port = your-broker-port;                  // Cổng của broker
const char* mqtt_user = "your-mqtt-username";         // Username MQTT
const char* mqtt_pass = "your-mqtt-password";       // Password MQTT

// Định nghĩa chân cho DHT11 và LDR
#define DHTPIN 4            // Chân kết nối DHT11
#define DHTTYPE DHT11      // Định nghĩa loại DHT
DHT dht(DHTPIN, DHTTYPE);

#define LDR_PIN 34         // Chân kết nối LDR (analog pin)

// Định nghĩa chân cho 4 đèn LED
#define LED1_PIN 22
#define LED2_PIN 23
#define LED3_PIN 21
#define WARNING_LED_PIN 19  // Chân kết nối đèn cảnh báo

WiFiClient espClient;
PubSubClient client(espClient);

void setup_wifi() {
  delay(10);
  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");
}

void callback(char* topic, byte* payload, unsigned int length) {
  String message;

  for (int i = 0; i < length; i++) {
    message += (char)payload[i];
  }

  Serial.print("Message received in topic [");
  Serial.print(topic);
  Serial.print("]: ");
  Serial.println(message);

  // Kiểm tra các lệnh bật/tắt đèn
  if (String(topic) == "home/led1") {
    if (message == "on") {
      digitalWrite(LED1_PIN, HIGH);
      client.publish("home/led/status", "LED1 is ON");
    } else if (message == "off") {
      digitalWrite(LED1_PIN, LOW);
      client.publish("home/led/status", "LED1 is OFF");
    }
  }

  if (String(topic) == "home/led2") {
    if (message == "on") {
      digitalWrite(LED2_PIN, HIGH);
      client.publish("home/led/status", "LED2 is ON");
    } else if (message == "off") {
      digitalWrite(LED2_PIN, LOW);
      client.publish("home/led/status", "LED2 is OFF");
    }
  }

  if (String(topic) == "home/led3") {
    if (message == "on") {
      digitalWrite(LED3_PIN, HIGH);
      client.publish("home/led/status", "LED3 is ON");
    } else if (message == "off") {
      digitalWrite(LED3_PIN, LOW);
      client.publish("home/led/status", "LED3 is OFF");
    }
  }

  // Kiểm tra lệnh bật/tắt tất cả đèn
  if (String(topic) == "home/all_leds") {
    if (message == "on") {
      digitalWrite(LED1_PIN, HIGH);
      digitalWrite(LED2_PIN, HIGH);
      digitalWrite(LED3_PIN, HIGH);
      client.publish("home/led/status", "All LEDs are ON");
    } else if (message == "off") {
      digitalWrite(LED1_PIN, LOW);
      digitalWrite(LED2_PIN, LOW);
      digitalWrite(LED3_PIN, LOW);
      client.publish("home/led/status", "All LEDs are OFF");
    }
  }
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Connecting to MQTT...");
    // Kết nối MQTT với username và password
    if (client.connect("ESP32Client", mqtt_user, mqtt_pass)) {
      Serial.println("connected");

      // Subscribe các topic cho đèn LED
      client.subscribe("home/led1");
      client.subscribe("home/led2");
      client.subscribe("home/led3");
      client.subscribe("home/all_leds");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      delay(5000);
    }
  }
}

void publishSensorData() {
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();
  int lightLevel = analogRead(LDR_PIN); // Đọc giá trị LDR
  int wind = random(0, 101); // Sinh giá trị ngẫu nhiên cho gió (từ 0 đến 100)

  // Kiểm tra xem có đọc dữ liệu thành công không
  if (isnan(humidity) || isnan(temperature)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }

  // Tạo JSON payload
  String payload = String("{\"temperature\":") + temperature +
                   ",\"humidity\":" + humidity +
                   ",\"light\":" + (4095 - lightLevel) +
                   ",\"wind\":" + wind + "}";

  // Gửi dữ liệu lên MQTT
  client.publish("home/sensor_data", payload.c_str());
  Serial.println("Published: " + payload);

  // Nháy đèn cảnh báo nếu giá trị gió > 80
  if (wind > 60) {
    digitalWrite(WARNING_LED_PIN, HIGH); // Bật đèn cảnh báo
    delay(500); // Để đèn sáng trong 500ms
    digitalWrite(WARNING_LED_PIN, LOW); // Tắt đèn cảnh báo
    delay(500); // Để đèn tắt trong 500ms
  } else {
    digitalWrite(WARNING_LED_PIN, LOW); // Tắt đèn cảnh báo nếu gió ≤ 80
  }
}

void setup() {
  Serial.begin(9600);

  // Khởi tạo chân LED là output
  pinMode(LED1_PIN, OUTPUT);
  pinMode(LED2_PIN, OUTPUT);
  pinMode(LED3_PIN, OUTPUT);
  pinMode(WARNING_LED_PIN, OUTPUT); // Khởi tạo chân đèn cảnh báo

  setup_wifi();
  client.setServer(mqtt_server, mqtt_port);
  client.setCallback(callback);
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  
  client.loop();

  // Gửi dữ liệu cảm biến mỗi 2 giây
  static unsigned long lastPublish = 0;
  if (millis() - lastPublish > 2000) {
    lastPublish = millis();
    publishSensorData();
  }
}
