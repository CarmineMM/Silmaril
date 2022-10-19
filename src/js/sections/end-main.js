export default (app) => {
    const main = app.find('main').first();

    app.find('.end-main').each((index, el) => {
        const detach = el.parentElement.removeChild(el);
        main.after(detach);
    });
}