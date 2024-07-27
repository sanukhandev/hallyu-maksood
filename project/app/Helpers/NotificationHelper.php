<?php

namespace app\Helpers;


define('INFOBIP_API_KEY', '430ec63fe814a0947138735d10976378-fd095b88-f355-41e8-92af-60ea404d396f');
define('INFOBIP_BASE_URL', 'https://mmn664.api.infobip.com');
class NotificationHelper
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = INFOBIP_API_KEY;
        $this->baseUrl = INFOBIP_BASE_URL;

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
