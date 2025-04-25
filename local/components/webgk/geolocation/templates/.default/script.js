document.addEventListener('DOMContentLoaded', () => {


    const geoModalEl = document.querySelector('.js-modal--geolocation');
    if (!geoModalEl) {
        return;
    }
    const geoModal = new Modal(geoModalEl);
    const geolocationCurCity = document.querySelector('.js-geolocation__cur-city');
    geolocationCurCity.addEventListener('click', (e) => {
        e.preventDefault();
        geoModal.open();
    })
    if (geolocationCurCity.dataset.showModal == 'yes') {
        geoModal.open();
    }
    const noButtonEl = document.querySelector('.js-geolocation__no');
    noButtonEl.addEventListener('click', handleNo);
});

/**
 *
 * @param {Event} e
 * @returns {Promise<void>}
 */
async function handleNo(e) {
    e.preventDefault();
    // const citiesList = await getCitiesList();
    const citiesScene = document.querySelector('.js-cities-scene');
    // buildCitiesList(citiesScene, citiesList);
    changeSceneTo(citiesScene);
}

async function getCitiesList() {
    BX.ajax.runComponentAction(
        'webgk:geolocation',
        'getCitiesList', {
            mode: 'class',
            signedParameters: [],
        }
    )
}

function changeSceneTo(nextSceneEl) {
    const curScene = document.querySelector('.js-cur-scene');
    if (!curScene || !nextSceneEl) {
        return;
    }
    curScene.style.display = 'none';
    nextSceneEl.style.display = 'block';
}

function buildCitiesList(citiesScene, cities) {
    let html = '';
    cities.forEach(city => {
        html += `<li class="geolocation__city-option"><a href="${city.link}">${city.name}</a></li>`
    })
    citiesScene.innerHTML = html;
}

