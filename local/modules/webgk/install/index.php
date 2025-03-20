<?
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Class webgk extends CModule
{
    var $MODULE_ID = 'webgk.main';
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    function __construct()
    {
        $arModuleVersion = array();
        include("version.php");

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = 'webgk main';
        $this->MODULE_DESCRIPTION = 'webgk main';
        $this->PARTNER_NAME = 'webgk';
        $this->PARTNER_URI = 'https://www.webgk.ru/';
    }

    public function doInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function doUninstall(): void
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
