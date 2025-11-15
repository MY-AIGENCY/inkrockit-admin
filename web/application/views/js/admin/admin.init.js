var stop_load = 0, stop_val = 0;
var last_click = 0;

$(function() {

    tinymce.init({
        selector: ".tinimce",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    });


    /*
     * Fix double ckick by table (when load task details)
     */
    $('td').live('click', function(event) {
        if (event.timeStamp - last_click < 300) {
            event.stopImmediatePropagation();
        }
        last_click = event.timeStamp;
    });

    $('body').keyup(function(e) {
        if (e.keyCode === 27) {
            $('.close_modal').trigger('click');
        }
    });

    /*
     * Save and next, Save buttons - validation form
     */
    $('input[name=save_and_next],input[name=just_save]').click(function() {
        var err = '';
        if (!$.trim($('input[name=title]').val())) {
            err = 'Title field is empty';
        } else if (!$('select[name=category]').val()) {
            err = 'Select a category';
        }
        if (err) {
            $('.msg_Error').show();
            $('.msg_Error p').text(err);
            return false;
        } else if ($('.submit_block').length > 0) {
            $('.submit_block').css('opacity', 0);
            $('<img src="/images/admin/info-loader.gif">').insertBefore('.submit_block');
        }
    });

    /*
     * Advanced search - checkbox groups change
     */
    $('h4 input[data-group!=""]').live('change', function() {
        var group = $(this).data('group');
        if ($(this).is(':checked')) {
            $('input[data-group="' + group + '"]').attr('checked', 'checked');
        } else {
            $('input[data-group="' + group + '"]').removeAttr('checked');
        }
        $('input[data-group="' + group + '"]').last().change();
    });

    /*
     * Delete item images (confirmation)
     */
    $('form[name=item_edit] a.delete').live('click', function(e) {
        e.preventDefault();
        if (confirm('Delete this image?')) {
            var img = $(this).data('img');
            $this = $(this);
            $.post('/ajax/index', {
                'fn': 'delete_item_img',
                'img': img
            }, function(data) {
                $this.parent().remove();
            }, 'json');
        }
    });

    /*
     * Save and next - validation form for item
     */
    $('form[name=item_edit] input[name=save_and_next]').live('click', function(e) {
        var cat = $('select[name=cat] option:selected').val();
        if (cat === 0) {
            alert('Please select a category');
            e.preventDefault();
        }
    });


    /* --- Print --- */

    /*
     * Show Add price form
     */
    $('.add_coat_price').click(function() {
        $('.prices_db tbody').append('<tr><td><input type="text" name="count[]" class="short"></td>\n\
        <td><input type="text" name="price[]" class="short"></td><td class="iconsweet">\n\
        <a class="tip_north remove_coat">X</a></td></tr>');
    });

    /*
     * Remove price row
     */
    $('.remove_coat').live('click', function() {
        $(this).parent().parent().remove();
    });

    /*
     * Show/hide Create Fedex Shipment/Edit for requests
     */
    $('input[name^=fedex]').live('change', function() {
        if ($('input[name^=fedex]:checked').length > 0) {
            $('.buttons_block').show();
        } else {
            $('.buttons_block').hide();
        }
    });

    /*
     * Create Fedex Shipment
     */
    $('#fedex_sent').live('click', function() {
        var all = [];
        $.each($('input[name^=fedex]:checked'), function(i) {
            all[i] = $(this).val();
        });
        $.post('/admin/print/ajax', {
            'func': 'reg_send_fedex',
            'items': all
        }, function(data) {
            if (data === 'location') {
                document.location = '/admin/sales/fedex';
            }
        });
    });

    /*
     * Search by Print requests
     */
    $('input[name=search_print]').live('keyup', function() {
        var $this = $(this),
                val = $('input[name=search_print]').val(),
//                get_new = $('input[name=search_print]').data('new'),
                page = $('input[name=page_sel]').val(),
                value = $('input[name=datepick_hide]').val();

        stop_load += 1; //loading pause control
        setTimeout(function() {
            stop_val += 1;
            if (stop_load === stop_val) {
                stop_load = 0;
                stop_val = 0;

                $('.loadz').remove();
                $('<img src="/images/admin/load6.gif" class="left loadz">').insertAfter($this);

                $('#print_table').html('');

                if (value == '[object Object]') {
                    value = '';
                }
                if (!page) {
                    page = 0;
                }
                $.post('/admin/print/ajax/?page=' + page, {
                    'func': 'search_print',
                    'val': val,
//                    'new': get_new,
                    'fields': $('input[name^=print_field]:checked').serialize(),
                    'date': value
                }, function(data) {
                    $('.buttons_block').hide();
                    $('.loadz').remove();
                    $('#print_table').html(data);
                    $("#sortTable").tablesorter({
                        headers: {
                            0: {
                                sorter: false
                            },
                            9: {
                                sorter: false
                            },
                            10: {
                                sorter: false
                            }
                        }
                    });

                    //get count my note tasks
                    $.post('/admin/sales/ajax', {
                        'func': 'get_count_my_tasks'
                    }, function(data) {
                        if (data.count && data.count > 0) {
                            $('a[href="/admin/Sales/current_jobs"]').html('My Tasks (<span class="task_counts">' + data.count + '</span>)')
                        }
                        if (data.count_assign && data.count_assign > 0) {
                            $('a[href="/admin/Sales/assigned_tasks"]').html('Assigned Tasks (<span class="task_assigned_counts">' + data.count_assign + '</span>)')
                        }
                    }, 'json');
                });
            }
        }, 700);
    });

    /*
     * Advanced search. Change search criteria
     */
    $('input[name^=print_field]').change(function() {
        $('.disable_advanced_search').attr('checked', 'checked').change();
    });

    /*
     * Advanced search: disabled/enabled
     */
    $('.disable_advanced_search').live('change', function() {
        var val = ($(this).is(":checked")) ? 0 : 1;
        $.post('/admin/print/ajax', {
            'func': 'use_advanced_search',
            'val': val
        }, function() {
            $('input[name=search_print]').keyup();
        });
    });

    /*
     * Print Search Pages
     */
    $('.ajax_pagin a').live('click', function() {
        page = $(this).text();
        if ($(this).css('opacity') < '0.5') {
            return false;
        }
        if (page == '…') {
            page = parseInt($(this).parent().prev().text()) + 1;
        } else if (page == '<') {
            page = parseInt($('.active').parent().prev().text());
        } else if (page == '>') {
            page = parseInt($('.active').parent().next().text());
        }
        $('input[name=page_sel]').val(page);
        $('input[name=search_print]').keyup();
        $('input[name=check_all]').removeAttr('checked');
        return false;
    });

    /*
     * Edit checked Print requests
     */
    $('#edit_print_list').live('click', function() {
        var all = [];
        $.each($('input[name^=fedex]:checked'), function(i) {
            all[i] = $(this).val();
        });
        $.post('/admin/print/ajax', {
            'func': 'send_edit_print',
            'items': all
        }, function(data) {
            if (data === 'location') {
                document.location = '/admin/sales/all/edit/';
            }
        });
    });

    //Confirm request without sending request
    $('#confirm_without_req').live('click', function() {
        var all = [];
        $.each($('input[name^=fedex]:checked'), function(i) {
            all[i] = $(this).val();
        });
        $.post('/admin/print/ajax', {
            'func': 'confirm_without_req',
            'items': all
        }, function(data) {
            if (data === 'location') {
                document.location = '/admin/sales/all/';
            }
        });
    });

    /*
     * Add Papers form
     */
    $('#add_paper_field').click(function() {
        $('.paper_db tr:last').removeClass('hide');
        $(this).hide();
    });

    /*
     * Add Papers save
     */
    $('#add_paper').click(function() {
        var name = $('input[name=paper_name]').val();
        var description = $('input[name=paper_description]').val();
        var price = $('input[name=paper_price]').val();
        var count = $('input[name=paper_count]').val();
        if (name && price && count) {
            $.post('/admin/print/ajax', {
                'func': 'add_paper',
                'name': name,
                'price[]': price,
                'count[]': count,
                'description': description
            }, function(data) {
                if (data.id) {
                    $('#add_paper_field').show();
                    $('.paper_db tr:last').addClass('hide');
                    $('input[name=paper_name]').val('');
                    $('input[name=paper_description]').val('');
                    $('input[name=paper_price]').val('');
                    $('input[name=paper_count]').val('');
                    var add_html = '<tr><td><input type="checkbox" name="use_paper[]" value="' + data.id + '" class="short">\n\
                <span class="ui-icon ui-icon-pencil pointer right edit_paper" data-id="' + data.id + '"></span>\n\
                <span class="ui-icon ui-icon-help pointer right qtips" title="' + description + '"></span></td>\n\
                <td>' + name + '</td>';
                    if ($('input[name=paper_default]').length > 0) {
                        add_html = add_html + '<td><input type="radio" name="default" value="' + data.id + '"></td>';
                    }
                    add_html = add_html + '<tr>';
                    $(add_html).insertBefore($('.paper_db tr:last'));
                }
            }, 'json');
        }
    });

    /*
     * Edit Papers form
     */
    $('.edit_paper').live('click', function() {
        var $this = $(this),
                id = $this.data('id'),
                text = $this.parent().next().text(),
                price = $this.parent().next().next().text();
        $this.parent().next().html('<input type="text" name="paper_title" data-id="' + id + '" value="' + text + '" style="width:90%">');
        $this.parent().next().next().html('<input type="text" name="paper_price" data-id="' + id + '" value="' + price + '" style="width:50px">');
        $this.removeClass('edit_paper').removeClass('ui-icon-pencil').addClass('ui-icon-circle-check').addClass('save_paper');
    });

    /*
     * Update Paper
     */
    $('.save_paper').live('click', function() {
        $this = $(this);
        var id = $this.data('id'),
                text = $('input[name=paper_title][data-id="' + id + '"]').val(),
                price = $('input[name=paper_price][data-id="' + id + '"]').val();
        $.post('/admin/print/ajax', {
            'func': 'save_paper',
            'id': id,
            'name': text,
            'price': price
        }, function() {
            $this.removeClass('ui-icon-circle-check').removeClass('save_paper').addClass('edit_paper').addClass('ui-icon-pencil');
            $this.parent().next().text(text);
            $this.parent().next().next().text(price);
        }, 'json');
    });

    /*
     * Change Default
     */
    $('input[type=radio][name$=_default], input[type=radio][name$=_def]').change(function() {
        if ($(this).prev().is('input')) {
            $(this).prev().attr('checked', 'checked');
        } else {
            $(this).parents('tr').find('input[type=checkbox]').attr('checked', 'checked');
        }
    });

    /*--- Scroll ---*/

    /*
     * Get scroll coordinates
     * @returns {Array}
     */
    var getScroll = function() {
        if (window.pageYOffset != undefined) {
            return [pageXOffset, pageYOffset];
        }
        else {
            var sx, sy, d = document, r = d.documentElement, b = d.body;
            sx = r.scrollLeft || b.scrollLeft || 0;
            sy = r.scrollTop || b.scrollTop || 0;
            return [sx, sy];
        }
    };

    /*
     * Add fixed position for request search field
     */
    $(window).scroll(function() {
        if ($('.form_fields_container').length > 0) {
            var pos = getScroll();
            if (pos[1] > 100) {
                $('.search_block').addClass('scroll_fixed_pos');
                $('.search_block input[name=check_all]').show();
                $('.block_save').fadeIn();
            } else {
                $('.search_block').removeClass('scroll_fixed_pos');
                $('.search_block input[name=check_all]').hide();
                $('.block_save').fadeOut();
            }
        }
    });

    /*-------- Inspiration Station --------*/
    /*
     * Show Edit/Remove buttons when check some inspiration item
     */
    $('input[name^=edit_insp]').live('change', function() {
        if ($('input[name^=edit_insp]:checked').length > 0) {
            $('.edit_insp_sent').show();
            $('.rem_insp_sent').show();
        } else {
            $('.edit_insp_sent').hide();
            $('.rem_insp_sent').hide();
        }
    });

    /*
     * Run editing items
     */
    $('.edit_insp_sent').live('click', function() {
        var all = [];
        $.each($('input[name^=edit_insp]:checked'), function(i) {
            all[i] = $(this).val();
        });
        $.post('/admin/inspiration/ajax', {
            'func': 'send_edit_insp',
            'items': all
        }, function(data) {
            if (data === 'location') {
                document.location = '/admin/inspiration/items/edit/';
            }
        });
    });

    /*
     * Check All functionality
     */
    $('input[name=check_all]').live('change', function() {
        if ($(this).is(':checked')) {
            var find_checked = false;
            $.each($('table input[type="checkbox"][data-check=all]:checked'), function(i, v) {
                find_checked = true;
            });
            if (find_checked) {
                $('input[name=check_all]').removeAttr('checked');
                $.each($('table input[type="checkbox"][data-check=all]'), function(i, v) {
                    $(this).removeAttr('checked');
                });
            } else {
                $('input[name=check_all]').attr('checked', 'checked');
                $.each($('table input[type="checkbox"][data-check=all]'), function(i, v) {
                    $(this).attr('checked', 'checked');
                });
            }
        } else {
            $.each($('table input[type="checkbox"][data-check=all]'), function(i, v) {
                $(this).removeAttr('checked');
            });
            $('input[name=check_all]').removeAttr('checked');
        }
        $('input[type="checkbox"]').last().change();
    });

    /*
     * Confirm removing 
     */
    $('.ui-icon-closethick').live('click', function() {
        if ($(this).attr('href')) {
            if (!confirm('Confirm removing')) {
                return false;
            }
        }
    });

    /*
     * Remove inspiration item
     */
    $('.rem_insp_sent a').live('click', function() {
        if (confirm('Remove selected items?')) {
            var rem = [];
            $.each($('input[name=edit_insp]:checked'), function(i) {
                rem[i] = $(this).attr('data-id');
            });
            $.post('/admin/inspiration/items/remove', {
                'id': rem
            }, function() {
                document.location = document.URL;
            });
        } else {
            return false;
        }
    });

    /*
     * Search by inspiration items when change search field
     */
    $('input[name=search_insp]').keyup(function() {
        $('.loadz').remove();
        var val = $('input[name=search_insp]').val();
        $('.insp_pagination').fadeOut();
        $('<img src="/images/admin/load6.gif" class="left loadz">').insertAfter($(this));
        $.post('/admin/inspiration/ajax/', {
            'func': 'search_insp',
            'val': val,
            'fields': $('input[name^=insp_field]:checked').serialize()
        }, function(data) {
            $('.loadz').remove();
            $('.widget_body').html(data);
            $("#sortTable").tablesorter({
                headers: {
                    0: {
                        sorter: false
                    },
                    4: {
                        sorter: false
                    },
                    10: {
                        sorter: false
                    }
                }
            });
        });
    });

    /*
     * Search by inspiration items when change additional fields
     */
    $('input[name^=insp_field]:checked').change(function() {
        $('input[name=search_insp]').trigger('keyup');
    });

    /*
     * Fedex send. Change Service type
     */
    $('select[name=serviceType]').change(function() {
        if ($(this).val() === 'SMART_POST') {
            $('.smart_detail').removeClass('hide');
        } else {
            $('.smart_detail').addClass('hide');
        }
    });

    /*
     * Show advanced search
     */
    $('#adv_search_show').click(function() {
        $('.search_menu').removeClass('hide');
    });

    /*
     * Hide advanced search
     */
    $('.adv_menu').mouseleave(function() {
        $('.search_menu').addClass('hide');
    });


    /* ------ Print ------ */

    /*
     * Remove print item " Small Preview" image
     */
    $('#remove_preview').live('click', function() {
        var id = $(this).data('id');
        $.post('/admin/print/ajax', {
            'func': 'remove_preview',
            'id': id
        });
        $(this).parent().remove();
    });

    /*
     * Remove print item "Active Preview" image
     */
    $('#remove_act_preview').live('click', function() {
        var id = $(this).data('id');
        $.post('/admin/print/ajax', {
            'func': 'remove_act_preview',
            'id': id
        });
        $(this).parent().remove();
    });

    /*
     * Remove print item "Product View" image
     */
    $('#remove_view').live('click', function() {
        var id = $(this).data('id');
        $.post('/admin/print/ajax', {
            'func': 'remove_view',
            'id': id
        });
        $(this).parent().remove();
    });

    /*
     * Remove print item "PSD" image
     */
    $('#remove_psd').live('click', function() {
        var id = $(this).data('id');
        $.post('/admin/print/ajax', {
            'func': 'remove_psd',
            'id': id
        });
        $(this).parent().remove();
    });


    /*
     * Fast edit inks in print add/edit product
     */
    $('.edit_ink').live('click', function() {
        var $this = $(this),
                id = $this.data('id'),
                text = $(this).parents('tr').find('.editable').text();
        $(this).parents('tr').find('.editable').html('<input type="text" name="ink_title" data-id="' + id + '" value="' + text + '" style="width:90%">');
        $this.removeClass('edit_ink').removeClass('ui-icon-pencil').addClass('ui-icon-circle-check').addClass('save_ink');
    });

    /*
     * Save fast edited inks in print add/edit product
     */
    $('.save_ink').live('click', function() {
        $this = $(this);
        var id = $this.data('id'),
                text = $('input[name=ink_title][data-id="' + id + '"]').val();
        $.post('/admin/print/ajax', {
            'func': 'save_ink',
            'id': id,
            'name': text
        }, function() {
            $this.removeClass('ui-icon-circle-check').removeClass('save_ink').addClass('edit_ink').addClass('ui-icon-pencil');
            $this.parents('tr').find('.editable').text(text);
        }, 'json');
    });

    /*
     * Fast edit finishes in print add/edit product
     */
    $('.edit_finish').live('click', function() {
        var id = $(this).data('id'),
                text = $(this).parents('tr').find('.editable').text();
        $(this).parents('tr').find('.editable').html('<input type="text" name="finish_title" data-id="' + id + '" value="' + text + '" style="width:90%">');
        $(this).removeClass('edit_finish').removeClass('ui-icon-pencil').addClass('ui-icon-circle-check').addClass('save_finish');
    });

    /*
     * Add Coating form
     */
    $('#add_coat_field').click(function() {
        $('.coat_db tr:last').removeClass('hide');
        $(this).hide();
    });

    /*
     * Add Finishes form
     */
    $('#add_finish_field').click(function() {
        $('.finish_db tr:last').removeClass('hide');
        $(this).hide();
    });

    /*
     * Save fast edited finishes in print add/edit product
     */
    $('.save_finish').live('click', function() {
        var $this = $(this),
                id = $this.data('id'),
                text = $('input[name=finish_title][data-id="' + id + '"]').val();
        $.post('/admin/print/ajax', {
            'func': 'save_finish',
            'id': id,
            'title': text
        }, function() {
            $this.removeClass('ui-icon-circle-check').removeClass('save_finish').addClass('edit_finish').addClass('ui-icon-pencil');
            $this.parents('tr').find('.editable').text(text);
        }, 'json');
    });

    /*
     * Fast edit finishes in print add/edit product
     */
    $('.edit_proof').live('click', function() {
        var id = $(this).data('id'),
                text = $(this).parent().next().text();
        $(this).parent().next().html('<input type="text" name="proof_title" data-id="' + id + '" value="' + text + '" style="width:90%">');
        $(this).removeClass('edit_proof').removeClass('ui-icon-pencil').addClass('ui-icon-circle-check').addClass('save_proof');
    });

    /*
     * Save fast edited finishes in print add/edit product
     */
    $('.save_proof').live('click', function() {
        var $this = $(this),
                id = $this.data('id'),
                text = $('input[name=proof_title][data-id="' + id + '"]').val();
        $.post('/admin/print/ajax', {
            'func': 'save_proof',
            'id': id,
            'title': text
        }, function() {
            $this.removeClass('ui-icon-circle-check').removeClass('save_proof').addClass('edit_proof').addClass('ui-icon-pencil');
            $this.parent().next().text(text);
        }, 'json');
    });

    /*
     * Fast edit coating in print add/edit product
     */
    $('.edit_coating').live('click', function() {
        var id = $(this).data('id'),
                text = $(this).parents('tr').find('.editable').text();
        $(this).parents('tr').find('.editable').html('<input type="text" name="coat_title" data-id="' + id + '" value="' + text + '" style="width:90%">');
        $(this).removeClass('edit_coating').removeClass('ui-icon-pencil').addClass('ui-icon-circle-check').addClass('save_coating');
    });

    /*
     * Save fast edited coating in print add/edit product
     */
    $('.save_coating').live('click', function() {
        var $this = $(this),
                id = $this.data('id'),
                text = $('input[name=coat_title][data-id="' + id + '"]').val();
        $.post('/admin/print/ajax', {
            'func': 'save_coating',
            'id': id,
            'title': text
        }, function() {
            $this.removeClass('ui-icon-circle-check').removeClass('save_coating').addClass('edit_coating').addClass('ui-icon-pencil');
            $this.parents('tr').find('.editable').text(text);
        }, 'json');
    });


    /*
     * When change print sort field - remove additional blocks
     */
    $('table.activity_datatable .header').live('click', function() {
        $('.task_details').remove();
        $('.edit_industry').remove();
    });


    /*
     * PPT bugs. Add bug - validation
     */
    $('input[name=bug_add]').click(function() {
        var title = $.trim($('input[name=title]').val()),
                description = $.trim($('textarea[name=text]').val());
        if (!title) {
            $('.error').text('Field "Title" is empty!');
            return false;
        } else if (!description) {
            $('.error').text('Field "Description" is empty!');
            return false;
        }
    });

});
