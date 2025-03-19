<?php
declare(strict_types=1);

class WPSOSmtpVendors {
    private static ?self $instance = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        if (get_option('wpso_smtp_enabled')) {
            add_action('phpmailer_init', [$this, 'configure_smtp']);
        }
    }

    public function configure_smtp(PHPMailer\PHPMailer\PHPMailer $phpmailer): void {
        $vendor = get_option('wpso_smtp_vendor', 'brevo');
        $phpmailer->isSMTP();
        $phpmailer->SMTPAuth = true;

        switch ($vendor) {
            case 'brevo':
                $phpmailer->Host = get_option('wpso_smtp_brevo_host', 'smtp-relay.brevo.com');
                $phpmailer->Port = get_option('wpso_smtp_brevo_port', 587);
                $phpmailer->Username = 'user@example.com'; // Replace with actual sender email
                $phpmailer->Password = get_option('wpso_smtp_brevo_key');
                $phpmailer->SMTPSecure = 'tls';
                break;
            case 'mailgun':
                $phpmailer->Host = 'smtp.mailgun.org';
                $phpmailer->Port = 587;
                $phpmailer->Username = 'postmaster@' . get_option('wpso_smtp_mailgun_domain');
                $phpmailer->Password = get_option('wpso_smtp_mailgun_key');
                $phpmailer->SMTPSecure = 'tls';
                break;
            case 'sendgrid':
                $phpmailer->Host = 'smtp.sendgrid.net';
                $phpmailer->Port = 587;
                $phpmailer->Username = 'apikey';
                $phpmailer->Password = get_option('wpso_smtp_sendgrid_key');
                $phpmailer->SMTPSecure = 'tls';
                break;
            case 'amazon':
                $phpmailer->Host = "email-smtp." . get_option('wpso_smtp_amazon_region', 'us-east-1') . ".amazonaws.com";
                $phpmailer->Port = 587;
                $phpmailer->Username = get_option('wpso_smtp_amazon_access_key');
                $phpmailer->Password = get_option('wpso_smtp_amazon_secret_key');
                $phpmailer->SMTPSecure = 'tls';
                break;
            case 'postmark':
                $phpmailer->Host = 'smtp.postmarkapp.com';
                $phpmailer->Port = 587;
                $phpmailer->Username = get_option('wpso_smtp_postmark_token');
                $phpmailer->Password = get_option('wpso_smtp_postmark_token');
                $phpmailer->SMTPSecure = 'tls';
                break;
        }
    }

    public function get_available_vendors(): array {
        return ['brevo', 'mailgun', 'sendgrid', 'amazon', 'postmark'];
    }
}