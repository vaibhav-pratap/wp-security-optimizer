<?php
declare(strict_types=1);

class WPSOOTPVendors {
    public array $vendors = [
        'twilio' => ['url' => 'https://api.twilio.com/2010-04-01/Accounts/{sid}/Messages.json', 'auth' => 'basic', 'fields' => ['sid', 'token', 'from']],
        'nexmo' => ['url' => 'https://rest.nexmo.com/sms/json', 'auth' => 'query', 'fields' => ['key', 'secret', 'from']],
        'messagebird' => ['url' => 'https://rest.messagebird.com/messages', 'auth' => 'header', 'fields' => ['key', 'from']],
        'plivo' => ['url' => 'https://api.plivo.com/v1/Account/{sid}/Message/', 'auth' => 'basic', 'fields' => ['sid', 'token', 'from']],
        'clickatell' => ['url' => 'https://platform.clickatell.com/messages', 'auth' => 'header', 'fields' => ['key', 'from']],
        'textmagic' => ['url' => 'https://rest.textmagic.com/api/v2/messages', 'auth' => 'basic', 'fields' => ['key', 'from']],
        'smsglobal' => ['url' => 'https://api.smsglobal.com/v1/sms/', 'auth' => 'basic', 'fields' => ['key', 'secret', 'from']],
        'bulkvs' => ['url' => 'https://portal.bulkvs.com/api/v1/sms', 'auth' => 'basic', 'fields' => ['key', 'secret', 'from']],
        'telesign' => ['url' => 'https://rest-api.telesign.com/v1/messaging', 'auth' => 'basic', 'fields' => ['key', 'secret', 'from']],
        'bandwidth' => ['url' => 'https://messaging.bandwidth.com/api/v2/users/{key}/messages', 'auth' => 'basic', 'fields' => ['key', 'secret', 'from']]
    ];

    public function send_phone_otp(string $phone, string $otp): void {
        $vendor = get_option('wpso_otp_vendor', 'twilio');
        if (!isset($this->vendors[$vendor])) return;
        $config = $this->vendors[$vendor];
        $message = sprintf(__('Your OTP is: %s. Expires in 5 min.', 'wp-security-optimizer'), $otp);

        switch ($vendor) {
            case 'twilio':
                $sid = get_option('wpso_otp_twilio_sid');
                wp_remote_post(str_replace('{sid}', $sid, $config['url']), [
                    'body' => ['From' => get_option('wpso_otp_twilio_from'), 'To' => $phone, 'Body' => $message],
                    'headers' => ['Authorization' => 'Basic ' . base64_encode("{$sid}:" . get_option('wpso_otp_twilio_token')), 'Content-Type' => 'application/x-www-form-urlencoded']
                ]);
                break;
            case 'nexmo':
                wp_remote_post($config['url'], [
                    'body' => ['api_key' => get_option('wpso_otp_nexmo_key'), 'api_secret' => get_option('wpso_otp_nexmo_secret'), 'from' => get_option('wpso_otp_nexmo_from'), 'to' => $phone, 'text' => $message],
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
                ]);
                break;
            case 'messagebird':
                wp_remote_post($config['url'], [
                    'body' => ['recipients' => $phone, 'originator' => get_option('wpso_otp_messagebird_from'), 'body' => $message],
                    'headers' => ['Authorization' => 'AccessKey ' . get_option('wpso_otp_messagebird_key'), 'Content-Type' => 'application/x-www-form-urlencoded']
                ]);
                break;
            case 'plivo':
                $sid = get_option('wpso_otp_plivo_sid');
                wp_remote_post(str_replace('{sid}', $sid, $config['url']), [
                    'body' => ['src' => get_option('wpso_otp_plivo_from'), 'dst' => $phone, 'text' => $message],
                    'headers' => ['Authorization' => 'Basic ' . base64_encode("{$sid}:" . get_option('wpso_otp_plivo_token')), 'Content-Type' => 'application/json']
                ]);
                break;
            case 'clickatell':
                wp_remote_post($config['url'], [
                    'body' => json_encode(['content' => $message, 'to' => [$phone], 'from' => get_option('wpso_otp_clickatell_from')]),
                    'headers' => ['Authorization' => get_option('wpso_otp_clickatell_key'), 'Content-Type' => 'application/json']
                ]);
                break;
            case 'textmagic':
                wp_remote_post($config['url'], [
                    'body' => ['text' => $message, 'phones' => $phone, 'from' => get_option('wpso_otp_textmagic_from')],
                    'headers' => ['X-TM-Username' => 'user', 'X-TM-Key' => get_option('wpso_otp_textmagic_key'), 'Content-Type' => 'application/x-www-form-urlencoded']
                ]);
                break;
            case 'smsglobal':
                wp_remote_post($config['url'], [
                    'body' => ['destination' => $phone, 'message' => $message, 'origin' => get_option('wpso_otp_smsglobal_from')],
                    'headers' => ['Authorization' => 'Basic ' . base64_encode(get_option('wpso_otp_smsglobal_key') . ':' . get_option('wpso_otp_smsglobal_secret')), 'Content-Type' => 'application/json']
                ]);
                break;
            case 'bulkvs':
                wp_remote_post($config['url'], [
                    'body' => ['from' => get_option('wpso_otp_bulkvs_from'), 'to' => $phone, 'message' => $message],
                    'headers' => ['Authorization' => 'Basic ' . base64_encode(get_option('wpso_otp_bulkvs_key') . ':' . get_option('wpso_otp_bulkvs_secret')), 'Content-Type' => 'application/json']
                ]);
                break;
            case 'telesign':
                wp_remote_post($config['url'], [
                    'body' => ['phone_number' => $phone, 'message' => $message, 'message_type' => 'OTP'],
                    'headers' => ['Authorization' => 'Basic ' . base64_encode(get_option('wpso_otp_telesign_key') . ':' . get_option('wpso_otp_telesign_secret')), 'Content-Type' => 'application/x-www-form-urlencoded']
                ]);
                break;
            case 'bandwidth':
                $key = get_option('wpso_otp_bandwidth_key');
                wp_remote_post(str_replace('{key}', $key, $config['url']), [
                    'body' => json_encode(['from' => get_option('wpso_otp_bandwidth_from'), 'to' => $phone, 'text' => $message]),
                    'headers' => ['Authorization' => 'Basic ' . base64_encode("{$key}:" . get_option('wpso_otp_bandwidth_secret')), 'Content-Type' => 'application/json']
                ]);
                break;
        }
    }

    public function get_available_vendors(): array {
        return array_keys($this->vendors);
    }
}