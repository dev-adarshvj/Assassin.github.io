$(document).on('change', '.content-field[data-type="number"] input.number_format', function () {
    var numberFormatting = $(this).parents('.options').find('.number-formatting');
    if ($(this).is(':checked')) {
        $(numberFormatting).removeClass('hidden').hide().slideDown('medium');
    }
    else {
        $(numberFormatting).slideUp('medium');
    }
});

$(document).on('change', '.content-field[data-type="number"] input.disallow_float', function () {
    var content_field = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(content_field).find('.alert-floats').removeClass('hidden');
        $(content_field).find('.min_number, .max_number').attr('data-validation-allowing', 'negative');
    }
    else {
        $(content_field).find('.alert-floats').addClass('hidden');
        $(content_field).find('.min_number, .max_number').attr('data-validation-allowing', 'float,negative');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="number"]').find('input.number_format:checked, input.disallow_float:checked').trigger('change');
});