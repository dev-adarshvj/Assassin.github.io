$(document).on('change', '.content-field[data-type="file"] input.title_field_required', function () {
    var titleFieldValues = $(this).parents('.options').find('.title_field-values');
    if ($(this).is(':checked')) {
        $(titleFieldValues).find('.title_field_fallback_value').attr('disabled', 'disabled');
    }
    else {
        $(titleFieldValues).find('.title_field_fallback_value').prop("disabled", false);
    }
});

$(document).on('change', '.content-field[data-type="file"] input.title_field', function () {
    var titleFieldValues = $(this).parents('.options').find('.title_field-values');
    if ($(this).is(':checked')) {
        $(titleFieldValues).removeClass('hidden').hide().slideDown('medium');
    }
    else {
        $(titleFieldValues).slideUp('medium');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="file"]').find('input.title_field:checked, input.title_field_required:checked').trigger('change');
});