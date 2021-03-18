<?php

namespace App\Http\Controllers;

use App\Exports\RecordsExport;
use App\Record;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailController extends Controller
{
    public function index(){
        $mail = new PHPMailer;

        $mail->isSMTP();

        $mail->CharSet = "utf-8";
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'nialcomBot@gmail.com';
        $mail->Password = 'POLnikitosik23';
        $mail->Port = 587;
        $mail->setFrom('nialcomBot@gmail.com', 'nialcomBot');

        $mail->addAddress('roylex16@gmail.com', 'John Doe');

        $mail->Subject = 'Табель рабочего времени за '.date('d.m.Y');
        $mail->Body     = 'Табель рабочего времени за '.date('d.m.Y');
        $mail->AltBody = 'This is a plain-text message body';
        $url = 'http://nialmanage/records/download?year='.date('Y').'&month='.date('m');
        $mail->addStringAttachment(file_get_contents($url), 'Табель.xlsx');

        if (!$mail->send()) {
            echo 'Mailer Error: '. $mail->ErrorInfo;
        } else {
            echo 'Message sent!';

            #if ($this->save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }
    }

    function save_mail($mail)
    {

        $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';
        $imapStream = imap_open($path, $mail->Username, $mail->Password);
        $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
        imap_close($imapStream);
        return $result;
    }
}
