<?php
namespace Webgk\Handler;

class Register
{
    private static $eventManager;

    public static function initHandlers()
    {
        self::$eventManager = \Bitrix\Main\EventManager::getInstance();
        self::initIblockHandlers();
        self::initMainHandlers();
        self::initSaleHandlers();
        self::initCatalogHandlers();
        self::initAsproHandlers();
        self::initBasketHandlers();
    }

    private static function initIblockHandlers()
    {
        self::$eventManager->addEventHandler('iblock', 'OnBeforeIblockElementUpdate', ["\Webgk\Handler\Iblock\Property", "beforeSetSale"]);
        self::$eventManager->addEventHandler('iblock', 'OnAfterIblockElementUpdate', ["\Webgk\Handler\Iblock\Property", "setSale"]);
        self::$eventManager->addEventHandler('iblock', 'OnAfterIblockElementAdd', ["\Webgk\Handler\Iblock\Property", "setNew"]);
        self::$eventManager->addEventHandler('iblock', 'OnAfterIblockElementAdd', ["\Webgk\Handler\Iblock\Property", "setServices"]);
        self::$eventManager->addEventHandler('iblock', 'OnBeforeIblockElementUpdate', ["\Webgk\Handler\Iblock\Property", "setStatusAdvancedPersonal"]);
        self::$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementSetPropertyValuesEx', ["\Webgk\Handler\Iblock\Property", "setWithoutPrice"]);

        self::$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', ["\Webgk\Handler\Iblock\Subscribe", "onAfterOfferAdd"]);
        self::$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', ["\Webgk\Handler\Iblock\Subscribe", "onAfterOfferUpdate"]);
    }

    private static function initMainHandlers()
    {
        self::$eventManager->addEventHandler('main', 'OnBuildGlobalMenu',  ["\Webgk\Handler\Admin", "statPricePage"]);
        self::$eventManager->addEventHandler('main', 'OnBeforeUserAdd',  ["Webgk\User\Registration", "checkUser"]);
		self::$eventManager->addEventHandler("main", "OnProlog", ["\Webgk\Handler\AMP","setAMPConstant"]);
		self::$eventManager->addEventHandler("main", "OnEpilog", ["\Webgk\Handler\AMP","setLazyLoad"]);
		self::$eventManager->addEventHandler("main", "OnEndBufferContent", ["\Webgk\Handler\AMP","onEndBufferContentHandler"]);
		self::$eventManager->addEventHandler("main", "OnEndBufferContent", ["\Webgk\Handler\Main\PageSpeed","clearHtmlForBots"]);
		self::$eventManager->addEventHandler("main", "OnAfterEpilog", ["\Webgk\Handler\Main\PageSpeed","disableHandlers"], 1);
		self::$eventManager->addEventHandler("main", "OnBeforeUserRegister", ["\Webgk\Captcha", "check"]);

        //self::$eventManager->addEventHandler("main", "OnProlog", ["Webgk\Handler\Main\RedirectProduct","redirectFromOldProduct"]);
    }

    private static function initSaleHandlers()
    {
        self::$eventManager->addEventHandler('sale', 'OnBasketDelete', ["\Webgk\Handler\Sale\Basket", "onBasketDelete"]);
        self::$eventManager->addEventHandler('sale', 'OnOrderNewSendEmail', ["\Webgk\Handler\Sale\Mail", "changeInfoAboutOrder"]);
    }

    private static function initCatalogHandlers()
    {

    }

	private static function initBasketHandlers()
	{
		self::$eventManager->addEventHandler('sale', 'OnBeforeBasketAdd', ["\Webgk\Handler\Sale\Basket",
			"addPropertyDefect"]);
	}

    private static function initAsproHandlers()
    {
        AddEventHandler("aspro.max", "OnAsproItemShowItemPrices", ["\Webgk\Handler\Aspro\Price", "changeHtmlEmptyPrice"]);
        AddEventHandler("aspro.max", "OnAsproShowSideFormLinkIcons", ["\Webgk\Handler\Aspro\Icons", "changeWebFormToCrm"]);
    }
}

