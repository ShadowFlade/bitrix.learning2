<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$asset = \Bitrix\Main\Page\Asset::getInstance();
$asset->addJs(SITE_TEMPLATE_PATH . '/js/Modal/Modal.js');
$asset->addCss(SITE_TEMPLATE_PATH . '/css/modals/geolocation.css');

?>
<div class="geolocation">
    <div class="geolocation__wrapper">
        <a href="" class="geolocation__cur-city js-geolocation__cur-city js-modal-trigger" data-show-modal="<?=empty($arResult['CURRENT_CITY']) ? 'yes' : 'no'?>">
            <?= $arResult['CURRENT_CITY'] ?>
        </a>
        <?
        $suggCity = $arResult['SUGGESTED_CITY'];
        $APPLICATION->IncludeFile(
            SITE_DIR . '/include/modals/geolocation.php',
            [
                'SUGGESTED_CITY' => $arResult['SUGGESTED_CITY'],
                'CITIES_LIST' => $arResult['CITIES_LIST'],
                'SET_CITY_PARAM' => $arResult['SET_CITY_PARAM'],
                'CURRENT_CITY' => $arResult['CURRENT_CITY'],
            ]
        );
        ?>
    </div>
</div>