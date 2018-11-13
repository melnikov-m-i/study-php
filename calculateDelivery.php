<?php
    require_once('./DataCache.php');

    header ("Content-Type: application/json");

    const EXTERNAL_SERVICE_DELIVERY = "http://exercise.develop.maximaster.ru/service/delivery/";

    $city = "";
    $weight = "";

    if (array_key_exists('city', $_POST)) {
        $city = trim($_POST['city']);
        $city = stripslashes($city);
        $city = htmlentities($city);

        if (mb_strlen($city) == 0) {
            sendJsonErrorMessage("Выберите город для доставки(он не может быть пустым)");
        } else {
            $listCities = DataCache::getInstance()->getData();

            if (!in_array($city, $listCities)) {
                sendJsonErrorMessage("Данного города нет в списке поддерживаемых, выберите другой");
            }
        }
    } else {
        sendJsonErrorMessage("Не передан город для доставки");
    }

    if (array_key_exists('weight', $_POST)) {
        $weight = trim($_POST['weight']);
        $weight = stripslashes($weight);
        $weight = htmlentities($weight);

        if (!is_numeric($weight) || floor($weight) != $weight  || $weight < 0) {
            sendJsonErrorMessage("Вес груза должен быть положительным целочисленным числом в килограммах");
        }
    } else {
        sendJsonErrorMessage("Не передан вес груза");
    }

    $queryArray = array("city" => $city, "weight" => $weight);
    $queryString .= http_build_query($queryArray,'','&');
    $url = EXTERNAL_SERVICE_DELIVERY."?".$queryString;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $delivery = curl_exec($ch);
    curl_close($ch);

    if ($delivery === false) {
        sendJsonErrorMessage("Произошла ошибка при загрузке результата расчета доставки из внешнего сервиса");
    } else {
        echo $delivery;
    }

    function sendJsonErrorMessage($errors) {
        echo json_encode(["status" => "error", "message" => $errors]);
        die();
    }
