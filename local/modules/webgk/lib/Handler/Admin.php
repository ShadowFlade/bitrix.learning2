<?php
namespace Webgk\Handler;

class Admin
{
    //добавляем кастомную страницу с конфигами в админку в раздел сервис
    public static function statPricePage(&$adminMenu, &$moduleMenu)
    {

        $moduleMenu[] = array(
            "parent_menu" => "global_menu_services", // в раздел "Сервис"
            "section" => "Статистика цен",
            "sort"        => 100,                    // сортировка пункта меню - поднимем повыше
            "url"         => "statistic_price.php",  // ссылка на пункте меню - тут как раз и пишите адрес вашего файла, созданного в /bitrix/admin/
            "text"        => 'Статистика цен',
            "title"       => 'Статистика цен',
            "icon"        => "form_menu_icon", // малая иконка
            "page_icon"   => "form_page_icon", // большая иконка
            "items_id"    => "menu_ваше название",  // идентификатор ветви
            "items"       => array()          // остальные уровни меню
        );
    }
}