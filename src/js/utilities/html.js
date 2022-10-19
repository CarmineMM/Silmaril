export default ($) => {
    $.find('.target-blank').each((index, el) => {
        el.setAttribute('target', '_blank');
    });
}