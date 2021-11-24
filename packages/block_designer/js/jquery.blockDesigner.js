(function ($) {
    var _PLUGIN_ = 'blockDesigner';
    var _VERSION_ = '2.1.2';

    $.fn[_PLUGIN_] = function (options) {
        var mainElement = this;
        var defaults = {
            contentField: '.content-field',
            contentFields: '.content-fields',
            fieldTypes: '.field-types'
        };
        var settings = $.extend({}, defaults, options);
        var contentFields = $(mainElement).find(settings.contentFields);

        var addField = function (e, template) {
            var type = $(e).attr('data-type');
            var fieldType = $(mainElement).find(settings.fieldTypes + ' li a[data-type="' + type + '"]');
            var id = rowID();
            var data = {
                "id": id,
                "type": type,
                'type_name': $(fieldType).html(),
                "base_fields": $(fieldType).attr('data-use-base-fields'),
                "can_repeat": $(fieldType).attr('data-use-can-repeat')
            };
            var hbTemplate = Handlebars.compile($(template).html());
            $(contentFields).append(hbTemplate(data));
            var newField = $(contentFields).find(settings.contentField + '[data-id="' + id + '"]');
            $(newField).trigger('create');
            $(newField).find(".launch-tooltip").tooltip();
            $(newField).hide().slideDown('medium').find('input, textarea, select').filter(':visible:first').focus();
            updateFields();
            $('html, body').animate({
                scrollTop: $(newField).offset().top - $('#ccm-toolbar').height() - 10
            }, 1000);
        };

        var deleteField = function (e) {
            $(e).hide();
            $(e).parents(settings.contentField).slideUp('medium', function () {
                $(this).trigger('remove').remove();
                updateFields();
            });
        };

        $(mainElement).on('change', settings.contentField + ' .content-field-label', function () {
            var me = this;
            var contentField = $(me).parents(settings.contentField);
            var contentFieldHeader = $(contentField).find('.header');
            var value = $(me).val();
            var label = $(contentFieldHeader).find('.label');
            $(label).html(value);
            if ($.trim(value) != '') {
                $(label).removeClass('hidden');
            }
            else {
                $(label).addClass('hidden');
            }
        });

        var fillFields = function (e, template) {
            if ($(template).length > 0 && $(e).length > 0) {
                var jsonString = $(e).attr('data-attr-content');
                var data = $.parseJSON(jsonString);
                if ($.isPlainObject(data)) {
                    var fields = [];
                    $.each(data.order, function (i, value) {
                        var field = data.fields[value];
                        if (field != undefined) {
                            var field_type = $(mainElement).find(settings.fieldTypes + ' li a[data-type="' + field.type + '"]');
                            field.id = value;
                            field.type_name = $(field_type).html();
                            field.base_fields = $(field_type).attr('data-use-base-fields');
                            field.can_repeat = $(field_type).attr('data-use-can-repeat');
                            fields.push(field);
                        }
                    });
                    $.each(fields, function (i, v) {
                        var hbTemplate = Handlebars.compile($(template).html());
                        $(contentFields).append(hbTemplate(v));
                        var newField = $(contentFields).find(settings.contentField + '[data-id="' + v.id + '"]');
                        $(newField).find(".launch-tooltip").tooltip();
                        $(newField).trigger('create');
                    });
                    $(contentFields).trigger('complete');
                    updateFields();
                }
            }
        };

        var updateFields = function () {
            var contentFieldsArray = $(contentFields).find(settings.contentField);
            if ($(contentFieldsArray).length <= 0) {
                $(mainElement).find('button.make-block').attr('disabled', 'disabled');
                $(contentFields).find('> .alert').removeClass('hidden');
                $(mainElement).find('.content-fields-links').addClass('hidden');
            }
            else {
                $(mainElement).find('button.make-block').prop('disabled', false);
                $(contentFields).find('> .alert').addClass('hidden');
                $(mainElement).find('.content-fields-links').removeClass('hidden');
                var hasRepeatable = false;
                $.each($(contentFieldsArray), function () {
                    if($(this).attr('data-type') == 'repeatable'){
                        hasRepeatable = true;
                    }
                });
                var canRepeats = $(contentFields).find(settings.contentField + ' .form-group-can_repeat');
                if(hasRepeatable){
                    $(canRepeats).slideDown('medium');
                }
                else {
                    $(canRepeats).slideUp('medium');
                }
            }
            $(contentFields).find('.delete, .fa-info[title]').tooltip({placement: 'right'});
        };

        var rowID = function () {
            var ids = new Array();
            $(contentFields).find(settings.contentField).each(function () {
                ids.push(parseInt($(this).attr('data-id')));
            });
            if (ids.length == 0) {
                ids.push(0);
            }
            return Math.max.apply(Math, ids) + 1;
        };

        return {
            fillFields: fillFields,
            updateFields: updateFields,
            addField: addField,
            deleteField: deleteField,
            rowID: rowID
        };
    };
})(jQuery);