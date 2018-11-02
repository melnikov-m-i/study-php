<?php
header ("Content-Type: text/html; charset=utf-8");

const LONG_NAME = 50;
const REPLACING_EMPTY_NAME = 'Анонимно';
const LONG_MESSAGE = 500;

$errorMessage = [];
$data = [];

if(array_key_exists('name', $_POST)) {
    $name = trim($_POST['name']);
    $name = stripslashes($name);
    $name = htmlentities($name);

    $data['name'] = $name != '' ? mb_substr($name, 0, LONG_NAME) : REPLACING_EMPTY_NAME;
}

if(array_key_exists('message', $_POST)) {
    $message = trim($_POST['message']);
    $message = stripslashes($message);
    $message = htmlentities($message);

    if($message == '') {
        $errorMessage[] = 'Сообщение не должно быть пустым';
    } elseif (mb_strlen($message) > LONG_MESSAGE) {
        $errorMessage[] = 'Сообщение не должно быть больше, чем '.LONG_MESSAGE.' символов';
    } else {
        $data['message'] = $message;
    }
}
else {
    $errorMessage[] = 'Не передано поле "message"';
}

$data['dateTime'] = (new DateTime())->format('Y-m-d H:i:s');

if(empty($errorMessage)) {
    include_once 'db.php';

    @mysqli_query($db, 'set character_set_results = "utf8"');
    mysqli_query($db, "INSERT INTO `guestBook` SET `dateTime` = '".$data['dateTime'].
        "', `name` = '".$data['name']."', `message` = '".$data['message']."'") or die('При записи сообщения в БД произошла ошибка <br>'.mysqli_error($db));
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title>Отправленное сообщение</title>
        <link rel="stylesheet" type="text/css" href="./css/styleGuestBook.css">
    </head>
    <body>
        <div class="container">
            <div class="success-send-message"></div>
            <?php
                if(empty($errorMessage)) {
                    echo '<div class="success-send-message">'.
                            '<h2>Сообщение успешно отправлено</h2><br>'.
                            '<p><a href="./guestBook.php">Перейти к гостевой книге</a></p>'.
                        '</div>';
                } else {
                    echo '<div class="error-send-message">'.
                        '<h2>Произошла ошибка при отправке сообщения!</h2><br>';
                    for($i = 0; $i < count($errorMessage); $i++) {
                        echo '<p>'.$errorMessage[$i].'</p>';
                    }
                    echo '</div>';
                }
            ?>
        </div>
    </body>
</html>


