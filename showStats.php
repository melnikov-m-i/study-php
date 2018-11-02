<?php

$result = mysqli_query($db, "SELECT `hosts`, `views` FROM `visits` WHERE `date` = '$date'");
$row = mysqli_fetch_assoc($result);

$now = new DateTime();

echo '<p>Уникальных посетителей: '.$row['hosts'].'<br>';
echo 'Просмотров: '.$row['views'].'<br>';
echo 'Текущее время: '.$now->format("H:i").'</p>';