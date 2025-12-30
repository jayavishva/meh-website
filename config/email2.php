<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/*
|--------------------------------------------------------------------------
| LOAD COMPOSER AUTOLOADER (MANDATORY)
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| SMTP CONFIGURATION
|--------------------------------------------------------------------------
*/
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');     // 🔁 change
define('SMTP_PASSWORD', 'your-app-password');        // 🔁 Gmail App Password
define('SMTP_ENCRYPTION', 'tls');

define('SMTP_FROM_EMAIL', 'noreply@mehedishop.com');
define('SMTP_FROM_NAME', 'Mehedi Shop');

/*
|--------------------------------------------------------------------------
| SEND EMAIL (MAIN FUNCTION)
|--------------------------------------------------------------------------
*/
function sendEmail($to, $subject, $message, $isHTML = true)
{
    return sendEmailWithPHPMailer($to, $subject, $message, $isHTML);
}

/*
|--------------------------------------------------------------------------
| PHPMailer FUNCTION (ONLY METHOD – NO mail())
|--------------------------------------------------------------------------
*/
function sendEmailWithPHPMailer($to, $subject, $message, $isHTML = true)
{
    try {
        $mail = new PHPMailer(true);

        // SMTP SETTINGS
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;

        $mail->CharSet = 'UTF-8';

        // FROM & TO
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // CONTENT
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();

        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];

    } catch (Exception $e) {
        error_log($mail->ErrorInfo);
        return [
            'success' => false,
            'message' => $mail->ErrorInfo
        ];
    }
}

/*
|--------------------------------------------------------------------------
| SEND OTP EMAIL
|--------------------------------------------------------------------------
*/
function sendOTPEmail($to, $otp_code, $expiry_minutes = 15)
{
    $subject = 'Password Reset OTP - Mehedi Shop';

    $message = "
    <html>
    <body style='font-family:Arial; background:#f4f4f4; padding:20px'>
        <div style='max-width:600px; margin:auto; background:#ffffff; padding:20px'>
            <h2 style='color:#3399cc'>Mehedi Shop</h2>
            <p>Your OTP code:</p>
            <h1 style='letter-spacing:6px'>{$otp_code}</h1>
            <p>This OTP will expire in <b>{$expiry_minutes} minutes</b>.</p>
            <p style='color:red'>Do not share this OTP with anyone.</p>
            <br>
            <p>Regards,<br>Mehedi Shop Team</p>
        </div>
    </body>
    </html>";

    return sendEmail($to, $subject, $message, true);
}
