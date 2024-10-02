# Bài tập lớn môn IoT và ứng dụng
#### Sinh viên: Lê Đức Anh - B21DCAT026 - Lớp: IoT 06
## Đề tài: Xây dựng ứng dụng Web điều khiển Smart Home

#### 1. Công nghệ sử dụng:
>Phần mềm:
>- Framework sử dụng: **Laravel**.
>- Sử dụng database **MySQL** để lưu trữ dữ liệu.
>- Trình soạn thảo: **Visual Studio Code**.
>- MQTT Broker: localhost
>- Arduino IDE.

>Phần cứng:
>- Vi điều khiển: **ESP-WROOM-32**.
>- Cảm biến nhiệt độ và độ ẩm: **DHT11**.
>- Cảm biến ánh sáng (quang trở): **LDR**
>- Breadboard.
>- Dây nối: đực-đực.
>- Đèn LED 5mm

#### 2. Giao diện Web:
Giao diện trang Login: 
![image](https://hackmd.io/_uploads/SJ_GiBqCR.png)

Giao diện trang Register Account:
![image](https://hackmd.io/_uploads/S1EviH9C0.png)

Giao diện Dashboard:
![image](https://hackmd.io/_uploads/rJoZvr_CR.png)

Giao diện Device History:
![image](https://hackmd.io/_uploads/By4sYruAR.png)

Giao diện Sensor Data History:
![image](https://hackmd.io/_uploads/BJdTFr_AC.png)

Giao diện My Profile:
![image](https://hackmd.io/_uploads/r1MxqrOCA.png)

Giao diện Change Password: 
![image](https://hackmd.io/_uploads/SJQf9SdR0.png)

#### 3. Chi tiết bảng mạch:
Góc chụp bên phải:
![mach](https://hackmd.io/_uploads/Syo_iHdRC.jpg)

Góc chụp bên trái: 
![461130012_1872256803261557_6973840931002495687_n](https://hackmd.io/_uploads/H1FhiruR0.jpg)

Góc chụp chính diện: 
![460165057_3890455757882319_791606423860525749_n](https://hackmd.io/_uploads/SJ4y3B_C0.jpg)

#### 4. Cách set up môi trường và các công cụ cần thiết:

##### Đầu tiên, ta cần khởi chạy được trang web:
Clone dự án từ Github:
```none
git clone https://github.com/LeeDiay/SmartHome-IoT
cd SmartHome-IoT
```

Cài đặt Laragon, Composer desktop. Sau đó, chạy composer và npm để cài đặt các gói cần thiết trong dự án

```none
composer install
npm install 
```

Thực hiện lệnh sau để copy ra file env:  

```none
cp .env.example .env
```

Tạo database và cập nhật file .env:

Cập nhật file env của bạn như sau:

```none
DB_CONNECTION=mysql          
DB_HOST=127.0.0.1            
DB_PORT=3306                 
DB_DATABASE=your-db-name    
DB_USERNAME=root             
DB_PASSWORD=   
```
Tạo ra key cho dự án:

```none
php artisan key:generate
```

Tạo ra các bảng và dữ liệu mẫu cho database:

```none
php artisan migrate
php artisan db:seed
```

Khởi chạy project:

```none
php artisan serve
```

Đăng nhập với tài khoản và mật khẩu cho sẵn (**admin:12345678**)

##### Tiếp theo, nạp code vào ESP32:

Cài đặt Arduino, cùng các thư viện cần thiết: DHT11, esp32, ...

Cài đặt Driver để nhận máy tính nhận Port khi kết nối với ESP32. Link tải ở đây: [link ](https://www.silabs.com/developers/usb-to-uart-bridge-vcp-drivers?tab=downloads)

Copy code trong file ***code_full_with_esp32.ino***, tiến hành chọn đúng Board, Port đang sử dụng và tiến hành upload code lên ESP32.

##### Cuối cùng, tải MQTT Broker để chạy server mqtt trên local:

Cài đặt mqtt về máy, mở cmd tại thư mục đã cài đặt mosquitto. 

Setup username, password cho broker:
```
mosquitto_passwd -c "your-file-locate-username" "your-username"
```
Nhập mật khẩu cho username. Sau đó, mở file *mosquitto.conf*, thêm các dòng này vào để config lại và lưu lại:
```
listener your-custom-port
allow_anonymous false

password_file "your-file-locate-username"
```

Bên ngoài cmd, chạy lại lệnh sau để load lại config và chạy lại server:
```
mosquitto -c mosquitto.conf -v
```

Hiển thị thông báo thành công, ví dụ:

```
D:\DucAnh_WebSec\mosquitto>mosquitto -c mosquitto.conf -v
1727836863: mosquitto version 2.0.18 starting
1727836863: Config loaded from mosquitto.conf.
1727836863: Opening ipv6 listen socket on port 4444.
1727836863: Opening ipv4 listen socket on port 4444.
1727836863: mosquitto version 2.0.18 running
```

Thay đổi các giá trị này trong file *.env* :

```none
MQTT_HOST=your-broker-host (recommend: localhost)
MQTT_PORT=your-broker-port
MQTT_USERNAME=your-mqtt-username
MQTT_PASSWORD=your-mqtt-password
```

. Để bắt đầu sub message từ esp về, sử dụng lệnh: 

```none
php artisan mqtt:sensor-subscribe
```

Nếu có bất kì lỗi nào, hãy quan sát file log ***/storage/logs/laravel.log*** trong Laravel để debug!

