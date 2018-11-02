<?php
    header ("Content-Type: text/html; charset=utf-8");
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
                <?php
                    include_once 'db.php';

                    @mysqli_query($db, 'set character_set_results = "utf8"');

                    $result = mysqli_query($db, "SELECT `dateTime`, `name`, `message` FROM `guestBook`");

                    if(mysqli_num_rows($result) != 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="message">'.
                                '<p class="msg-date-time">'.(new DateTime($row['dateTime']))->format('d.m.Y H:i').'</p>'.
                                '<p class="msg-name-guest">'.$row['name'].'</p>'.
                                '<p class="msg-body">'.$row['message'].'</p>'.
                            '</div>';
                        }
                    }
                ?>
            </div>
            <div class="form-send-message">
                <form name="formToSendMessage" method="post" action="./sendMessage.php">
                    <input type="text" name="name" placeholder="Ваше имя" maxlength="50">
                    <textarea name="message" placeholder="Ваше сообщение" maxlength="500" cols="50" rows="4" required></textarea>
                    <input type="submit" value="Отправить">
                </form>
            </div>
        </div>

    </body>
</html>
