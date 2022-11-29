jQuery(document).ready(($) => {
    /**
     * Contenedor del debug console
     *
     * @type {jQuery|HTMLElement|*}
     */
    const debugConsole = $('#debug-console');

    /**
     * Contenido de depuración
     *
     * @type {jQuery|HTMLElement|*}
     */
    const content    = debugConsole.find('.content-debug-console');
    const btnIsOpen  = debugConsole.find('.toggle-debug-console');
    const debugItems = debugConsole.find('.debug-item');
    const debugBtn   = debugConsole.find('.debug-button');

    /**
     * Clicks
     */
    debugConsole
        // Toggle para el contenido
        .on('click', '.toggle-debug-console', (e) => {
            openContentDebug();
        })
        // Clic en el título
        .on('click', 'h1', (e) => {
            openContentDebug();
        });


    /**
     * Clic sobre elemento
     */
    debugBtn.on('click', (e) => {
        if ( !content.hasClass('open') ) {
            openContentDebug();
        }

        debugItems.removeClass('open');
        debugBtn.removeClass('active');

        debugItems.each((index, el) => {
            if ( e.target.dataset.debug === el.dataset.item ) {
                el.classList.add('open');
                e.target.classList.add('active');
            }
        })
    });


    const openContentDebug = () => {
        if ( content.hasClass('open') ) {
            content.slideUp('fast', () => {
                content.removeClass('open');
                btnIsOpen.removeClass('open');
                debugBtn.removeClass('active');
                debugItems.removeClass('open');
                debugItems.first().addClass('open');
            });
        }
        else {
            content.slideDown('fast', () => {
                content.addClass('open');
                btnIsOpen.addClass('open');
            });
        }
    }
});