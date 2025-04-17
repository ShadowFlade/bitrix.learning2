class Scrollbar {
    constructor() {
        this.isHidden = false;
        this.scrollPosition = 0;
    }

    hide() {
        this.scrollPosition = window.scrollY || document.documentElement.scrollTop;

        document.body.style.paddingRight = `${window.innerWidth - document.body.clientWidth}px`;
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = '-' + this.scrollPosition + 'px';
        document.body.style.left = '0';
        document.body.style.width = '100%';

        this.isHidden = true;
    }

    show() {
        document.body.style.paddingRight = '';
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.left = '';
        document.body.style.width = '';

        window.scroll(0, +this.scrollPosition);

        this.isHidden = false;
    }
}