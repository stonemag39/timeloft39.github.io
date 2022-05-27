<?php
    $msg_box = ""; 
    $errors = array(); 

    // если форма без ошибок
    if(empty($errors)){     
        // собираем данные из формы
        $message = "Имя: " . $_POST['user_name'] . "<br/> Номер телефона: " . $_POST['user_tel'];
        send_mail($message); // отправим письмо
    }
     
    // функция отправки письма
    function send_mail($message){
        // почта, на которую придет письмо
        $mail_to = "sinya.kat@gmail.com"; 
        
        // тема письма
        $subject = "Заявка на бесплатный замер";
         
        // заголовок письма
        $headers= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
        $headers .= "From: <sinya.kat@gmail.com>\r\n"; // от кого письмо
         
        // отправляем письмо 
        mail($mail_to, $subject, $message, $headers);
    }
     
?>

<?php

    $randomtoken = md5(uniqid(rand(), true));
    $_SESSION['csrfToken']=$randomtoken;

    // Файлы phpmailer
    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';
    require 'phpmailer/Exception.php';

    // Переменные, которые отправляет пользователь
    $name = $_POST['user_name'];
    $tel = $_POST['user_tel'];

    // Формирование письма
    $title = "Заявка с сайта | Timeloft";
    $body = "
    <h2>Новая заявка</h2>
    <b>Имя:</b> $name<br><br>
    <b>Телефон:</b> $tel<br><br>
    ";

    // Настройки PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();   
        $mail->CharSet = "UTF-8";
        $mail->SMTPAuth   = true;
        //$mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

        // Настройки почты
        $mail->Host       = 'smtp.mail.ru'; // SMTP сервера почты
        $mail->Username   = 'timeloft39@inbox.ru'; // Логин
        $mail->Password   = 'j2rjmKg6u2j35A13bnx9'; // Пароль для внешнего приложения почты
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->setFrom('timeloft39@inbox.ru', 'Изготовление мебели в стиле лофт'); // Адрес отправителя

        // Получатель письма
        $mail->addAddress('timeloft39@inbox.ru');

        // Отправка сообщения
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $body;    

        // Проверяем отравленность сообщения
        if ($mail->send()) {$result = "success";} 
        else {$result = "error";}

    } catch (Exception $e) {
        $result = "error";
        $status = "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
    }

    // Отображение результата
    echo json_encode(["result" => $result, "resultfile" => $rfile, "status" => $status]);
?>

<?php
    //ТЕЛЕГРАМ

    //Переменная $token из @botFather
    $token = "5345521062:AAHZSp_cX4wvcI6OQz_Ca4oYtS93LRIt9aA";

    $chat_id = "-546704130";

    //Определяем переменные 
        $name = ($_POST['user_name']);
        $phone = ($_POST['user_tel']);

    //Собираем в массив то, что будет передаваться боту
        $arr = array(
            'Имя:' => $name,
            'Телефон:' => $phone
        );

    //Настраиваем внешний вид сообщения в телеграме
        foreach($arr as $key => $value) {
            $txt .= "<b>".$key."</b> ".$value."%0A";
        };

    //Передаем данные боту
        $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$txt}","r");

?>

<?php
    //GOOGLE ТАБЛИЦЫ

    $url = "https://docs.google.com/forms/d/1RXxmteU_Y6p78u3EN6nX1-EFtnrMY09Z3p9wRF5JfBE/formResponse";

    // массив данных
    $post_data = array (
    "entry.1461396659" => $_POST['user_name'],
    "entry.330969374" => $_POST['user_tel'],
    "draftResponse" => "[,,&quot;-1141414548648688046&quot;]",
    "pageHistory" => "0",
    "fbzx" => "-1141414548648688046"
    );

    // с помощью CURL заносим данные в таблицу google
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // указываем POST запрос
    curl_setopt($ch, CURLOPT_POST, 1);
    // добавляем переменные
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    //заполняем таблицу google
    $output = curl_exec($ch);
    curl_close($ch);

?>