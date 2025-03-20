<?php

namespace Flade\Main;

use Bitrix\Main\EventManager;
use Flade\Main\Catalog\CatalogService;
use Flade\Main\Helpers\CacheManager;
use Flade\Main\Helpers\Logger;
use Bitrix\Iblock\PropertyIndex\Manager;

class EventsService
{
    private static function getEventManager()
    {
        return EventManager::getInstance();
    }

    public static function init()
    {
        self::getEventManager()->addEventHandler('search', 'BeforeIndex', [
                '\Flade\Main\EventsService',
                'BeforeIndexCatalogHandler'
            ]
        );

        self::getEventManager()->addEventHandler('search', 'OnBeforeFullReindexClear', [
                '\Flade\Main\EventsService',
                'clearSearchTaggedCache'
            ]
        );

        self::getEventManager()->addEventHandler('search', 'BeforeIndex', [
                '\Flade\Main\EventsService',
                'clearSearchTaggedCache'
            ]
        );

        self::getEventManager()->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [
            '\Flade\Main\EventsService',
            'updateStoreCustomAvailable'
        ]);

        self::getEventManager()->addEventHandler('iblock', 'OnAfterIBlockElementAdd', [
            '\Flade\Main\EventsService',
            'updateStoreCustomAvailable'
        ]);

        self::getEventManager()->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [
            '\Flade\Main\EventsService',
            'clearCatalogTaggedCache'
        ]);

        self::getEventManager()->addEventHandler('iblock', 'OnAfterIBlockElementAdd', [
            '\Flade\Main\EventsService',
            'clearCatalogTaggedCache'
        ]);

        self::getEventManager()->addEventHandler('main', 'OnEndBufferContent', [
            '\Flade\Main\EventsService',
            'sanitizeTitle'
        ]);

        self::getEventManager()->addEventHandler('main', 'OnBeforeEventAdd', [
            '\Flade\Main\EventsService',
            'notificationSender'
        ]);

        self::getEventManager()->addEventHandler('catalog', 'OnCompleteCatalogImport1C', [
            '\Flade\Main\EventsService',
            'reindexAllAfter1c'
        ]);

        self::getEventManager()->addEventHandler('catalog', 'OnCompleteCatalogImport1C', [
            '\Flade\Main\EventsService',
            'updateStoreCustomAvailable1c'
        ]);

        self::getEventManager()->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [
            '\Flade\Main\EventsService',
            'setSelections'
        ]);

        self::getEventManager()->addEventHandler('iblock', 'OnAfterIBlockElementAdd', [
            '\Flade\Main\EventsService',
            'setSelections'
        ]);

        self::getEventManager()->addEventHandler('ipol.sdek', 'onCalculate', [
            '\Flade\Main\EventsService',
            'changeSDEKTerms'
        ]);
    }

    static function changeSDEKTerms(array &$arResult, $profile, $arConfig, $arOrder): void
    {
        if (
            $arResult['RESULT'] === 'OK'
            && $arOrder['PRICE'] >= (int) get_setting('delivery_price_for_free')
        ) {
            $arResult['VALUE'] = 0;
        }
    }

    static function setSelections(array $arFields): array
    {
        if ($arFields['IBLOCK_ID'] == get_iblock_id('selections')) {
            $iProps = get_iblock_properties($arFields['IBLOCK_ID']);

            if (empty($arFields['PROPERTY_VALUES'][$iProps['SECTION_LINK']['ID']])) return $arFields;

            $sectionLink = reset($arFields['PROPERTY_VALUES'][$iProps['SECTION_LINK']['ID']]);
            $sectionId = $sectionLink['VALUE'];

            if (empty($sectionId)) return $arFields;

            CatalogService::setSelections($arFields['ID'], $arFields['IBLOCK_ID'], $sectionId);
        }

        return $arFields;
    }

    /**
     * Generate notifications based on the event and order information.
     *
     * @param mixed $event The event triggering the notification.
     * @param mixed $lid The lead ID.
     * @param mixed $arFields The array containing order information.
     * @param mixed $message_id The message ID.
     * @return bool Returns true if notifications were sent successfully, false otherwise.
     */
    static function notificationSender(&$event, &$lid, &$arFields, &$message_id): bool
    {
        if (in_array($event, NotificationService::SYSTEM_NOTIFICATION_EVENTS)) {
            $orderInfo = NotificationService::getInfoForEvent($arFields['ORDER_ID']);

            $notificationService = new NotificationService($orderInfo['USER_ID']);
            $methods = $notificationService->getMethods();

            if (empty($methods)) return false;

            foreach ($methods['system'] as $method) {
                if ($method == 'email') {
                    $arFields['EMAIL'] = $orderInfo['EMAIL'];
                    $arFields['NAME'] = $orderInfo['FIO'];
                    $arFields['PHONE'] = $orderInfo['PHONE'];
                    $arFields['ADDRESS'] = $orderInfo['ADDRESS'];
                    $arFields['DELIVERY_DATETIME'] = $orderInfo['DELIVERY_DATETIME'];
                    $arFields['ADDRESS_LOMBARD'] = $orderInfo['ADDRESS_LOMBARD'];
                    $arFields['PRICE'] = $orderInfo['PRICE'];
                    $arFields['DELIVERY_PRICE'] = $orderInfo['DELIVERY_PRICE'];
                    $arFields['ONE_CLICK_TEXT'] = $orderInfo['ONE_CLICK'] ? 'Заказ в 1 клик' : '';

                    switch ($orderInfo['DELIVERY_ID']) {
                        case SHIPMENT_PICKUP_ID:
                        {
                            $arFields['DELIVERY_NAME'] = 'Самовывоз';
                            break;
                        }
                        case SHIPMENT_SDEK_DELIVERY_ID:
                        {
                            $arFields['DELIVERY_NAME'] = 'Доставка СДЭК-курьер';
                            break;
                        }
                        case SHIPMENT_SDEK_PVZ_ID:
                        {
                            $arFields['DELIVERY_NAME'] = 'Доставка CDEK ПВЗ';
                            break;
                        }
                        default:
                        {
                            $arFields['DELIVERY_NAME'] = '';
                            break;
                        }
                    }

                    $arFields['LINK'] = get_absolute_link('/personal/orders/order_detail.php?ID=' . $arFields['ORDER_ID']);

                    $arFields['BASKET'] = '<tbody>';
                    foreach ($orderInfo['BASKET'] as $item) {
                        $img = $item['PHOTO'] ? '<img border="0" width="140" style="vertical-align:top;height: 120px;padding-right: 16px;\" src="' . $item['PHOTO'] . '">' : '';

                        $dName = empty($item['NAME']) ? 'уважаемый клиент' : $item['NAME'];
                        $name = "<td><span style='color: #3885ff;font-size: 17px;line-height: 23px;'>" . $dName . "</span></td>";
                        $articul = "<td style='padding-top: 10px;display: inline-block;'><span style='color: #b5b5b7;font-size: 13px;line-height: 18px;width: 90px;display: inline-block;'>" . "Артикул" . "</span><span style='font-size: 13px;line-height: 18px;display: inline-block;'>" . $item['ARTICLE'] . "</span></td>";
                        $quantity = "<td style='padding-top: 10px;display: inline-block;'><span style='color: #b5b5b7;font-size: 13px;line-height: 18px;width: 90px;display: inline-block;'>" . "Количество" . "</span><span style='font-size: 13px;line-height: 18px;display: inline-block;'>" . $item['QUANTITY'] . "</span></td>";
                        $price = "<td style='padding-top: 10px;display: inline-block;'><span style='color: #b5b5b7;font-size: 13px;line-height: 18px;width: 90px;display: inline-block;'>" . "Цена" . "</span><span style='font-size: 13px;line-height: 18px;display: inline-block;'>" . $item['PRICE'] . "</span></td>";
                        $div = "<div style='display:inline-block; width:200px;text-align: left;'><table border='0' cellpadding='0' role='presentation'><tbody><tr>$name</tr><tr>$articul</tr><tr>$quantity</tr><tr>$price</tr></tbody></table></div>";

                        $arFields['BASKET'] .= "<tr><td>$div$img</td></tr>";
                    }
                    $arFields['BASKET'] .= '</tbody>';
                } elseif ($method == 'sms') {
                    if (get_setting('sms_notification_on') != 'Y') break;
                    $smsParams = [
                        'PHONE' => (string)clear_phone($orderInfo['PHONE']),
                        'ORDER_ID' => $arFields['ORDER_ID'],
                        'NAME' => $orderInfo['FIO'],
                    ];
                    $sms = new \Bitrix\Main\Sms\Event(
                        'SMS_' . $event,
                        $smsParams,
                    );
                    $sms->setSite('s1'); // SITE_ID здесь не работает
                    $sms->setLanguage('ru');
                    $sms->send();
                }
            }

            if (!in_array('email', $methods['system'])) return false;
        }

        return true;
    }

    static function clearCatalogTaggedCache($arFields)
    {
        if ($arFields['IBLOCK_ID'] == get_iblock_id('catalog')) {
            CacheManager::clearCacheByTag('custom_catalog_yml_tag');
        }
    }

    /**
     * Добавляем разделы в иднекс для каталога
     * TODO: Проверять доступность товара, чтобы не добавлять его в индекс (? мб неактуально)
     *
     * @param array $arFields
     * @return $arFields
     */
    static function BeforeIndexCatalogHandler($arFields)
    {
        if ($arFields["MODULE_ID"] == "iblock"
            && $arFields["PARAM2"] == get_iblock_id('catalog')
            && substr($arFields["ITEM_ID"], 0, 1) != "S") {
            $arFields["PARAMS"]["iblock_section"] = [];
            $rsSections = \CIBlockElement::GetElementGroups($arFields["ITEM_ID"], true);
            while ($arSection = $rsSections->Fetch()) {
                $arFields["PARAMS"]["iblock_section"][] = $arSection["ID"];
            }
        }

        return $arFields;
    }

    public static function clearSearchTaggedCache($arFields): void
    {
        if ($arFields['IBLOCK_ID'] == get_iblock_id('catalog')) {
            CacheManager::clearCacheByTag('custom_search_tag');
            CacheManager::clearCacheByTag('custom_catalog_search_tag');
        }
    }

    public static function updateStoreCustomAvailable($arFields)
    {
        $iblockId = get_iblock_id('catalog');

        CatalogService::updateStoreCustomAvailable($arFields['ID'], $iblockId);
    }

    public static function updateStoreCustomAvailable1c()
    {
        include_modules(['main', 'sale']);

        $iblockId = get_iblock_id('catalog');

        $res = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y', 'CATALOG_AVAILABLE' => 'Y'],
            false,
            false,
            ['ID']
        );

        while ($el = $res->GetNext()) {
            CatalogService::updateStoreCustomAvailable($el['ID'], $iblockId);
        }

        $index = Manager::createIndexer($iblockId);
        $index->startIndex();
        $index->continueIndex();
        $index->endIndex();
    }

    /*
     * Очищаем заголовок страницы от символов <title>
     *
     * @param string &$content
     */
    public static function sanitizeTitle(&$content)
    {
        if (defined("ADMIN_SECTION")) return;

        $titleStart = strpos($content, '<title>');
        if ($titleStart !== false) {
            $titleEnd = strpos($content, '</title>', $titleStart);
            if ($titleEnd !== false) {
                $title = substr($content, $titleStart + 7, $titleEnd - $titleStart - 7); // 7 is the length of '<title>'
                $title = strip_tags(htmlspecialchars_decode($title));
                $newTitle = '<title>' . $title . '</title>';
                $content = substr_replace($content, $newTitle, $titleStart, $titleEnd - $titleStart + 8); // 8 is the length of '</title>'
            }
        }
    }

    public static function reindexAllAfter1c($arParams, $arFields)
    {
        include_modules('search');
        $res = \CSearch::ReIndexAll(true, 60);
        while (is_array($res)) {
            $res = \CSearch::ReIndexAll(true, 60, $res);
        }

        Logger::log(['event' => 'reindex_all', 'Количество проиндексированных элементов' => $res]);

        return true;
    }
}
