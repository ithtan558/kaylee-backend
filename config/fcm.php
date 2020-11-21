<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAJnHoxYU:APA91bH4RAL7cS3gYx3AOL_x_xuI-6OTqlcinaT732ZCmT9cQNCdcxkWtCO09hnUNZ1p-FqiBB21n_MPIw4ccXHz48Ai4IAeo1TC91UDDaYcnixUaH8XxFPCY7EAkRte6ezHGH9XmnSo'),
        'sender_id' => env('FCM_SENDER_ID', '165119837573'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
