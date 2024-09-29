<?php

return [
    'broker' => [
        'host' => env('MQTT_HOST', '3484371f141f47a5aaa3c64216ba0e7d.s1.eu.hivemq.cloud'), // Địa chỉ MQTT broker
        'port' => env('MQTT_PORT', 8883), // Cổng MQTT
        'username' => env('MQTT_USERNAME', 'leediay153'), // Username MQTT
        'password' => env('MQTT_PASSWORD', 'johnbeo123'), // Password MQTT
    ],
];
