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
$asset->addCss(SITE_TEMPLATE_PATH . '/css/Modal.css');

?>
<div class="geolocation">
    <div class="geolocation__wrapper">
        <p class="geolocation__cur-city"><?= $arResult['CURRENT_CITY'] ?></p>
        <? if (empty($arResult['CURRENT_CITY'])):
            $suggCity = $arResult['SUGGESTED_CITY'];
            $APPLICATION->IncludeFile(
                SITE_DIR . '/include/modals/geolocation.php',
                ['SUGGESTED_CITY' => $arResult['SUGGESTED_CITY']]
            );
        endif ?>
    </div>
</div>