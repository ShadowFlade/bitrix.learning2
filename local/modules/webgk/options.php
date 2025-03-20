<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$request = HttpApplication::getInstance()->getContext()->getRequest();

$moduleId = htmlspecialcharsbx($request['mid'] != '' ? $request['mid'] : $request['id']);

Loader::includeModule($moduleId);

$aTabs = [
    [
        'DIV' => 'main',
        'TAB' => Loc::getMessage('SHADOW_FLADE_TAB_MAIN'),
        'TITLE' => Loc::getMessage('SHADOW_FLADE_TAB_MAIN'),
        'OPTIONS' => [

        ]
    ]
];

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->Begin();
?>

    <form action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= $moduleId ?>&lang=<?= LANG ?>"
          method="post">

        <?php
        foreach ($aTabs as $aTab) {

            if ($aTab['OPTIONS']) {

                $tabControl->BeginNextTab();

                foreach ($aTab['OPTIONS'] as $aOption) {
                    if ($aOption[3][0] == 'number') {
                        $optionName = $aOption[0];
                        $optionValue = Option::get($moduleId, $optionName);
                        ?>

                        <tr>
                            <td width="50%"><?= $aOption[1] ?></td>
                            <td width="50%">
                                <input class="adm-input"
                                       type="<?= $aOption[3][0] ?: 'text' ?>"
                                       name="<?= $optionName ?>"
                                       size="<?= $aOption[3][1] ?: 10 ?>"
                                    <?= isset($aOption[3][2]) ? sprintf('min="%s"', $aOption[3][2]) : '' ?>
                                    <?= isset($aOption[3][3]) ? sprintf('max="%s"', $aOption[3][3]) : '' ?>
                                       value="<?= $optionValue ?>"/>
                            </td>
                        </tr>

                    <?php } else {
                        __AdmSettingsDrawRow($moduleId, $aOption);
                    }
                }
            }
        }
        ?>

        <?php

        $tabControl->Buttons();
        ?>

        <input type="submit" name="apply" value="<?= Loc::GetMessage('SHADOW_FLADE_BUTTON_APPLY') ?>"
               class="adm-btn-save"/>
        <input type="submit" name="default" value="<?= Loc::GetMessage('SHADOW_FLADE_BUTTON_DEFAULT') ?>" style="float: right"/>

        <?= bitrix_sessid_post() ?>

    </form>

<?php
$tabControl->End();

if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {

        foreach ($aTab['OPTIONS'] as $arOption) {

            if (!is_array($arOption) || $arOption['note']) {
                continue;
            }

            if ($request['apply']) {

                $optionValue = $request->getPost($arOption[0]);

                if ($arOption[3][0] == 'checkbox' && $optionValue == '') {
                    $optionValue = 'N';
                }

                Option::set($moduleId, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);

            } elseif ($request['default']) {

                Option::set($moduleId, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . $moduleId . '&lang=' . LANG . '&mid_menu=1');
}
