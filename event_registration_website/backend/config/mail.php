<?php 
 require '../vendor/autoload.php';

 use PHPMailer\PHPMailer\PHPMailer;



 function getmailer(): PHPMailer
    {
        $semail='kadenbreak2@gmail.com';
        $sep='syildgftdobfcwci';
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $semail;
        $mail->Password = $sep;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($semail,'Event Team');
 

        return $mail;
    }



?>