$(document).on('change', '.content-field[data-type="smart_link"] input.exclude_file', function () {
    var contentField = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(contentField).find('[data-smart-link-type="file"]').slideUp('medium');
    }
    else {
        $(contentField).find('[data-smart-link-type="file"]').slideDown('medium');
    }
});

$(document).on('change', '.content-field[data-type="smart_link"] input.exclude_image', function () {
    var contentField = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(contentField).find('[data-smart-link-type="image"]').slideUp('medium');
    }
    else {
        $(contentField).find('[data-smart-link-type="image"]').slideDown('medium');
    }
});

$(document).on('change', '.content-field[data-type="smart_link"] input.exclude_url', function () {
    var contentField = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(contentField).find('[data-smart-link-type="url"]').slideUp('medium');
    }
    else {
        $(contentField).find('[data-smart-link-type="url"]').slideDown('medium');
    }
});

$(document).on('change', '.content-field[data-type="smart_link"] input.exclude_relative_url', function () {
    var contentField = $(this).parents('.content-field');
    if ($(this).is(':checked')) {
        $(contentField).find('[data-smart-link-type="relative_url"]').slideUp('medium');
    }
    else {
        $(contentField).find('[data-smart-link-type="relative_url"]').slideDown('medium');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="smart_link"]').find('input.exclude_file:checked').trigger('change');
    $('.content-field[data-type="smart_link"]').find('input.exclude_image:checked').trigger('change');
    $('.content-field[data-type="smart_link"]').find('input.exclude_url:checked').trigger('change');
    $('.content-field[data-type="smart_link"]').find('input.exclude_relative_url:checked').trigger('change');
});