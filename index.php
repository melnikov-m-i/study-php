<?php
    include 'count.php';
    header ("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title>Счётчик посещений</title>
    </head>
    <body>
        <h1>Счётчик посещений</h1>
        <?php include 'showStats.php' ?>
    </body>
</html>

