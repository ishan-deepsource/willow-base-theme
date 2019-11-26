<?php

namespace Bonnier\Willow\Base\Notifications;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function send($to, $subject, $body)
    {
        $from = env('SES_FROM');
        $name = env('SES_NAME');
        $username = env('SES_USERNAME');
        $password = env('SES_PASSWORD');
        $host = env('SES_HOST');
        $port = env('SES_PORT');
        if (!$from || !$name || !$username || !$password || !$host || !$port) {
            return false;
        }
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->setFrom($from, $name);
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->Host = $host;
            $mail->Port = $port;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->Send();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
