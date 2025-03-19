<?php
declare(strict_types=1);

class WPSOOTPVendors {
    private array $vendors = [
        'twilio' => [
            'url' => 'https://api.twilio.com/2010-04-01/Accounts/{sid}/Messages.json',
            'auth' => 'basic',
            'fields' => ['sid', 'token', 'from']
        ],
        'nexmo' => [
            'url' => 'https://rest.nexmo.com/sms/json',
            'auth' => 'query',
            'fields' => ['key', 'secret', 'from']
        ],
        'messagebird' => [
            'url' => 'https://rest.messagebird.com/messages',
            'auth' => 'header',
            'fields' => ['key', 'from']
        ]
    ];

    public function send_phone_otp(string $phone, string $otp): void {
        $vendor = get_option('wpso_otp_vendor', 'twilio');
        if (!isset($this->vendors[$vendor])) return;

        $config = $this->vendors[$vendor];
        $message = sprintf(__('Your OTP is: %s. Expires in 5 min.', 'wp-security-optimizer'), $otp);

        switch ($vendor) {
            case 'twilio':
                $sid = get_option('wpso_otp_twilio_sid');
                $token = get_option('wpso_otp_twilio_token');
                $from = get_option('wpso_otp_twilio_from');
                wp_remote_post(str_replace('{sid}', $sid, $config['url']), [
                    'body' => [
                        'From' => $from,
                        'To' => $phone,
                        'Body' => $message
                    ],
                    'headers' => [
                        'Authorization' => 'Basic ' . base64_encode("{$sid}:{$token}"),
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ]
                ]);
                break;

            case 'nexmo':
                wp_remote_post($config['url'], [
                    'body' => [
                        'api_key' => get_option('wpso_otp_nexmo_key'),
                        'api_secret' => get_option('wpso_otp_nexmo_secret'),
                        'from' => get_option('wpso_otp_nexmo_from'),
                        'to' => $phone,
                        'text' => $message
                    ],
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
                ]);
                break;

            case 'messagebird':
                wp_remote_post($config['url'], [
                    'body' => [
                        'recipients' => $phone,
                        'originator' => get_option('wpso_otp_messagebird_from'),
                        'body' => $message
                    ],
                    'headers' => [
                        'Authorization' => 'AccessKey ' . get_option('wpso_otp_messagebird_key'),
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ]
                ]);
                break;
        }
    }

    public function get_available_vendors(): array {
        return array_keys($this->vendors);
    }
}