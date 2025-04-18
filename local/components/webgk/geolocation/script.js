document.addEventListener('DOMContentLoaded', () => {
    const geoModalEl = document.querySelector('.js-modal--geolocation');
    if (!geoModalEl) {
        return;
    }
    const geoModal = new Modal(geoModalEl);
    const yesEl = document.querySelector('.js-geolocation__yes');
    yesEl.addEventListener('click', (e) => {
        BX.Ajax.
        e.target.textContent
    })
})