class Modal {
    constructor(element, options = {}) {
        this.element = element;
        this.options = this._defineOptions(options);

        if (!this.element || this.element.classList.contains(this.options.modalInitClass))
            return;

        this.id = element.getAttribute('data-modal-id');

        this.open = this.open.bind(this);
        this.close = this.close.bind(this);


        if (this.options.canUserClose) {
            this._handleModalDocumentKeydown =
                this._handleModalDocumentKeydown.bind(this);
            this._handleDocumentClick =
                this._handleDocumentClick.bind(this);
            this._handleModalDocumentClick =
                this._handleModalDocumentClick.bind(this);
        }


        this._init();

    }

    _init() {
        this._initEventListeners();

        this.element.modal = this;

        this.element.classList.add(this.options.modalInitClass);
    }

    /**
     * Активируем обработчики
     * @private
     */
    _initEventListeners() {
        const triggers = document.querySelectorAll(
            `[data-modal-id="${this.id}"].${this.options.triggerClass}`
        );
        triggers.forEach((trigger) => {
            trigger.removeEventListener('click', this.open);
            trigger.addEventListener('click', (e) => {
                //для случаев если кнопка закрытия находится внутри кнопки открытия (может быть такое если модалка находится внутри кнопки открытия для относительного расположения, то есть модалка типа tooltip)
                if (e.target.classList.contains(this.options.closeButtonClass)) {
                    return;
                }
                trigger.dispatchEvent(new CustomEvent('open-modal', {
                    bubbles: true,
                    cancelable: true,
                    detail: {id: trigger.getAttribute('data-slide')}
                }));

                this.open({trigger: trigger});
            });
        });
    }

    /**
     * Открываем модалку
     * @param detail
     */
    open(detail) {
        // if (!webgkScrollbar.isHidden) {
        //     webgkScrollbar.hide();
        // }
        let pageYStart;

        if (this.options.canUserClose) {
            this.element.addEventListener('click', this._handleModalDocumentClick);
            document.addEventListener('click', this._handleDocumentClick);
            document.addEventListener(
                'keydown',
                this._handleModalDocumentKeydown
            );
        }


        const modalBodyMobile = this.element.querySelector('.' + this._baseClass + '__body-mobile')

        this.element.ariaHidden = false;
        this.element.classList.add(this.options.modalActiveClass);

        this.element.dispatchEvent(new CustomEvent('open', {detail: detail}));

    }

    /**
     * Закрываем модалку
     */
    close() {
        if (webgkScrollbar && webgkScrollbar.isHidden) {
            webgkScrollbar.show();
        }

        this.element.removeEventListener(
            'click',
            this._handleModalDocumentClick
        );

        document.removeEventListener(
            "keydown",
            this._handleModalDocumentKeydown
        );

        this.element.ariaHidden = true;
        this.element.classList.remove(this.options.modalActiveClass);

        this.element.dispatchEvent(new CustomEvent('close'));

        setTimeout(() => {
            this.element.querySelector(this.modalWrapperSelector).style.bottom = 'inherit'
            this.element.querySelector(this.modalWrapperSelector).style.opacity = 1
        }, 200);
    }

    /**
     * Обрабатываем клик внутри модалки
     * @param e
     * @private
     */
    _handleModalDocumentClick(e) {
        const isClose = e.target.closest(this.options.closeSelector);
        const isNoClose = e.target.closest(this.options.noCloseSelector);
        const isCloseButton = e.target.closest(
            `.${this.options.closeButtonClass}`
        );

        if (isCloseButton) {
            this.close();
        }

        if (isClose && !isNoClose) {
            this.close();
        }

    }

    /**
     * Обрабатываем клик вне модалки
     * @param e
     * @private
     */
    _handleDocumentClick(e) {
        //если кликаем внутри элемента - ничего не делаем, за это отвечает другой обработчик (_handleModalDocumentClick)
        if (e.target.closest([`[data-modal-id="${this.id}"]`])) {
            return;
        }
        const isClose = e.target.closest(this.options.closeSelector);
        const isOpenButton = e.target.closest('.' + this.options.triggerClass);

        if (!isClose && !isOpenButton) {
            this.close();
        }
    }


    /**
     * Закрываем модалку на Esc
     * @param e
     * @private
     */
    _handleModalDocumentKeydown(e) {
        if (e.keyCode === 27) {
            this.close();
        }
    }

    /**
     * Мержим стандартные и юзер настройки модалки
     * @param options
     * @returns {*&{modalInitClass: string, modalActiveClass: string, triggerClass: string, closeButtonClass: string, closeSelector: string, noCloseSelector: string, canUserClose: boolean}}
     * @private
     */
    _defineOptions(options) {
        this._baseClass = "modal-webgk";
        this.modalWrapperSelector = "." + this._baseClass + '__wrapper';

        const initialOptions = {
            modalInitClass: this._baseClass + '--init',
            modalActiveClass: this._baseClass + '--active',
            triggerClass: 'js-modal-trigger',
            closeButtonClass: 'js-modal-close-btn',
            closeSelector: '.js-modal-close',
            noCloseSelector: '.js-modal-no-close',
            /**
             * Должен ли юзер иметь право сам закрывать модалку (например loaidng модальное окно он не должен закрывать сам)
             */
            canUserClose: true
        };

        return {
            ...initialOptions,
            ...options,
        };
    }

}