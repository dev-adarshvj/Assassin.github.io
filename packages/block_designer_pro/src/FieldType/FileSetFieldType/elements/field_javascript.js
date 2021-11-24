$(document).on('change', '.content-field[data-type="file_set"] input.title_display', function () {
    var contentField = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(contentField).find('.title-values').slideDown('medium');
    }
    else {
        $(contentField).find('.title-values').slideUp('medium');
    }
});

$(document).on('change', '.content-field[data-type="file_set"] input.list_display', function () {
    var contentField = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(contentField).find('.list-values').slideDown('medium');
    }
    else {
        $(contentField).find('.list-values').slideUp('medium');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="file_set"]').find('input.title_display').trigger('change');
    $('.content-field[data-type="file_set"]').find('input.list_display').trigger('change');
});