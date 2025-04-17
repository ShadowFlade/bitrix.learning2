document.addEventListener('DOMContentLoaded', () => {
    const geoModalEl = document.querySelector('.js-modal--geolocation');
    if (!geoModalEl) {
        return;
    }
    const geoModal = new Modal(geoModalEl);
})