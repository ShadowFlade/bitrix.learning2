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

    public function getCitiesList()
    {
        $dataclass = (new \Webgk\Helper\HighloadBlock('geolocation_cities'))->getEntityDataClass();
        $availableCities = $dataclass::GetList()->fetchAll();
        return $availableCities;
    }


    public function getCity()
    {
        if (!empty($city = \Webgk\Service\Geolocation::getCityName())) {
            return ['RESULT' => $city];
        } else {
            return ['SUCCESS' => false];
        }
    }

    public function executeComponent()
    {
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