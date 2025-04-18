<?php
$suggCity = $arParams['SUGGESTED_CITY'];
?>
<div class="geolocation__suggest modal-webgk modal-webgk--geolocation js-modal js-modal--geolocation" data-modal-id="geolocation">
    <div class="modal-webgk__wrapper js-modal-close">
        <div class="modal-webgk__body">
            <div class="geolocation__question">Это ваш город -
                <a href="<?= $APPLICATION->GetCurPage() . "?set_city=$suggCity" ?>"
                   class="geolocation__suggested-city js-geolocation__suggested-city"><?= $suggCity ?></a>
                ?
            </div>
            <div class="geolocation__answers">
                <div class="geolocation__yes js-geolocation__yes">Да</div>
                <div class="geolocation__no">Нет</div>
            </div>
        </div>

    </div>

</div>