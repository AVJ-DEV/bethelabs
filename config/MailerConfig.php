<?php
/**
 * MailerConfig - PHPMailer wrapper for sending emails via SMTP or PHP mail()
 * Configuration can be set via environment variables or hardcoded values
 */

class MailerConfig {
    protected static $instance = null;
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $fromEmail;
    protected $fromName;
    protected $useSMTP;

    private function __construct() {
        // Read configuration from environment or set defaults
        $this->useSMTP = getenv('MAIL_USE_SMTP') !== false ? filter_var(getenv('MAIL_USE_SMTP'), FILTER_VALIDATE_BOOLEAN) : true;
        $this->host = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        $this->port = getenv('MAIL_PORT') ?: 587;
        $this->username = getenv('MAIL_USERNAME') ?: '';
        $this->password = getenv('MAIL_PASSWORD') ?: '';
        $this->fromEmail = getenv('MAIL_FROM_EMAIL') ?: 'noreply@bethelabs.local';
        $this->fromName = getenv('MAIL_FROM_NAME') ?: 'BETHEL LABS';
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Send an email via PHPMailer (SMTP) or PHP mail()
     * @return bool Success status
     */
    public function send($to, $subject, $message, $isHtml = false) {
        if (!$this->useSMTP || empty($this->username) || empty($this->password)) {
            // Fallback to PHP mail()
            return $this->sendViaMail($to, $subject, $message);
        }

        // Use PHPMailer with SMTP
        return $this->sendViaPhpMailer($to, $subject, $message, $isHtml);
    }

    protected function sendViaPhpMailer($to, $subject, $message, $isHtml) {
        try {
            // Check if PHPMailer is available (via composer or manual include)
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                // Try to load from vendor if composer was used
                $autoload = __DIR__ . '/../vendor/autoload.php';
                if (file_exists($autoload)) {
                    require_once $autoload;
                } else {
                    // PHPMailer not available, fall back to mail()
                    return $this->sendViaMail($to, $subject, $message);
                }
            }

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->Port = $this->port;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->port === 465 ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML($isHtml);

            return $mail->send();
        } catch (\Exception $e) {
            // Log error and fall back to mail()
            error_log('PHPMailer Error: ' . $e->getMessage());
            return $this->sendViaMail($to, $subject, $message);
        }
    }

    protected function sendViaMail($to, $subject, $message) {
        $headers = 'From: ' . $this->fromEmail . ' <' . $this->fromName . '>' . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
        return mail($to, $subject, $message, $headers);
    }

    public function getFromEmail() {
        return $this->fromEmail;
    }

    public function getFromName() {
        return $this->fromName;
    }
}
?>
