<?php
// Файлы phpmailer
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';


function sendMail($rName, $rEmail, $rTitle, $rBody, $rFile) {
    $name = $rName;
    $email = $rEmail;
    $title = $rTitle;
    $text = $rBody;
    $file = $rFile;

    // макет письма
    $body = "
        <h2>Header</h2>
        <b>To:</b> $name<br>
        <b>Mail:</b> $email<br><br>
        <b>Message:</b><br>$text
        ";

    // Настройки PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPAuth = true;
        //$mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) {
            $GLOBALS['status'][] = $str;
        };

        // Настройки почты
        $mail->Host = 'smtp.gmail.com'; // SMTP сервера вашей почты
        $mail->Username = 'e.u.zubkov1@gmail.com'; // Логин на почте
        $mail->Password = 'okbyepnqnbpiewti'; // Пароль на почте
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('tutoria@tutoria.com', 'Tutoria'); // Адрес самой почты и имя отправителя

        // Получатель письма
        $mail->addAddress($email);
        //$mail->addAddress($email); // Ещё один, если нужен

        // Прикрипление файлов к письму
        if (!empty($file['name'][0])) {
            for ($ct = 0; $ct < count($file['tmp_name']); $ct++) {
                $uploadfile = tempnam(sys_get_temp_dir(), sha1($file['name'][$ct]));
                $filename = $file['name'][$ct];
                if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
                    $mail->addAttachment($uploadfile, $filename);
                    $rfile[] = "The file $filename is attached";
                } else {
                    $rfile[] = "Failed to attach file $filename";
                }
            }
        }
        // Отправка сообщения
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $body;

        // Проверяем отравленность сообщения
        if ($mail->send()) {
            $result = "success";
            $status = "Message sent successfully";
        } else {
            $result = "error";
            $status = "Error sending message";
        }

    } catch (Exception $e) {
        $result = "error";
        $status = "The message was not sent. Cause of error: {$mail->ErrorInfo}";
    }

    // Отображение результата
    echo json_encode(["result" => $result, "resultfile" => $rfile, "status" => $status]);
}

// example
sendMail("Evgeniy","zubkoveu@mail.ru", "Message for you", "Hi, this is my message", $_FILES['myfile']);

