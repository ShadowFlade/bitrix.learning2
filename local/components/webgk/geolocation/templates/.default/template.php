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
$asset->addJs('/local/templates/.default/js/Modal/Modal.js');

?>
<div class="geolocation">
    <div class="geolocation__wrapper">
        <p class="geolocation__cur-city"><?= $arResult['CURRENT_CITY'] ?></p>
        <? if (empty($arResult['CURRENT_CITY'])):
            $suggCity = $arResult['SUGGESTED_CITY'];
            ?>
            <div class="geolocation__suggest js-modal js-modal--geolocation">
                <div class="geolocation__question">Это ваш город -
                    <a href="<?=$APPLICATION->GetCurPage() . "?set_city=$suggCity"?>" class="geolocation__suggested-city js-geolocation__suggested-city"><?= $arResult['SUGGESTED_CITY'] ?></a> ?
                </div>
                <div class="geolocation__answers">
                    <div class="geolocation__yes js-geolocation__yes">Да</div>
                    <div class="geolocation__no">Нет</div>
                </div>
            </div>
        <? endif ?>
    </div>
</div>