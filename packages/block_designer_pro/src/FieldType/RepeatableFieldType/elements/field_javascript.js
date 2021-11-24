$(document).on('keyup', '.content-field[data-type="repeatable"] input[name$="][label]"]', function () {
    var value = $(this).val();
    var contentField = $(this).parents('.content-field');
    var id = $(contentField).attr('data-id');
    var label = $.trim(value) != '' ? value + ' - #' + id : '#' + id;
    $.each($('.select-repeatable'), function () {
        $(this).find('option[value="' + id + '"]').html(label);
    });
});

$(document).on('keyup', '.content-field[data-type="text_box"] input[name$="][label]"]', function () {
    var value = $(this).val();
    var contentField = $(this).parents('.content-field');
    var id = $(contentField).attr('data-id');
    var label = $.trim(value) != '' ? value + ' - #' + id : '#' + id;
    $.each($('select.title_me'), function () {
        $(this).find('option[value="' + id + '"]').html(label);
    });
});

$(document).on('remove', '.content-field[data-type="repeatable"]', function () {
    var id = $(this).attr('data-id');
    $.each($('.select-repeatable'), function () {
        $(this).find('option[value="' + id + '"]').remove();
    });
});

$(document).on('remove', '.content-field[data-type="text_box"]', function () {
    var id = $(this).attr('data-id');
    $.each($('select.title_me'), function () {
        $(this).find('option[value="' + id + '"]').remove();
    });
});

$(document).on('create', '.content-field', function () {
    var me = this;
    if ($(me).attr('data-type') == 'repeatable') {
        var id = $(me).attr('data-id');
        var value = $(me).find('input[name="fields[' + id + '][label]"]').val();
        var label = $.trim(value) != '' ? value + ' - #' + id : '#' + id;
        var titleMe = $(me).find('select.title_me');
        var titleMeValueSelected = $(titleMe).attr('data-value');
        $.each($('.content-field'), function () {
            if ($(this).attr('data-type') == 'text_box') {
                var titleMeID = $(this).attr('data-id');
                var titleMeValue = $(this).find('input[name="fields[' + id + '][label]"]').val();
                var titleMeLabel = $.trim(titleMeValue) != '' ? titleMeValue + ' - #' + titleMeID : '#' + titleMeID;
                var titleMeSelected = titleMeValueSelected == titleMeID ? 'selected="selected"' : '';
                $(titleMe).append('<option value="' + titleMeID + '" ' + titleMeSelected + '>' + titleMeLabel + '</option>');
            }
            var select = $(this).find('.select-repeatable');
            if ($(select).length > 0) {
                $(select).append('<option value="' + id + '">' + label + '</option>');
                $('.content-field select.select-repeatable[data-value="' + id + '"]').val(id);
            }
        });
    }
    else {
        var select = $(me).find('.select-repeatable');
        if ($(select).length > 0) {
            $.each($('.content-field[data-type="repeatable"]'), function () {
                var id = $(this).attr('data-id');
                var value = $(this).find('input[name="fields[' + id + '][label]"]').val();
                var label = $.trim(value) != '' ? value + ' - #' + id : '#' + id;
                $(select).append('<option value="' + id + '">' + label + '</option>');
                if (parseInt($(select).attr('data-value')) == parseInt(id)) {
                    $(select).val(id);
                }
                if ($(me).attr('data-type') == 'text_box') {
                    var titleMe = $(this).find('select.title_me');
                    var titleMeValueSelected = $(titleMe).attr('data-value');
                    var titleMeID = $(me).attr('data-id');
                    var titleMeValue = $(me).find('input[name="fields[' + $(me).attr('data-id') + '][label]"]').val();
                    var titleMeLabel = $.trim(titleMeValue) != '' ? titleMeValue + ' - #' + titleMeID : '#' + titleMeID;
                    var titleMeSelected = titleMeValueSelected == titleMeID ? 'selected="selected"' : '';
                    $(titleMe).append('<option value="' + titleMeID + '" ' + titleMeSelected + '>' + titleMeLabel + '</option>');
                }
            });
        }
    }
    $(me).find('.base-fields + .form-group').insertAfter($(me).find('.content-field-options'));
});