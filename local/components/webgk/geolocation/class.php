<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Webgk\Helpers\HighloadBlock;


class Geolocation extends \CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'get' => [
                'prefilters' => [],
            ],
            'getCitiesList' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getCityByIP()
    {
        $handle = curl_init();
        $ip = $_SERVER['REMOTE_ADDR'];
        curl_setopt($handle, CURLOPT_URL, "http://ipwho.is/$ip");
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        $resultJson = curl_exec($handle);
        $result = json_decode($resultJson, true);

        if (empty($result['city'])) {
            return ['RESULT' => '', 'SUCCESS' => false];
        } else {
            return ['RESULT' => $result['city'], 'SUCCESS' => true];
        }
    }

    public function getCitiesList()
    {
        $dataclass = (new \Webgk\Helper\HighloadBlock('geolocation_cities'))->getEntityDataClass();
        $availableCities = $dataclass::GetList()->fetchAll();
        return $availableCities;
    }


    public function getCity()
    {
        if (!empty($city = $this->getCityByIP())) {
            return $city;
        } else {
            return ['SUCCESS' => false];
        }
    }

    public function executeComponent()
    {
        \Bitrix\Main\Diag\Debug::writeToFile(
            $_SESSION['WEBGK']['GEO_CITY'],
            date("d.m.Y H:i:s"),
            'local/geo.log'
        );
        $this->arResult['CURRENT_CITY'] = $_SESSION['WEBGK']['GEO_CITY'] ?? '';


        if (empty($this->arResult['CURRENT_CITY'])) {
            $this->arResult['SUGGESTED_CITY'] = $this->getCity()['RESULT'];
        }
        $citiesList = $this->getCitiesList();
        $this->arResult['CITIES_LIST'] = $citiesList;
        $this->arResult['SET_CITY_PARAM'] = 'set_city';

        $this->includeComponentTemplate();
    }
}