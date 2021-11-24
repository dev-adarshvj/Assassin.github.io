(function($){
    $.fn.extend({
        select2_sortable: function(){
            var select = $(this);
            $(select).select2();
            var ul = $(select).prev('.select2-container').first('ul');
            ul.sortable({
                placeholder : 'ui-state-highlight',
                items       : 'li:not(.select2-search-field)',
                tolerance   : 'pointer',
                stop: function() {
                    $($(ul).find('.select2-search-choice').get().reverse()).each(function() {
                        var id = $(this).data('select2Data').id;
                        var option = select.find('option[value="' + id + '"]')[0];
                        $(select).prepend(option);
                    });
                }
            });
        }
    });
}(jQuery));