$(function() {

    $('input[name=panels_count]').change(function() {
        var need = $(this).val();
        $('.pockets_block > div').hide();
        if ($('input[name=have_pockets]').is(':checked')) {
            for (var i = 0; i < need; i++) {
                if ($('.one_block[data-num="' + (i + 1) + '"]').length === 0) {
                    $('.pockets_block').append('<div class="one_block" data-num="' + (i + 1) + '"><h4 style="font-size: 18px">Panel #' + (i + 1) + '</h4>\n\
                    <label class="lab">Count:</label><input type="number" name="pocket_count['+(i + 1)+']" value="1" min="0"><br class="clear">\n\
                    <div class="pocket_options"><label class="lab">Position:</label><select name="pocket_position['+(i + 1)+']"><option value="horizontal">Horizontal</option><option value="vertical">Vertical</option></select><br class="clear">\n\
                    <label class="lab">Type:</label><select name="pack_type['+(i + 1)+']"><option value="">-</option><option value="standard">Standard</option><option value="expandable">Expandable</option><option value="box">Box</option></select><br class="clear">\n\
                    <div class="pack_type"></div></div></div>');
                } else {
                    $('.one_block[data-num="' + (i + 1) + '"]').show();
                }
            }
            $('.pockets_block > div:hidden').remove();
        }
        if ($('input[name=have_slits]').is(':checked')) {
            $('select[name=slit_type]').change();
        }
    });

    $('input[name=have_pockets]').change(function() {
        $('input[name=panels_count]').change();
    });
    
    $('input[name^=pocket_count]').live('change',function(){
        console.log('!')
        var elem_more = $(this).parent().find('.pocket_options');
        if($(this).val() <= 0 ){
            elem_more.hide();
        }else{
            elem_more.show();
            
        }
    });

    $('select[name^=pack_type], select[name^=pocket_position]').live('change', function() {
        var $this = $(this).parents('.one_block'),
                block = $(this).parents('.one_block').find('.pack_type'),
                num = $(this).parents('.one_block').data('num'),
                type_val = $this.find('select[name^=pack_type]').val();
                block.html('');
        if (type_val) {
            block.html('<img src="/images/admin/load6.gif">');
            $.post('/admin/print/ajax', {
                'func': 'load_pockets',
                'type': type_val,
                'num': num,
                'position': $this.find('select[name^=pocket_position]').val(),
                'width': $('input[name=panels_width]').val(),
                'height': $('input[name=panels_height]').val(),
                'total_count': $('input[name=panels_count]').val()
            }, function(data) {
                block.html(data);
            });
        }
        if ($('input[name=have_slits]').is(':checked')) {
            $('select[name=slit_type]').change();
        }
    });

    $('input[name^=pack_default],input[name^=slits_default]').live('click', function() {
        $(this).parents('tr').find('input[type=checkbox]').attr('checked', 'checked');
    });


    /*----- Slits -----*/

    $('input[name=have_slits]').change(function() {
        if ($(this).is(':checked')) {
            $('#slits_container').show();
        } else {
            $('#slits_container').hide();
            $('select[name=slit_type]').removeAttr('selected').find('option').first().attr('selected', 'selected').change();
        }
    });

    $('select[name=single_position]').live('change', function() {
        if ($(this).val()) {
            $('.slits_table').show();
        } else {
            $('.slits_table').hide();
        }
    });

    $('.multiple_slit').live('change', function() {
        if ($(this).is(':checked')) {
            $(this).parent().next().show();
        } else {
            $(this).parent().next().hide();
        }
    });

    $('select[name=slit_type]').change(function() {
        var val = $(this).val(),
                elements = parseInt($('input[name=panels_count]').val()),
                aval_pockets = [];
        $.each($('select[name^=pack_type]:visible'), function(i) {
            if ($(this).val()) {
                aval_pockets[i] = $(this).parents('.one_block').data('num');
            }
        });

        if (elements && val === 'multiple') {
            $('.slit_type_block').html('');
            for (var i = 0; i < elements; i++) {
                $('.slit_type_block').append('<div class="col2" data-type="panel" data-num="' + (i + 1) + '"><h5><input class="multiple_slit" type="checkbox" name="multiple[panel][' + (i + 1) + ']"> Panel #' + (i + 1) + '</h5><div class="slits_table hide"></div></div>');
            }
            $('.slit_type_block').append('<br class="clear"><hr><br class="clear">');
            //Pockets
            $.each(aval_pockets, function(i, v) {
                if (v) {
                    $('.slit_type_block').append('<div class="col2" data-type="pocket" data-num="' + v + '"><h5><input class="multiple_slit" type="checkbox" name="multiple[pocket][' + v + ']"> Pocket #' + v + '</h5><div class="slits_table hide"></div></div>');
                }
            });

            $.each($('.col2[data-type="panel"]'), function(i) {
                var $this = $(this);
                $.post('/admin/print/ajax', {
                    'func': 'get_slits_table',
                    'num': (i + 1),
                    'type': 'panel'
                }, function(data) {
                    $this.find('.slits_table').html(data);
                });
            });
            $.each($('.col2[data-type="pocket"]'), function(i) {
                var $this = $(this);
                $.post('/admin/print/ajax', {
                    'func': 'get_slits_table',
                    'num': $this.data('num'),
                    'type': 'pocket'
                }, function(data) {
                    $this.find('.slits_table').html(data);
                });
            });

        } else if (elements && val === 'single') {
            $('.slit_type_block').html('<label style="width: 100px">Position:</label><select name="single_position"></select><br><div class="slits_table"></div>');
            $('select[name=single_type]').append('<option value="">-</option>');
            //Panels
            for (var i = 0; i < elements; i++) {
                $('select[name^=single_position]').append('<option value="panel-' + (i + 1) + '">Panel #' + (i + 1) + '</option>');
            }
            //Pockets
            $.each(aval_pockets, function(i, v) {
                if (v) {
                    $('select[name^=single_position]').append('<option value="pocket-' + v + '">Pocket #' + v + '</option>');
                }
            });
            //load table
            $.post('/admin/print/ajax', {
                'func': 'get_slits_table',
                'num': 1
            }, function(data) {
                $('.slits_table').html(data);
            });
        } else {
            if (!val) {
                $('.slit_type_block').text('');
            } else {
                $('.slit_type_block').text('*No Panels');
            }
        }
    });


    /*--- Add/Edit Product ---*/

    $('.print_edit_page').live('click', function() {
        var cat = $('select[name=category] :selected').val();
        var err = '';
        if (!cat) {
            err = 'Select a Product';
            $('a[href="#tabs-1"]').trigger('click');
        } else if ($('input[name=paper_default]:checked').length == 0) {
            err = 'Select default Paper';
            $('a[href="#tabs-3"]').trigger('click');
        } else if ($('input[name=inks1_default]:checked').length == 0) {
            err = 'Select default Inks - Side 1';
            $('a[href="#tabs-4"]').trigger('click');
        } else if ($('input[name=inks2_default]:checked').length == 0) {
            err = 'Select default Inks - Side 2';
            $('a[href="#tabs-4"]').trigger('click');
        } else if ($('input[name=finish2_def]:checked').length == 0) {
            err = 'Select default Finishes - Side 2';
            $('a[href="#tabs-5"]').trigger('click');
        } else if ($('input[name=finish1_def]:checked').length == 0) {
            err = 'Select default Finishes - Side 1';
            $('a[href="#tabs-5"]').trigger('click');
        } else if ($('input[name=coating1_def]:checked').length == 0) {
            err = 'Select default Coating - Side 1';
            $('a[href="#tabs-6"]').trigger('click');
        } else if ($('input[name=coating2_def]:checked').length == 0) {
            err = 'Select default Coating - Side 2';
            $('a[href="#tabs-6"]').trigger('click');
        } else if ($('input[name=proof_default]:checked').length == 0) {
            err = 'Select default Proof';
            $('a[href="#tabs-9"]').trigger('click');
        } 
        if ($('input[name=sticked]').is(':checked')) {
            if ($('input[name=stick_finish_def]:checked').length == 0) {
                err = 'Select default Sticked Finishes';
                $('a[href="#tabs-7"]').trigger('click');
            }
        }
        if (err) {
            $('.msg_Error').show().children().text(err);
            $("html, body").animate({scrollTop: 0}, "slow");
            return false;
        } else {
            $('.submit_block').css('opacity', 0);
            $('<img src="/images/admin/info-loader.gif">').insertBefore('.submit_block');
        }
    });

    $('#add_dimention').click(function() {
        $('#dimmention_form').append('<div><hr><label><span class="ui-icon ui-icon-minus left pointer remove_dim"></span>Name:</label><input type="text" name="dim_name[]" class="left"><br class="clear"><label>Width:</label><input type="text" name="dim_width[]" class="left small"><label>Height:</label><input type="text" name="dim_height[]" class="left small"><label>ABBR:</label><input type="text" name="dim_abbr[]" class="left small"><br class="clear"><label class="left">Description:</label><textarea name="dim_descr[]" style="width: 496px !important; float: left"></textarea><br class="clear"></div>')
    });

    $('.remove_dim').live('click', function() {
        $(this).parent().parent().remove()
    });

    $('input[name=sticked]').change(function() {
        if ($(this).is(':checked')) {
            $('#stick_block').show();
        } else {
            $('#stick_block').hide();
        }
    });

    $('.ui-tabs-panel input[name=width]').blur(function() {
        var dim = $('input[name^=dim_width]:eq(1)');
        var pan = $('input[name=panels_width]');
        if (!dim.val()) {
            dim.val($(this).val());
        }
        if (!pan.val()) {
            pan.val($(this).val());
        }
    });
    $('.ui-tabs-panel input[name=height]').blur(function() {
        var dim = $('input[name^=dim_height]:eq(1)');
        var pan = $('input[name=panels_height]');
        if (!dim.val()) {
            dim.val($(this).val());
        }
        if (!pan.val()) {
            pan.val($(this).val());
        }
    });


    /*
     * Add category form
     */
    $('#add_category').live('click', function() {
        $(this).replaceWith('<span class="add_category_block"><span style="margin-left: 10px">Add category:<\/span> <input type="text" name="new_category" style="width:206px"> <input type="button" name="add_category" value="Save"></span>');
    });

    /*
     * Add category save
     */
    $('input[name=add_category]').live('click', function() {
        var new_category = $('input[name=new_category]').val();
        var type = $('select[name=category]').data('type');
        if (new_category) {
            $.post('/admin/' + type + '/ajax', {
                'func': 'add_category',
                'title': new_category
            }, function(data) {
                if (data.ins_id) {
                    $('select[name=category]').append('<option selected="selected" value="' + data.ins_id + '">' + new_category + '</option>');
                    $('.add_category_block').remove();
                    $('select[name=category]').change();
                }
            }, 'json');
        }
    });


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

                    var add_html = '<tr><td><input type="checkbox" style="margin-left:15px" name="coating1_aval[]" value="' + data.id + '"> <input type="radio" name="coating1_def" value="' + data.id + '"></td>\n\
                    <td><input type="checkbox" style="margin-left:15px" name="coating2_aval[]" value="' + data.id + '"> <input type="radio" name="coating2_def" value="' + data.id + '"></td>\n\
                    <td><span class="ui-icon ui-icon-pencil pointer right edit_paper" data-id="' + data.id + '"></span>\n\
                    <span class="ui-icon ui-icon-help pointer right qtips" title="' + abbr + '"></span></td>\n\
                    <td class="editable">' + title + '</td>\n\
                    </tr>';
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

                    var add_html = '<tr><td><input type="checkbox" style="margin-left: 15px" name="finishes1_aval[]" value="' + data.id + '"><input type="radio" name="finish1_def" value="' + data.id + '"></td>\n\
                    <td><input type="checkbox" style="margin-left: 15px" name="finishes2_aval[]" value="' + data.id + '"><input type="radio" name="finish2_def" value="' + data.id + '"></td>\n\
                    <td><span class="ui-icon ui-icon-pencil pointer right edit_finish" data-id="' + data.id + '"></span>\n\
                    <span class="ui-icon ui-icon-help pointer right qtips" title="' + abbr + '"></span></td>\n\
                    <td class="editable">' + title + '</td>\n\
                    </tr>';

                    $(add_html).insertBefore($('.finish_db tr:last'));
                }
            }, 'json');
        }
    });

    /*
     * Add INKS form
     */
    $('#add_inks_field').click(function() {
        $('.inks_db tr:last').removeClass('hide');
        $(this).hide();
    });

    /*
     * Add INKS save
     */
    $('#add_inks').click(function() {
        var name = $('input[name=inks_name]').val();
        var price = $('input[name=inks_price]').val();
        var description = $('input[name=inks_description]').val();
        $.post('/admin/print/ajax', {
            'func': 'add_inks',
            'name': name,
            'price': price,
            'description': description
        }, function(data) {
            if (data.id) {
                $('#add_inks_field').show();
                $('.inks_db tr:last').addClass('hide');
                $('input[type=text][name^=inks_]').val('');

                $('<tr><td><input type="checkbox" style="margin-left: 15px" name="inks1_use[]" value="' + data.id + '"><input type="radio" name="inks1_default" value="' + data.id + '">\n\
                </td><td><input type="checkbox" style="margin-left: 15px" name="inks2_use[]" value="' + data.id + '"><input type="radio" name="inks2_default" value="' + data.id + '">\n\
                </td><td><span class="ui-icon ui-icon-pencil pointer right edit_ink" data-id="' + data.id + '"></span>\n\
                <span class="ui-icon ui-icon-help pointer right qtips" title="' + description + '"></span>\n\
                </td>\n\<td class="editable">' + name + '\n\
                </td><td>' + price + '\n\
                </td></tr>').insertBefore($('.inks_db tr:last'));
            }
        }, 'json');
    });

    /*
     * Check All
     */
    $('input[name=check_all]').live('change', function(){
        if($(this).is(':checked')){
            $(this).parents('.activity_datatable').find('input[type=checkbox]').attr('checked','checked');
        }else{
            $(this).parents('.activity_datatable').find('input[type=checkbox]').removeAttr('checked');
        }
    });
    
    /*
     * Product properties
     */
    $('select[name=category]').change(function(){
        if($(this).val() == 1){
            $('a[href=#tabs-7]').parent().show();
        }else{
            $('a[href=#tabs-7]').parent().hide();
            $('input[name=sticked]').removeAttr('checked').change();
        }
    });

});