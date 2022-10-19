export default (app) => {
    const list = app.find('.wp-block-latest-posts__list');

    list.find('li').addClass('card shadow-sm mb-3 overflow-hidden');
}