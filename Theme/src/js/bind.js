/**
 * Constructor rápido para hacer funciones desde HTML
 *
 * @param $
 */
export default ($) => {
    // Textos
    $.find('[\\$-text]').each((index, el) => {
        const call = el.getAttribute('$-text');

        el.textContent = (() => eval(call))();
    });

    // HTML
    $.find('[\\$-html]').each((index, el) => {
        const call = el.getAttribute('$-html');

        el.innerHTML = (() => eval(call))();
    });
}