<?php
require_once('./DataCache.php');

header ("Content-Type: text/html; charset=utf-8");

$listCities = DataCache::getInstance()->getData();

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

