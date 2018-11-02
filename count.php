<?php
include 'db.php';

@mysqli_query($db, 'set character_set_result = "utf8"');

$visitorIP = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');

$result = mysqli_query($db, "SELECT `id_visit` FROM `visits` WHERE `date` ='$date'");

if(mysqli_num_rows($result) == 0) {
    mysqli_query($db, "DELETE FROM `ips`");
    mysqli_query($db, "INSERT INTO `ips` SET `ip_address` = '$visitorIP'");
    mysqli_query($db, "INSERT INTO `visits` SET `date` = '$date', `hosts` = 1, `views` = 1");
} else {
    $currentIP = mysqli_query($db, "SELECT `id_ip` FROM `ips` WHERE `ip_address` = '$visitorIP'");

    if(mysqli_num_rows($currentIP) == 1) {
        mysqli_query($db, "UPDATE `visits` SET `views` = `views` + 1 WHERE `date` = '$date'");
    } else {
        mysqli_query($db, "INSERT INTO `ips` SET `ip_address` = '$visitorIP'");
        mysqli_query($db, "UPDATE `visits` SET `hosts` = `hosts` + 1,`views` = `views` + 1 WHERE `date` = '$date'");
    }
} 