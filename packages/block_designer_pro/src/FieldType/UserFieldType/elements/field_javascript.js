$(document).on('change', '.content-field[data-type="user"] input.make_link', function () {
    var content_field = $(this).parents('.content-field');
    var linkValues = $(content_field).find('.link-values');
    if ($(linkValues).hasClass('hidden')) {
        $(linkValues).removeClass('hidden').hide();
    }
    if ($(this).is(':checked')) {
        $(content_field).find('.link-values').slideDown('medium');
    }
    else {
        $(content_field).find('.link-values').slideUp('medium');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="user"]').find('input.make_link:checked').trigger('change');
});