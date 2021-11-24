$(function () {
    $(".block-types-sortable").sortable({
        update: function (event, ui) {
            var parent = ui.item.parent();
            var items = $(parent).find('> li');
            var btsID = $(parent).attr('data-btsid');
            var order = new Array();

            $.each(items, function (i, v) {
                var btid = $(this).attr('data-btid');
                order.push(btid);
            });

            $.ajax({
                data: {
                    order: order,
                    btsID: btsID
                },
                dataType: 'json',
                type: 'POST',
                url: CCM_DISPATCHER_FILENAME + '/dashboard/blocks/block_order/update',
                success: function (result) {
                }
            });
        }
    });
});

$(document).on('click', '.block-types-sortable a', function (e) {
    e.preventDefault();
});