$(document).on('change', '.content-field[data-type="youtube"] select.size', function () {
    var hiddenValues = $(this).parents('.options').find('.youtube_values');
    if ($(this).val() == 'custom') {
        $(hiddenValues).removeClass('hidden').hide().slideDown('medium');
        $(hiddenValues).find('.height, .width').attr('data-validation', 'required');
    }
    else {
        $(hiddenValues).slideUp('medium');
        $(hiddenValues).find('.height, .width').attr('data-validation', '');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="youtube"]').find('select.size').trigger('change');
});