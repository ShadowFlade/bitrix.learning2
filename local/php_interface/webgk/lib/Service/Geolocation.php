<?php

namespace Webgk\Service;

use \Bitrix\Main\Service\GeoIp;

class Geolocation
{
    public static function getCityName()
    {
        $ipAddress = GeoIp\Manager::getRealIp();
        $result = GeoIp\Manager::getDataResult($ipAddress)->getGeoData();

        return $result->cityName;
    }

}