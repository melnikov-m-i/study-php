<?php
header ("Content-Type: text/html; charset=utf-8");

const FILE_CACHE = './cache/cacheCity.txt';
const EXTERNAL_SERVICE = 'http://exercise.develop.maximaster.ru/service/city/';

if (file_exists(FILE_CACHE)) {
    $dateLastModifyFileCache = filemtime(FILE_CACHE);
    if ($dateLastModifyFileCache && $dateLastModifyFileCache < mktime(0,0,0)) {
        $listCities = downloadListOfCitiesFromAnExternalService(EXTERNAL_SERVICE);
    } else {
        $listCities = file_get_contents(FILE_CACHE);
        if ($listCities === false) {
            die('Произошла ошибка при загрузке списка городов из файла кэша');
        }
    }
} else {
    $listCities = downloadListOfCitiesFromAnExternalService(EXTERNAL_SERVICE);
}

$listCities = json_decode($listCities);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Список городов передан в формате отличном от JSON');
}

function downloadListOfCitiesFromAnExternalService($service) {
    $listCities = file_get_contents($service);
    if ($listCities === false) {
        die('Произошла ошибка при загрузке списка городов из внешнего сервиса');
    } else {
        $result = json_decode($listCities);

        if (json_last_error() === JSON_ERROR_NONE) {
            writeJsonListCitiesInFileCache($listCities);
            return $listCities;
        } else {
            die('Список городов передан в формате отличном от JSON');
        }
    }
}

function writeJsonListCitiesInFileCache($jsonListCities) {
    $countByte = file_put_contents(FILE_CACHE, $jsonListCities);

    if ($countByte === false) {
        die('При записи в файл списка городов произошла ошибка');
    }

    return $countByte;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title>Калькулятор доставки</title>
        <link rel="stylesheet" type="text/css" href="./css/styleDeliveryCalculator.css">
    </head>
    <body>
        <h1>Калькулятор доставки</h1>
        <div class="container">
            <div class="form-delivery-calculator">
                <form name="formDeliveryCalculator">
                    <select name="city">
                        <?php foreach ($listCities as $city): ?>
                            <option value="<?=$city; ?>" <?=$city == "Москва" ? 'selected' : ''; ?>><?=$city; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="weight" min="0" step="1" pattern="^\d+$" placeholder="Вес, кг">
                    <input type="submit" value="Рассчитать">
                </form>
                <div class="info-calculate"></div>
            </div>
        </div>
        <script type="application/javascript" src="./js/deliveryCalculator.js"></script>
    </body>
</html>

