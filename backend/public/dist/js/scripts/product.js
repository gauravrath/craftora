function productTabs(tab) {
    $('#prod_detail, #prod_img, #prod_var').removeClass('active');
    $('#details, #image, #variants').removeClass('active').hide();

    if (tab === 'details') {
        $('#prod_detail').addClass('active');
        $('#details').addClass('active').show();

    } else if (tab === 'image') {
        $('#prod_img').addClass('active');
        $('#image').addClass('active').show();

    } else if (tab === 'variants') {
        $('#prod_var').addClass('active');
        $('#variants').addClass('active').show();
    }
}