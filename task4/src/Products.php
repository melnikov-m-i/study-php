<?php

class Products
{
    private static $fields = ['name', 'description', 'price'];

    public static function selfFields()
    {
        return self::$fields;
    }
}