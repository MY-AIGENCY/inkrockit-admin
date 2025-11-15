$(function(){
    
    /*
     * Date range picker for advanced search
     */
    $('input[name=datepick]').daterangepicker({
        arrows:true,
        onChange: function(val){
            $('input[name=datepick_hide]').val(val);
            $('input[name=search_print]').trigger('keyup');
            $('input[name=entry_type]').trigger('change');
            if($.trim(val)){
                $('.clear_search_date').show();
            }else{
                $('.clear_search_date').hide();
            }
        }
    });
})

