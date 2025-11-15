$(function(){
    
    /*
     * Add Coating save
     */
    $('#add_coat').click(function() {
        var title = $('input[name=coat_name]').val();
        var abbr = $('input[name=coat_abbr]').val();
        var days = $('input[name=coat_days]').val();
        var price = $('input[name=coat_price]').val();
        var count = $('input[name=coat_count]').val();
        if (title) {
            $.post('/admin/print/ajax', {
                'func': 'add_coat',
                'title': title,
                'abbr': abbr,
                'days': days,
                'active': 1,
                'price[]': price,
                'count[]': count
            }, function(data) {
                if (data.id) {
                    $('#add_coat_field').show();
                    $('.coat_db tr:last').addClass('hide');
                    $('input[type=text][name^=coat_]').val('');

                    var add_html = '<tr><td>\n\
                    <input type="checkbox" name="coating_aval[]" value="' + data.id + '" class="short">\n\
                    <span class="ui-icon ui-icon-pencil pointer right edit_coat" data-id="' + data.id + '"></span>\n\
                    <span class="ui-icon ui-icon-help pointer right qtips" data-hasqtip="true"></span>\n\
                    </td><td class="editable">' + title + '</td></tr>';
                    $(add_html).insertBefore($('.coat_db tr:last'));
                }
            }, 'json');
        }
    });

    /*
     * Add Finishes save
     */
    $('#add_finish').click(function() {
        var title = $('input[name=finish_name]').val();
        var abbr = $('input[name=finish_abbr]').val();
        var days = $('input[name=finish_days]').val();
        var price = $('input[name=finish_price]').val();
        var count = $('input[name=finish_count]').val();
        if (title) {
            $.post('/admin/print/ajax', {
                'func': 'add_finish',
                'title': title,
                'abbr': abbr,
                'days': days,
                'active': 1,
                'price[]': price,
                'count[]': count
            }, function(data) {
                if (data.id) {
                    $('#add_finish_field').show();
                    $('.finish_db tr:last').addClass('hide');
                    $('input[type=text][name^=finish_]').val('');

                    var add_html = '<tr><td>\n\
                    <input type="checkbox" name="finishes_aval[]" value="' + data.id + '" class="short">\n\
                    <span class="ui-icon ui-icon-pencil pointer right edit_finish" data-id="' + data.id + '"></span>\n\
                    <span class="ui-icon ui-icon-help pointer right qtips" data-hasqtip="true"></span>\n\
                    </td>\n\
                    <td class="editable">' + title + '</td></tr>';
                    $(add_html).insertBefore($('.finish_db tr:last'));
                }
            }, 'json');
        }
    });
    
});