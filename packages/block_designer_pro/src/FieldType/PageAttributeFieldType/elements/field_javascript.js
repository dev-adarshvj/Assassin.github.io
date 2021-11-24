$(document).on('change', '.content-field[data-type="page_attribute"] select.attribute', function () {
    var content_field = $(this).parents('.content-field');
    if ($(this).val() == 'other') {
        $(content_field).find('.attribute-other').removeClass('hidden');
        $(content_field).find('.attribute-other input').attr('data-validation-optional', 'false');
    }
    else {
        $(content_field).find('.attribute-other').addClass('hidden');
        $(content_field).find('.attribute-other input').attr('data-validation-optional', 'true');
    }
});

$(document).on('change', '.content-field[data-type="page_attribute"] input.current-page', function () {
    var baseFields = $(this).parents('.options').find('.base-fields');
    if ($(this).is(':checked')) {
        $(baseFields).find('input[id$="label]"]').removeData('validation');
        $(baseFields).find('input[id$="label]"]').removeAttr('data-validation');
        $(baseFields).find('input[id$="slug]"]').removeData('validation');
        $(baseFields).find('input[id$="slug]"]').removeAttr('data-validation');
        $(baseFields).slideUp(200, function () {
            $(this).addClass('hidden');
        });
    }
    else {
        $(baseFields).removeClass('hidden').slideDown(200);
        $(baseFields).find('input[id$="label]"]').attr('data-validation', 'required');
        $(baseFields).find('input[id$="slug]"]').attr('data-validation', 'custom');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="page_attribute"]').find('select.attribute').trigger('change');
    $('.content-field[data-type="page_attribute"]').find('input.current-page:checked').trigger('change');
});