<?php
/**
 * send_email: Helper function to send an email.
 */

// configuration
require_once __DIR__ . '/../config/smtp.php';
if(!isset($smtp)) exit();
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Send an email.
 * @param string $recipient_email
 * @param string $subject
 * @param string $body
 * @return array
 * @throws \PHPMailer\PHPMailer\Exception
 */
function send_email($recipient_email, $subject, $body)
{
    // prepare the response
    $response = [
        'success' => false,
        'error'   => ''
    ];

    // set up the email
    global $smtp;
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug   = $smtp['debug'];
    $mail->SMTPOptions = $smtp['options'];
    $mail->Host        = $smtp['host'];
    $mail->SMTPAuth    = $smtp['auth'];
    $mail->Username    = $smtp['username'];
    $mail->Password    = $smtp['password'];
    $mail->Port        = $smtp['port'];
    $mail->From        = $smtp['from']['email'];
    $mail->FromName    = $smtp['from']['name'];
    $mail->addAddress($recipient_email);
    $mail->isHTML(true);
    $mail->Subject    = $subject;
    $mail->Body       = $body;
    $mail->AltBody    = strip_tags($body);

    // send the email
    if($mail->send()) {
        $response['success'] = true;
    }
    else {
        $response['error'] = $mail->ErrorInfo;
    }

    // return the response
    return $response;
}