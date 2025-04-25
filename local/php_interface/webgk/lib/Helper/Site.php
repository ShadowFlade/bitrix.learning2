<?php

namespace Webgk\Helper;

class Site
{
    public static function handleGetParams()
    {

        LocalRedirect($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    public static function handelSetCity()
    {
        if (!isset($_GET['set_city']) || empty($_GET['set_city'])) {
            return;
        }
        $city = $_GET['set_city'];

        $_SESSION['WEBGK']['GEO_CITY'] = $city;
        $dataclass = (new \Webgk\Helper\HighloadBlock('geolocation_cities'))->getEntityDataClass();
        $count = $dataclass::getList(['filter' => ['UF_NAME' => $city]])->getSelectedRowsCount();
        if ($count == 0) {
            $dataclass::Add(['UF_NAME' => $city]);
        }

        Header('Location:' . 'http://' . $_SERVER['HTTP_HOST'] . str_replace("set_city=$city", '', $_SERVER['REQUEST_URI']));
    }
}