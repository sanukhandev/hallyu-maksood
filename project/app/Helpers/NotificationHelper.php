<?php

namespace app\Helpers;

use Dotenv\Dotenv;

class NotificationHelper
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        // Load environment variables from .env file
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        // Get Infobip configuration from environment variables
        $this->apiKey = getenv('INFOBIP_API_KEY');
        $this->baseUrl = getenv('INFOBIP_BASE_URL');

        // Check if the environment variables are set
        if (!$this->apiKey || !$this->baseUrl) {
            throw new \Exception('Infobip configuration not set in environment variables.');
        }
    }

    public function sendSms($to, $message)
    {
        $url = $this->baseUrl . '/sms/2/text/advanced';

        $data = [
            'messages' => [
                [
                    'from' => 'ServiceSMS',
                    'destinations' => [
                        ['to' => $to]
                    ],
                    'text' => $message
                ]
            ]
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: App ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Request Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
