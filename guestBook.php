<?php
    header ("Content-Type: text/html; charset=utf-8");

    const LONG_NAME = 50;
    const REPLACING_EMPTY_NAME = 'Анонимно';
    const LONG_MESSAGE = 500;

    include_once 'db.php';
    @mysqli_query($db, 'set character_set_results = "utf8"');

    if (!empty($_POST)) {
        $errorMessage = [];
        $data = [];

        if (array_key_exists('name', $_POST)) {
            $name = trim($_POST['name']);
            $name = stripslashes($name);
            $name = htmlentities($name);

            $data['name'] = $name == '' ? REPLACING_EMPTY_NAME : mb_substr($name, 0, LONG_NAME);
        }

        if (array_key_exists('message', $_POST)) {
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
        } else {
            $errorMessage[] = 'Не передано поле "message"';
        }

        $data['dateTime'] = (new DateTime())->format('Y-m-d H:i:s');

        if (empty($errorMessage)) {
            mysqli_query($db, "INSERT INTO `guestBook` SET `dateTime` = '".$data['dateTime'].
                "', `name` = '".$data['name']."', `message` = '".$data['message']."'") or die('При записи сообщения в БД произошла ошибка <br>'.mysqli_error($db));

            header("Location: ".$_SERVER['REQUEST_URI']);
        }
    }

    $result = mysqli_query($db, "SELECT `dateTime`, `name`, `message` FROM `guestBook`");
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title>Гостевая книга</title>
        <link rel="stylesheet" type="text/css" href="./css/styleGuestBook.css">
    </head>
    <body>
        <h1>Гостевая книга</h1>
        <div class="container">
            <div class="list-messages">

                <?php foreach ($messages as $msg): ?>
                    <div class="message">
                        <p class="msg-name-guest"><?=$msg['name']; ?></p>
                        <p class="msg-date-time"><?=(new DateTime($msg['dateTime']))->format('d.m.Y H:i'); ?></p>
                        <p class="msg-body"><?=$msg['message']; ?></p>
                    </div>
                <?php endforeach; ?>

            </div>
            <div class="form-send-message">
                <form name="formToSendMessage" method="post" action="./guestBook.php">
                    <input type="text" name="name" placeholder="Ваше имя" maxlength="50">
                    <textarea name="message" placeholder="Ваше сообщение" maxlength="500" cols="50" rows="4" required></textarea>
                    <input type="submit" value="Отправить">
                </form>
            </div>

            <?php if (!empty($errorMessage)): ?>

                <div class="error-send-message">
                    <h2>Произошла ошибка при отправке сообщения!</h2>
                    <br>

                    <?php for ($i = 0; $i < count($errorMessage); $i++): ?>

                        <p>
                            <?=$errorMessage[$i]?>
                        </p>

                    <?php endfor; ?>

                </div>

            <?php endif; ?>

        </div>

    </body>
</html>
