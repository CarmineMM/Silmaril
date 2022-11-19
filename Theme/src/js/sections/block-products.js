export default (app) => {

    // Contenedor del producto
    app.find('.wc-block-grid__products')
        .find('.wc-block-grid__product').addClass('p-lg-3 p-xl-5').css({
            border: 0,
        })
        .find('.wc-block-grid__product-image').addClass('rounded overflow-hidden');
}