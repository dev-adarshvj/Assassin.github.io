$(document).on('change', '.content-field[data-type="ooyala"] select.size', function () {
    var hiddenValues = $(this).parents('.options').find('.ooyala_values');
    if ($(this).val() == 'custom') {
        $(hiddenValues).removeClass('hidden').hide().slideDown('medium');
        $(hiddenValues).find('.height, .width').attr('data-validation', 'required');
    }
    else {
        $(hiddenValues).slideUp('medium');
        $(hiddenValues).find('.height, .width').attr('data-validation', '');
    }
});

$(document).on('change', '.content-field[data-type="ooyala"] select.tvRatingsTimer', function () {
    var hiddenValues = $(this).parents('.options').find('.ooyala_tvratings_values');
    if ($(this).val() == 'custom') {
        $(hiddenValues).removeClass('hidden').hide().slideDown('medium');
        $(hiddenValues).find('.tvRatingsTimerSeconds').attr('data-validation', 'number');
    }
    else {
        $(hiddenValues).slideUp('medium');
        $(hiddenValues).find('.tvRatingsTimerSeconds').attr('data-validation', '');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="ooyala"]').find('select.size').trigger('change');
    $('.content-field[data-type="ooyala"]').find('select.tvRatingsTimer').trigger('change');
});