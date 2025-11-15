$(function() {

    $('.event_details').click(function() {
        var $this = $(this),
                id = $this.data('id');
        $('.activity_datatable tr').css('background', '#FFF');
        $this.parents('tr').css('background', '#EEE');

        $.post('/admin/users/ajax', {
            'func': 'event_details',
            'id': id
        }, function(data) {
            if (data) {
                $('.details_list').parent().remove();
                $('<tr><td colspan="3" class="details_list">' + data + '</td></tr>').insertAfter($this.parents('tr'));
            }
        });
    });

    $('select[name=user_group_search]').change(function() {
        $('input[name=search_user_print]').keyup();
    });

    $('input[name=search_user_print]').keyup(function(e) {
        var page = 1;
        if (typeof e.originalEvent == 'undefined') {
            page = $('.pagination').find('a.active').text();
        }
        var val = $(this).val();
        $.post('/admin/users/ajax', {
            'func': 'search_user',
            'val': val,
            'group': $('select[name=user_group_search]').val(),
            'page': page
        }, function(data) {
            $('.widget_body').html(data);
            $('body').scrollTop(0);
        });
    });

    //pagination
    $('.pagination.users a').live('click', function() {
        var num = parseInt($(this).text());
        if (num) {
            $('.pagination a').removeClass('active');
            $(this).addClass('active');
            $('input[name=search_user_print]').keyup();
        }
        return false;
    });

});