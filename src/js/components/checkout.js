export default (app) => {
    const checkout = app.find('.checkout')

    checkout.find('input').addClass('form-control');
    checkout.find('textarea').addClass('form-control');
    checkout.find('thead .product-name').text('Programa');

    const h3 = checkout.find('h3:first-of-type')[0];

    if (h3) {
        h3.textContent = 'Detalles del Participante';
    }
}