$(document).on('click', '.content-field[data-type="color_picker"] .palette-rows .btn-add-row', function (e) {
    e.preventDefault();
    var contentField = $(this).parents('.content-field');
    var paletteRows = $(this).parent();
    var paletteRow = $(paletteRows).find('.palette-row:first-child');
    var clonePaletteRow = $(paletteRow).clone();
    var ids = new Array();
    $(paletteRows).find('.palette-row').each(function () {
        ids.push(parseInt($(this).attr('data-attr-row')));
    });
    if (ids.length == 0) {
        ids.push(0);
    }
    var dataAttrRow = Math.max.apply(Math, ids) + 1;
    $(clonePaletteRow).attr('data-attr-row', dataAttrRow);
    $(clonePaletteRow).find('.palette-color + .palette-color').remove();
    $(clonePaletteRow).find('.palette-color input').val('');
    $(clonePaletteRow).find('.palette-color input').attr('name', 'fields[' + $(contentField).attr('data-id') + '][palette][' + dataAttrRow + '][]');
    $(this).before(clonePaletteRow);
});

$(document).on('click', '.content-field[data-type="color_picker"] .palette-rows .palette-row .btn-add-color', function (e) {
    e.preventDefault();
    var paletteRow = $(this).parent();
    var paletteColor = $(paletteRow).find('.palette-color:last-child');
    var clonePaletteColor = $(paletteColor).clone();
    $(clonePaletteColor).find('input').val('').removeAttr('value');
    $(paletteColor).after(clonePaletteColor);
});

$(document).on('click', '.content-field[data-type="color_picker"] .palette-rows .palette-row .palette-color .btn-delete-color', function (e) {
    e.preventDefault();
    var paletteColor = $(this).parents('.palette-color');
    $(paletteColor).remove();
});

$(document).on('click', '.content-field[data-type="color_picker"] .palette-rows .palette-row .btn-delete-row', function (e) {
    e.preventDefault();
    var paletteRow = $(this).parent();
    $(paletteRow).slideUp(200, function () {
        $(paletteRow).remove();
    });
});

$(document).on('change', '.content-field[data-type="color_picker"] input.show_palette', function () {
    var showPaletteValues = $(this).parents('.options').find('.pallet-rows-container');
    if ($(this).is(':checked')) {
        $(showPaletteValues).removeClass('hidden').hide().slideDown('medium');
    }
    else {
        $(showPaletteValues).slideUp('medium');
    }
});

$(document).ready(function () {
    $('.content-field[data-type="color_picker"]').find('input.show_palette:checked').trigger('change');
});