$(function () {
    $(".block-designer-container .block-types-order").sortable();
    $('select[name="ft_sort"]').trigger('change');
    $('select.ft_hide').removeClass('form-control').select2();
    $('.ft_hide').css('width', '100%');
    $('select.ft_hide').trigger('change');
});

$(document).on('change', 'select.ft_hide', function () {
    var me = this;
    var value = $(me).val();
    $('.block-types-order a.btn').removeClass('btn-danger').addClass('btn-primary');
    $.each(value, function (i, v) {
        $('.block-types-order a.btn[data-attr-handle="' + v + '"]').addClass('btn-danger').removeClass('btn-primary');
    });
});

$(document).on('change', 'select[name="ft_sort"]', function () {
    var me = this;
    var value = $(me).val();
    var div = $('.form-group-custom-order');
    switch (value) {
        case 'uksort':
        case 'usort':
            $(div).slideDown('medium');
            break;
        default:
            $(div).slideUp('medium');
            break;
    }
});