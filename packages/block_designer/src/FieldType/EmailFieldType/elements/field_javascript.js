$(document).on('change', '.content-field[data-type="email"] input.anchor_field', function () {
    var emailFieldValues = $(this).parents('.options').find('.anchor_field-values');
    if ($(this).is(':checked')) {
        $(emailFieldValues).removeClass('hidden').hide().slideDown('medium');
    }
    else {
        $(emailFieldValues).slideUp('medium');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="email"]').find('input.anchor_field:checked').trigger('change');
});