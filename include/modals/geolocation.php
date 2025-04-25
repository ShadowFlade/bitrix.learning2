<?php
$suggCity = $arParams['SUGGESTED_CITY'] ?? $arParams['CURRENT_CITY'];
$linkSetCity = $APPLICATION->GetCurPage() . "?set_city=$suggCity";
?>
<div class="geolocation__suggest modal-webgk modal-webgk--geolocation js-modal js-modal--geolocation"
     data-modal-id="geolocation"
>
    <div class="modal-webgk__wrapper">
        <div class="modal-webgk__body">
            <div class="js-cur-scene cur-scene">
                <div class="geolocation__question">Это ваш город -
                    <a href="<?= $linkSetCity ?>"
                       class="geolocation__suggested-city js-geolocation__suggested-city"><?= $suggCity ?></a>
                    ?
                </div>
                <div class="geolocation__answers">
                    <a href="<?= $linkSetCity ?>"
                       class="geolocation__answer geolocation__answer--yes js-geolocation__yes">Да</a>
                    <button class="geolocation__answer geolocation__answer--no geolocation__no js-geolocation__no">Выбрать другой
                    </button>
                </div>
            </div>

            <div class="js-cities-scene" style="display:none;">
                <p class="geolocation__cur-city">Выберите, пожалуйста, ваш город</p>
                <div class="geolocation__cities">
                    <ul class="geolocation__cities js-geolocation__cities">
                        <? foreach ($arParams['CITIES_LIST'] as $city): ?>
                            <li class="geolocation__city-option">
                                <a href="<?= $APPLICATION->GetCurPage() . '?' . $arParams['SET_CITY_PARAM'] . '=' . $city['UF_NAME'] ?>">
                                    <?= $city['UF_NAME'] ?>
                                </a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </div>
            </div>

        </div>

    </div>

</div>