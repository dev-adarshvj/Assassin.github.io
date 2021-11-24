$(document).on('change', '.content-field[data-type="image"] input.make_thumbnail', function () {
    var contentField = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(contentField).find('.thumbnail-values').slideDown('medium');
        $(contentField).find('.width, .height').attr('data-validation-optional', 'false').attr('data-validation', 'number');
    }
    else {
        $(contentField).find('.thumbnail-values').slideUp('medium');
        $(contentField).find('.width, .height').attr('data-validation-optional', 'true').attr('data-validation', '');
    }
});

$(document).on('change', '.content-field[data-type="image"] select.make_link', function () {
    var contentField = $(this).parents('.content-field');
    switch ($(this).val()) {
        case '1':
        case '2':
            $(contentField).find('.link-values').slideDown('medium');
            break;
        default:
            $(contentField).find('.link-values').slideUp('medium');
            break;
    }
});

$(document).on('keyup', '.content-field[data-type="image"] input.thumbnail_handle', function () {
    var contentField = $(this).parents('.content-field');
    var makeThumbnail = $(contentField).find('input.make_thumbnail');
    var me = this;
    var value = $(me).val();
    if ($.trim(value) !== '') {
        $(makeThumbnail).prop('checked', false).attr('disabled', 'disabled').trigger('change');
    }
    else {
        $(makeThumbnail).prop('disabled', false);
    }
});

$(document).ready(function () {
    $('.content-field[data-type="image"]').find('input.make_thumbnail:checked').trigger('change');
    $('.content-field[data-type="image"]').find('select.make_link').trigger('change');
    $('.content-field[data-type="image"]').find('input.thumbnail_handle').trigger('keyup');
});