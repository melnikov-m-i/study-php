<?php

const EXTERNAL_SERVICE = "http://exercise.develop.maximaster.ru/service/delivery/";

$errors = [];
$html = "";

if(array_key_exists('city', $_POST)) {
    $city = trim($_POST['city']);
    $city = stripslashes($city);
    $city = htmlentities($city);

    if(mb_strlen($city) == 0) {
        $errors[] = "Выберите город для доставки(он не может быть пустым)";
    }
} else {
    $errors[] = "Не передан город для доставки";
}

if(array_key_exists('weight', $_POST)) {
    $weight = trim($_POST['weight']);
    $weight = stripslashes($weight);
    $weight = htmlentities($weight);

    if(!is_numeric($weight) || !is_int(+$weight) || $weight < 0) {
        $errors[] = "Вес груза должен быть положительным целочисленным числом в килограммах";
    }
} else {
    $errors[] = "Не передан вес груза";
}

if(empty($errors)) {
    $queryArray = array("city"=>$city,"weight"=>$weight);
    $queryString .= http_build_query($queryArray,'','&');
    $url = EXTERNAL_SERVICE."?".$queryString;

    /* $delivery = file_get_contents($url); */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $delivery = curl_exec($ch);
    curl_close($ch);

    if($delivery === false) {
        $errors[] = 'Произошла ошибка при загрузке результата расчета доставки из внешнего сервиса';
        //die('Произошла ошибка при загрузке результата расчета доставки из внешнего сервиса');
    } else {
        $delivery = json_decode($delivery, true);

        if(json_last_error() === JSON_ERROR_NONE) {
            if($delivery['status'] == "OK") {
                $html = '<div class="success-calculate"><p>'.$delivery['message'].'</p></div>';
            } else {
                $html = '<div class="error-calculate"><p>'.$delivery['message'].'</p></div>';
            }
        } else {
            $errors[] = 'Результата расчета доставки передан в формате отличном от JSON';
            //die('Результата расчета доставки передан в формате отличном от JSON');
        }
    }
}

if(!empty($errors)) {
    $html = '<div class="error-calculate">';
    foreach ($errors as $error) {
        $html .= '<p>'.$error.'</p>';
    }
    $html .= '</div>';
}

echo $html;