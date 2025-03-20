<?php
namespace Webgk\Handler\Main;

class pageSpeed
{

    public static function clearHtmlForBots(&$content){

        $bIndexBot = \CMax::checkIndexBot(); // is indexed yandex/google bot

        //проверка на бота
        if($bIndexBot) {

            //вырезам теги стилей из папки /bitrix/css и /bitrix/js
            $pattern = '/<link href="\/bitrix\/css\/.+?.css\?\d+"[^>]+>/';
            $content = preg_replace($pattern, '', $content);
            $pattern = '/<link href="\/bitrix\/js\/.+?.css\?\d+"[^>]+>/';
            $content = preg_replace($pattern, '', $content);

            //вырезаем скрипты
            $pattern = '/<script.*?<\/script>/is';
            $content = preg_replace($pattern, '', $content);

            //вырезаем фреймы
            $pattern = '/<iframe.*?<\/iframe>/is';
            $content = preg_replace($pattern, '', $content);

            //сжатие html
            $search = array(
                '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/',
                '/\>[^\S ]+/s',
                '/[^\S ]+\</s',
                '/(\s)+/s'
            );
            $replace = array(
                '',
                '>',
                '<',
                '\\1'
            );

            $content = preg_replace($search, $replace, $content);
        }

    }

    public static function disableHandlers(){

        $bIndexBot = \CMax::checkIndexBot(); // is indexed yandex/google bot

        //проверка на бота
        if($bIndexBot) {
            //die();
        }

    }

}