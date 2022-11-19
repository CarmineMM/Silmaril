export default (app) => {
    app.closest('body').removeClass('overflow-hidden');
    app.find('.preloader').fadeOut('fast', function() {
        this.remove();
    });
}