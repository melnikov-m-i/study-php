<?php

class DataCache
{
    const FILE_CACHE = './cache/cacheCity.txt';
    const EXTERNAL_SERVICE_CITY = 'http://exercise.develop.maximaster.ru/service/city/';
    const TIME_MEMCACHE = 24*60*60;

    private static $instance = null;
    private $cache = [];

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __clone() {}

    private function __wakeup() {}

    private function __construct()
    {
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211);
        $data = $memcache->get('listCities');

        if ($data) {
            $this->cache = $data;
        } else {
            if (file_exists(self::FILE_CACHE)) {
                $dateLastModifyFileCache = filemtime(self::FILE_CACHE);
                if ($dateLastModifyFileCache && $dateLastModifyFileCache < mktime(0,0,0)) {
                    $listCities = $this->downloadListOfCitiesFromAnExternalService(self::EXTERNAL_SERVICE_CITY);
                } else {
                    $listCities = file_get_contents(self::FILE_CACHE);
                    if ($listCities === false) {
                        $listCities = [];
                    } else {
                        $listCities = json_decode($listCities);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $listCities = [];
                        }
                    }
                }
            } else {
                $listCities = $this->downloadListOfCitiesFromAnExternalService(self::EXTERNAL_SERVICE_CITY);
            }

            $memcache->set("listCities", $listCities, false, self::TIME_MEMCACHE);
            $this->cache = $listCities;
        }
    }

    private function downloadListOfCitiesFromAnExternalService($service)
    {
        $listCities = file_get_contents($service);
        if ($listCities === false) {
            return [];
        } else {
            $result = json_decode($listCities);

            if (json_last_error() === JSON_ERROR_NONE) {
                file_put_contents(self::FILE_CACHE, $listCities);
                return $result;
            } else {
                return [];
            }
        }
    }

    public function getData()
    {
        return $this->cache;
    }
}


