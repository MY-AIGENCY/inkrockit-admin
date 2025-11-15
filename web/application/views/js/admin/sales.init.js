$(function() {

    $("#phone").mask("(999) 999-9999");
    $(".phone_num").mask("(999) 999-9999");
    $("#zip").mask("99999");

    $('body').live('click', function() {
        $('.aval_company_users').hide();
        $('.edit_industry').remove();
    });

    //save_additional_phone
    /*
     *  Load task details for /admin/sales/all
     */
    $('.load_tasks').live('click', function(e) {
        var $this = $(this),
                id = $this.parent().data('id'),
                uid = $this.parent().data('uid'),
                cid = $this.parent().data('cid');

        if ($('.load').length > 0)
            return false;

        //edit industry (load samples collection)
        if ($this.is('.industry_field')) {
            $('.additional_email .edit_industry').hide();
            $('.additional_phone .edit_industry').hide();
            var id = $(this).parents('.print_row').data('id');
            $this.append('<div class="edit_industry"><img src="/images/admin/info-loader.gif" class="load"></div>');
            $.post('/admin/sales/ajax', {
                'func': 'load_industry_variables',
                'id': id
            }, function(data) {
                $this.find('.edit_industry').html(data);
            });
        } else {
            $('.edit_industry').remove();
        }

        $('.aval_company_users').hide();
        if ($this.is('.show_users')) {
            $this.find('.aval_company_users').show();
            return false;
        } else {
            $this.find('.aval_company_users').hide();
        }

        if ($this.parent().next().is('.task_details') && !$this.is('.industry_field')) {
            //show addres if we click by name field, don't hide

            $this.parent().next().remove();
            $this.parent().css('border-bottom', 'solid 1px #E5E5E5');
            $this.parent().css('font-weight', 'normal');
        } else if (!$this.parent().next().is('.task_details')) {
            $('.activity_datatable tr').css('border-bottom', 'solid 1px #E5E5E5');
            $('.task_details').remove();
            $this.parents('table').find('tr').css('font-weight', 'normal');
            $('<tr class="task_details"><td colspan="11"><img src="/images/admin/load6.gif"></td></tr>').insertAfter($this.parent());
            $this.parent().css('font-weight', 'bold');
            $this.parent().css('border-bottom', 'none');
            $.post('/admin/print/ajax/', {
                'func': 'load_task_details',
                'id': id,
                'uid': uid,
                'cid': cid
            }, function(data) {
                if (data) {
                    $this.parent().next().find('td').html(data);
                    fix_job_menu_width();
                    $('.date_time').mask('9999-99-99 99:99:00');
                }
            });
        }
        e.stopPropagation();
    });

    //Load Users
    $('.show_users').live('click', function() {
        var $this = $(this),
                req_id = $(this).parents('tr').data('id');
        $.post('/admin/users/ajax', {
            'func': 'get_company_users',
            'req_id': req_id,
            'job': $('.clickable.ui-tabs-selected').data('id')
        }, function(data) {
            $this.find('.aval_company_users').html(data);
            $(".phone").mask("(999) 999-9999");
            $(".zip").mask("99999");

            var active = $this.find('.aval_company_users').find('select').val();
            $this.find('.aval_company_users').find('li[data-id="' + active + '"]').find('.remove_this_user').remove();
        });
    });

    //update user - from company Job list
    $('.edit_this_user').live('click', function() {
        var $this = $(this);
        $.post('/admin/users/ajax', {
            'func': 'edit_user_form',
            'id': $this.parent().data('id')
        }, function(data) {
            $this.parents('.aval_company_users').html(data);
            $(".phone").mask("(999) 999-9999");
            $(".zip").mask("99999");
        });
    });

    $('button[name=user_fast_update]').live('click', function() {
        var $this = $(this),
                first_name = $this.parent().find('input[name=first_name]').val(),
                last_name = $this.parent().find('input[name=last_name]').val(),
                email = $this.parent().find('input[name=email]').val(),
                street = $this.parent().find('input[name=street]').val(),
                street2 = $this.parent().find('input[name=street2]').val(),
                city = $this.parent().find('input[name=city]').val(),
                state = $this.parent().find('input[name=state]').val(),
                zipcode = $this.parent().find('input[name=zipcode]').val(),
                phone = $this.parent().find('input[name=phone]').val(),
                phone_ext = $this.parent().find('input[name=phone_ext]').val(),
                fax = $this.parent().find('input[name=fax]').val(),
                position = $this.parent().find('input[name=position]').val();

        $.post('/admin/users/ajax', {
            'func': 'edit_user_update',
            'first_name': first_name,
            'last_name': last_name,
            'email': email,
            'street': street,
            'street2': street2,
            'city': city,
            'state': state,
            'zipcode': zipcode,
            'phone': phone,
            'phone_ext': phone_ext,
            'fax': fax,
            'position': position,
            'id': $this.data('id')
        }, function() {
            $this.parent().find('button[name=user_fast_close]').trigger('click');
            $('.clickable.ui-tabs-selected').trigger('click');
        });
    });

    //remove user - from company Job list
    $('.remove_this_user').live('click', function() {
        var $this = $(this),
                uid = $this.parent('li').data('id');
        if (confirm('Remove this user with all connected data?')) {
            $.post('/admin/users/ajax', {
                'func': 'remove_user',
                'id': uid
            }, function() {
                var counts = $this.parents('.this_users_list').find('li').length;
                if (counts === 2) {
                    $this.parents('.this_users_list').find('.remove_this_user').hide();
                }
                $this.parents('.users_list').find('select option[value="' + uid + '"]').remove();
                $this.parent('li').remove();
            });
        }
    });

    //select other user
    $('select[name=select_comp_user]').live('change', function() {
        var $this = $(this),
                req_id = $this.parents('tr').data('id'),
                user_id = $this.val(),
                job_id = 0;
        if ($(this).parents('tr').next().is('.task_details')) {
            job_id = $(this).parents('tr').next().find('.clickable.ui-tabs-selected').data('id');
        }

        if (user_id) {
            $.post('/admin/users/ajax', {
                'func': 'get_userdata',
                'req_id': req_id,
                'user_id': user_id,
                'set_main': 1,
                'job_id': job_id
            }, function(data) {
                if (data) {
                    var phone = (data.phone_ext) ? data.phone + ' x' + data.phone_ext : data.phone;
                    var phone_alt = (data.phone_alt_ext) ? data.phone_alt + ' x' + data.phone_alt_ext : data.phone_alt;
                    var email_alt = (data.email_alt) ? data.email_alt : '';
                    $this.parents('tr').find('.set_username').text(data.first_name + ' ' + data.last_name);
                    $this.parents('tr').find('.additional_phone .v1').text(phone);
                    $this.parents('tr').find('.additional_email .v1').html('<a href="mailto:' + data.email + '">' + data.email + '</a>');

                    $this.parents('tr').find('input[name=additional_email]').val(email_alt).change();
                    $this.parents('tr').find('input[name=additional_phone]').val(phone_alt).change();

                    $this.parents('.aval_company_users').hide();
                    $this.parents('tr').data('uid', data.id);

                    //update tab if opened
                    $this.parents('tr').next().find('.clickable.ui-tabs-selected').trigger('click');
                }
            }, 'json');
        }
    });

    $('input[name=additional_email]').live('change', function() {
        var val = $(this).val();
        $(this).parents('.additional_email').find('.v2').html('<a href="mailto:' + val + '">' + val + '</a>');
    });

    $('input[name=additional_phone]').live('change', function() {
        $(this).parents('.additional_phone').find('.v2').html($(this).val());
    });

    //add user form
    $('button[name=add_user_form]').live('click', function() {
        $(this).hide();
        $('button[name=sel_user_form]').show();
        $('.sel_user').hide();
        $('.add_user').show();
    });
    //select users form
    $('button[name=sel_user_form]').live('click', function() {
        $(this).hide();
        $('button[name=add_user_form]').show();
        $('.sel_user').show();
        $('.add_user').hide();
    });


    //Fast add user to company
    $('button[name=user_fast_add]').live('click', function() {
        var $this = $(this),
                fname = $this.parent().find('input[name=first_name]').val(),
                lname = $this.parent().find('input[name=last_name]').val(),
                email = $this.parent().find('input[name=email]').val(),
                req_id = $this.parents('tr').data('id');

        if (!fname) {
            $('.user_err').text('Fill "First name" field');
        } else if (!lname) {
            $('.user_err').text('Fill "Last name" field');
        } else if (!email) {
            $('.user_err').text('Fill "Email" field');
        } else {
            $('.user_err').text('');
            $.post('/admin/users/ajax', {
                'func': 'add_company_user',
                'first_name': fname,
                'last_name': lname,
                'email': email,
                'street': $this.parent().find('input[name=street]').val(),
                'street2': $this.parent().find('input[name=street2]').val(),
                'city': $this.parent().find('input[name=city]').val(),
                'state': $this.parent().find('input[name=state]').val(),
                'zipcode': $this.parent().find('input[name=zipcode]').val(),
                'phone': $this.parent().find('input[name=phone]').val(),
                'phone_ext': $this.parent().find('input[name=phone_ext]').val(),
                'fax': $this.parent().find('input[name=fax]').val(),
                'position': $this.parent().find('input[name=position]').val(),
                'req_id': req_id
            }, function(data) {
                if (data.id) {
                    $this.parents('.aval_company_users').find('button[name=sel_user_form]').trigger('click');
                    $this.parents('.aval_company_users').find('select[name=select_comp_user]').append('<option value="' + data.id + '"> ' + fname + ' ' + lname + ' </option>');
                    $this.parents('.aval_company_users').find('ul.this_users_list').append('<li data-id="' + data.id + '"><a class="ui-icon ui-icon-pencil left edit_this_user">edit</a><a class="ui-icon ui-icon-closethick left remove_this_user">del</a> ' + fname + ' ' + lname + ' </li>');
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }
    });

    $('button[name=user_fast_close]').live('click', function() {
        $(this).parents('.aval_company_users').hide();
        $(this).parents('.aval_company_users').html('');
    });

    /*
     * Task Details. Fix Job menu width. 
     */
    function fix_job_menu_width() {
        //job menu
        if ($('.job_menu li').length > 5) {
            var all_w = 0;
            $.each($('.job_menu li'), function() {
                all_w = all_w + ($(this).width() + 3);
            });
            $('.job_menu').css('width', all_w + 'px');
            if ($('.jobs_list').width() > all_w) {
                $('.job_slider').hide();
            }
        }
    }

    /*
     * Samples collection. Close samples modal window in /admin/sales/all
     */
    $('input[name=close_edit]').live('click', function() {
        $(this).parents('.edit_industry').remove();
        $('.iconsweet.right').css('opacity', 0);
    });

    /*
     * Samples collection. Change checkbox with industry category - show products
     */
    $('.edit_industry input[name=industry_item]').live('change', function() {
        $('.edit_industry label.active').removeClass('active');
        $(this).parent().addClass('active');
        $('input[name=industry_collection]').removeAttr('checked');
        if ($('input[name=save_new_content]').length > 0) {
            //check for collection items
            var id = $(this).val(),
                    checked = $(this).is(':checked');
            $.each($('.ind_new'), function() {
                var val = $(this).data('type');
                if (val == id) {
                    if (checked) {
                        $(this).attr('checked', 'checked');
                    } else {
                        $(this).removeAttr('checked');
                    }
                    $('.active .used').text($('.ind_new[data-type="' + val + '"]:checked').length);
                }
            });

        } else {
            if ($(this).is(':checked')) {
                $('.sel_products').html('<img src="/images/admin/table-loader.gif" class="load">');
                $('.edit_industry').addClass('details');
                $('.industry_collection').removeAttr('checked');
                $('.new_ind_form').html('<input type="button" name="create_content" value="+ Create Content">');
                $('.nav_buttons').show();
                //load insp products
                $.post('/admin/sales/ajax', {
                    'func': 'get_industry_products',
                    'cat_id': $(this).val()
                }, function(data) {
                    $('.sel_products').html(data);
                    //calculate
                    $('.edit_industry .active').find('.used').text($('input[name=ind_item_need]:checked').length);
                });
            } else {
                $(this).parent().find('.used').text('0');
                $('.edit_industry').removeClass('details');
                $('.sel_products').html('');
                //remove session
                $.post('/admin/sales/ajax', {
                    'func': 'delete_industry_products',
                    'cat_id': $(this).val()
                });
            }
        }
    });

    /*
     * stopPropagation for click by main sample collection modal window
     */
    $('.edit_industry').live('click', function(e) {
        e.stopPropagation();
    });

    /*
     * Samples collection. Change product selection
     */
    $('input[name=ind_item_need]').live('change', function() {
        var $this = $(this),
                id = $this.val(),
                type_index = $this.data('type'),
                type = 0;
        if ($this.is(':checked')) {
            type = 1;
        }
        $.post('/admin/sales/ajax', {
            'func': 'change_need_status',
            'id': id,
            'type_index': type_index,
            'type': type
        });
        //recalculate checked count
        $('.edit_industry .active').find('.used').text($('input[name=ind_item_need]:checked').length);
    });

    /*
     * Samples collection. Save collection/items
     */
    $('input[name=save_industry_items]').live('click', function() {
        var req_id = $(this).parents('.print_row').data('id'),
                industry_title = $('.print_row[data-id="' + req_id + '"]').find('.industry_title');
        industry_title.html('<img src="/images/admin/table-loader.gif" class="load">');
        $('input[name=close_edit]').trigger('click');
        $.post('/admin/sales/ajax', {
            'func': 'save_industry_items',
            'req_id': req_id
        }, function(data) {
            if (data.samples_string) {
                industry_title.text(data.samples_string);
            } else {
                industry_title.text('');
            }
        }, 'json');
    });

    /*
     * Task details. Add new note to request
     */
    $('#add_new_note').live('click', function() {
        var note_text = $('textarea[name=new_note]').val(),
                id = $('input[name=request_id]').val(),
                type = $('select[name=type]').val(),
                job_id = $('.ui-tabs-nav .ui-tabs-selected').data('id'),
                user_type = $('.user_type.active').data('type'),
                pay_note_summ = $('input[name=pay_note_summ]').val(),
                pay_note_date = $('input[name=pay_note_date]').val(),
                pay_note_card = $('select[name=pay_note_card]').val(),
                uid = $(this).parents('.task_details').prev().data('uid'),
                required = $('input[name=note_required]').is(":checked"),
                assign = $('select[name=assign_to]').val(),
                date = $('input[name=note_date]').val();

        if (required && !assign) {
            alert('Select "Assign to" user');
            return false;
        }

        if (note_text) {

            if (!job_id) {
                job_id = $('select[name=select_note_job]').val();
            }

            $.post('/admin/print/ajax', {
                'func': 'add_request_note',
                'note_text': note_text,
                'id': id,
                'type': type,
                'job_id': job_id,
                'user_type': user_type,
                'pay_note_summ': pay_note_summ,
                'pay_note_date': pay_note_date,
                'pay_note_card': pay_note_card,
                'uid': uid,
                'required': required,
                'assign': assign,
                'date': date
            }, function(data) {
                if (data) {
                    $('textarea[name=new_note]').val('');
                    $('.order_notes').prepend(data);
                    $('.add_payment_options').hide();
                    $('.add_payment_options input').val('');
                    $('input[name=note_date]').val('');
                } else {
                    alert('Server error!');
                }
            });
        }
    });

    /*
     * Task details -> Notes. Answer to required message
     */
    $('select[name=required_ansver]').live('change', function() {
        var $this = $(this),
                val = $this.val();
        if (val == 'resolved') {
            $this.parents('.line').find('.re_assign').hide();
            $this.parents('.line').find('.required_text').show();
        } else if (val == 'clarify') {
            $this.parents('.line').find('.re_assign').show();
            $this.parents('.line').find('.required_text').show();
        } else {
            $this.parents('.line').find('.re_assign').hide();
            $this.parents('.line').find('.required_text').hide();
        }
    });

    /*
     * Save reassign request
     */
    $('input[name=save_required_req]').live('click', function() {
        var types = $(this).parents('.required_block').find('select[name=required_ansver]').val(),
                text = $(this).parents('.required_block').find('textarea[name=required_text]').val(),
                uid = $(this).parents('.required_block').find('select[name=reassign_uid]').val(),
                id = $(this).data('id');

        if (!uid && types == 'clarify') {
            alert('Select user for clarify');
        } else if (!text) {
            alert('Enter message text');
        } else {
            $.post('/admin/sales/ajax', {
                'func': 'add_required_message',
                'type': types,
                'text': text,
                'uid': uid,
                'id': id
            }, function(data) {
                if (data.ok) {
                    $('li.ui-tabs-selected').trigger('click');
                    //change general task count - 1
                    var total_tasks = parseInt($('.task_counts').text()) - 1;
                    if (total_tasks >= 0) {
                        $('.task_counts').text(total_tasks);
                    }
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }
    });


    /*
     * Task details. Send email to client
     */
    $('.send_client_message').live('click', function() {
        var subj = $('input[name=mess_subject]').val(),
                text = $('textarea[name=mess_message]').val(),
                email = $(this).data('email'),
                req_id = $(this).parents('.task_details').prev().data('id'),
                job_id = $('.clickable.ui-tabs-selected').data('id'),
                comp_id = $(this).parents('.task_details').prev().data('cid');
        $.post('/admin/sales/ajax', {
            'func': 'send_email',
            'subj': subj,
            'text': text,
            'email': email,
            req_id: req_id,
            job_id: job_id,
            comp_id: comp_id
        }, function(data) {
            if (data == '1') {
                $('#send_email_block').html('<br><b>Message sent.</b>')
            } else {
                alert('Server error!');
            }
        }, 'json');
    });

    /*
     * Task details. Load task details with filter by User type/Job Id
     */
    $('.job_menu li, .user_type, .buttons span').live('click', function(e) {
        var $this = $(this),
                id = $this.parents('.task_details').prev().data('id'),
                uid = $this.parents('.task_details').prev().data('uid'),
                cid = $this.parents('.task_details').prev().data('cid'),
                job_id = '',
                user_type = '',
                show_block = '';

        if ($this.is('li')) {
            //top menu
            job_id = $this.data('id');
            user_type = $('.user_type.active').data('type');
            if(!$(this).is('.ui-tabs-selected')){
                show_block = $('.pj_cat.visible_block').attr('id');
            }            
        } else if ($this.is('span')) {
            //details block
            job_id = $('.job_menu li.ui-tabs-selected').data('id');
            user_type = $('.user_type.active').data('type');
            show_block = $(this).attr('id'); //
        } else {
            job_id = $('.job_menu li.ui-tabs-selected').data('id');
            user_type = $(this).data('type');
            if ($this.is('.active')) {
                $('.pj_cat.visible_block').removeClass('visible_block');
                $('.job_menu a').first().trigger('click');
            } else {
                $('.user_type').removeClass('active');
                $(this).addClass('active');
                if ($('.clickable.ui-tabs-selected').length) {
                    $('.clickable.ui-tabs-selected').trigger('click');
                } else {
                    $('.job_menu a').first().trigger('click');
                }
            }
            return false;
        }
        if (($this.is('li') && $this.is('.clickable')) || $this.is('span')) {
            $('.main_block_info > div').hide();
            $('.main_block_info').append('<img src="/images/admin/info-loader.gif">');

            var user = '';
            if ($this.data('id')) {
                //is job selected - current user info
                user = $this.data('id');
            } else {
                //is all jobs selected - main user info
                user = 'main';
            }
            $.post('/admin/users/ajax', {
                'func': 'get_userdata',
                'jobinfo_id': user,
                'req_id': id,
                'set_main': 0
            }, function(data) {
                if (data) {
                    var phone = (data.phone_ext) ? data.phone + ' x' + data.phone_ext : data.phone;
                    var phone_alt = (data.phone_alt_ext) ? data.phone_alt + ' x' + data.phone_alt_ext : data.phone_alt;
                    var email_alt = (data.email_alt) ? data.email_alt : '';
                    $this.parents('tr').prev().find('.set_username').text(data.first_name + ' ' + data.last_name);

                    $this.parents('tr').prev().find('.additional_phone .v1').text(phone);
                    $this.parents('tr').prev().find('.additional_email .v1').html('<a href="mailto:' + data.email + '">' + data.email + '</a>');

                    $this.parents('tr').prev().find('input[name=additional_email]').val(email_alt).change();
                    $this.parents('tr').prev().find('input[name=additional_phone]').val(phone_alt).change();

                    $('.task_details').prev().data('uid', data.id);
                }
            }, 'json');

            $.post('/admin/print/ajax', {
                'func': 'load_task_details',
                'id': id,
                'uid': uid,
                'cid': cid,
                'job_id': job_id,
                'user_type': user_type,
                'show_block': show_block
            }, function(data) {
                $('.task_details td').html(data);
//                if (show_block) {
//                    $('#' + show_block).trigger('click');
//                }
                fix_job_menu_width();
                //cc input format
                $("input[name=card_number]").mask("9999 9999 9999 99?99");
            });
        }
    });

    /*
     * Task details. Edit Job title
     */
    $('.job_menu .edit_job').live('click', function(e) {
        var comp_id = $(this).parents('.task_details').prev().data('cid'),
                job_id = $(this).parents('.clickable').data('id');
        $('.modal_bg').show();
        //load modal form
        $.post('/admin/sales/ajax', {
            'func': 'get_jobedit_form',
            'comp_id': comp_id,
            'job_id': job_id
        }, function(data) {
            $('.loading').hide();
            $('.modal_bg').find('.contents').html(data);

            //numeric
            $(".numeric").numeric();

        });
        e.stopPropagation();
    });

    $('input[name=edit_current_num]').live('click', function() {
        var type = $('select[name=job_type]').val(),
                job_abbr = $('input[name=edit_job_abbr]').val(),
                num_job = $('input[name=edit_num_job]').val(),
                job_user = $('select[name=job_user]').val();

        $.post('/admin/sales/ajax', {
            'func': 'edit_one_job',
            'type': type,
            'job_abbr': job_abbr,
            'job_user': job_user,
            'num_job': num_job,
            'job_id': $(this).data('job_id'),
            'comp_id': $(this).data('cid')
        }, function(data) {
            if (data.update_user) {
                $('.task_details').prev().data('uid', job_user);
            }
            $('.close_modal').trigger('click');
            $('.clickable.ui-tabs-selected').trigger('click');
        }, 'json');
    });

    $('.modal_bg').on('keyup', 'input[name=edit_num_job]', function() {
        //check job# - if exist
        var job = $('input[name=edit_job_abbr]').val(),
                val = $(this).val(),
                def = $(this).data('def'),
                type = $.trim($('.type_job').text());
        $('.edit_message_error').text('');
        $('input[name=edit_current_num]').hide();
        if (val.length >= 3) {

            if (val == def) {
                //current job
                $('input[name=edit_current_num]').show();
            } else {
                $.post('/admin/sales/ajax', {
                    func: 'check_exist_job',
                    job: job + '-' + type + val
                }, function(data) {
                    if (!parseInt(data.counts)) {
                        $('input[name=edit_current_num]').show();
                    } else {
                        $('.edit_message_error').text('Job exists! Please, use another Job number.');
                    }
                }, 'json');
            }
        }
    });

    /*
     * Task details. stopPropagation for job id edit field
     */
    $('.job_menu input[name=edit_job]').live('click', function(e) {
        e.stopPropagation();
    });

    /*
     * Task details. Update Job title
     */
    $('.job_menu .ui-icon-check').live('click', function(e) {
        var $this = $(this),
                text = $('.job_menu input[name=edit_job]').val();
        $.post('/admin/sales/ajax', {
            'func': 'update_job_id',
            'title': text,
            'id': $this.parent().data('id')
        }, function() {
            $this.parent().html('<a>' + text + '</a><span class="ui-icon ui-icon-pencil pointer marg_t5 edit_job">edit</span><span class="ui-icon ui-icon-close pointer marg_t5 remove_job">del</span>');
            $this.parent().attr('data-id', text);
        });
        e.stopPropagation();
    });

    /*
     * Task details. Remove Job
     */
    $('.job_menu .remove_job').live('click', function() {
        var parent = $(this).parent();
        if (confirm('Remove this Job?')) {
            $.post('/admin/sales/ajax', {
                'func': 'delete_job',
                'id': parent.data('id')
            }, function(data) {
                if (data.ok == 'ok') {
                    parent.remove();
                    $('.job_menu li:first a').trigger('click');
                } else if (data.ok == 'exist_payment') {
                    alert('You can\'t remove job with payments!');
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }
    });

    /*
     * Add alternative email/phone
     */
    $('.add_alternative').live('click', function() {
        var type = $(this).data('type');
        if (type === 'email') {
            $('.alt_' + type + '_block').append('<div><label>&nbsp;</label><input type="text" name="' + type + '_alt[]" value=""> <span class="iconsweet rem_alternative">-</span><br class="clear"></div>');
        } else {
            $('.alt_' + type + '_block').append('<div><label><select name="addtional_phone_type[]"><option value="cell">cell</option><option value="home">home</option><option value="office">office</option></select></label><input type="text" name="' + type + '_alt[]" value="" class="phone_num"> <input type="text" name="' + type + '_ext_alt[]" value="" class="phone_num_ext" placeholder="ext."> <span class="iconsweet rem_alternative">-</span><br class="clear"></div>');
            $(".phone_num").mask("(999) 999-9999");
        }
    });

    /*
     * Remove alternative
     */
    $('.rem_alternative').live('click', function() {
        $(this).parent().remove();
    });

    /* ----- Notes -----*/

    /*
     * Task details. Show edit note form
     */
    $('.edit_note').live('click', function() {
        var $this = $(this),
                $parent = $this.parent(),
                id = $this.data('id'),
                text = $parent.find('.note_text').text(),
                job = $parent.find('strong').text(),
                date = $parent.find('.date').attr('data-formated');
        $parent.find('.note_text').html('<textarea name="ch_note_text" data-id="' + id + '">' + text + '</textarea><br><input type="button" data-id="' + id + '" value="Save" class="save_note"><input type="button" data-id="' + id + '" value="Cancel" class="cancel_note">');
        $this.removeClass('edit_note');
        $parent.find('.date').html('<input type="text" name="ch_note_date" class="date_time" value="' + date + '"><br><span>Y-m-d H:m:s</span>');
        $parent.find('strong').html('<select name="ch_note_job" style="width: 80px"><option value="">-</option></select>');
        $parent.find('select[name=edit_type]').show();
        $parent.find('.info_block').hide();

        $.each($('.ui-tabs-nav li'), function() {
            if ($(this).data('id')) {
                var ch = ($(this).find('a').text() == job) ? 'selected="selected"' : '';
                $('select[name=ch_note_job]').append('<option value="' + $(this).data('id') + '" ' + ch + '>' + $(this).find('a').text() + '</option>');
            }
        });
        $('.date_time').mask('9999-99-99 99:99:99');
    });

    /*
     * Task details. Delete note
     */
    $('.del_note').live('click', function() {
        var $this = $(this),
                id = $this.data('id');
        if (confirm('Remove this note?')) {
            $this.parents('.line').hide();
            $.post('/admin/sales/ajax', {
                'func': 'del_note',
                'id': id
            }, function(data) {
                if (!data.ok) {
                    alert('Server error!');
                    $this.parents('.line').show();
                } else {
                    $this.parents('.line').remove();
                }
            }, 'json');
        }
    });

    /*
     * Task details. Cancel edit note without saving
     */
    $('.cancel_note').live('click', function() {
        $('.clickable.ui-tabs-selected').trigger('click');
//        var $this = $(this),
//                $parent = $this.parents('.line'),
//                id = $this.data('id'),
//                text = $('textarea[name=ch_note_text][data-id=' + id + ']').val();
//        $.post('/admin/sales/ajax', {
//            'func': 'not_save_note',
//            'id': id,
//            'text': text
//        }, function(data) {
//            $parent.find('.ui-icon-pencil').addClass('edit_note');
//            $parent.find('.date').html(data.date);
//            $parent.find('strong').html($('select[name=ch_note_job] :selected').text());
//            $parent.find('.note_text').html(data.text);
//            $parent.find('select[name=edit_type]').hide();
//            $parent.find('.info_block').show();
//        }, 'json');
    });

    /*
     * Task details. Update note
     */
    $('.save_note').live('click', function() {
        var $this = $(this),
                $parent = $this.parents('.line'),
                id = $this.data('id'),
                text = $('textarea[name=ch_note_text][data-id=' + id + ']').val(),
                date = $('input[name=ch_note_date]').val(),
                job = $('select[name=ch_note_job]').val(),
                job_title = $('select[name=ch_note_job] :selected').text(),
                type = $parent.find('select[name=edit_type]').val();

        if (!text) {
            alert('Text note is empty!');
        } else {
            $.post('/admin/sales/ajax', {
                'func': 'save_note',
                'id': id,
                'text': text,
                'date': date,
                'job': job,
                'type': type
            }, function(data) {
                var user_type_text = $.trim($parent.find('select[name=edit_type] :selected').text());
                $parent.find('.ui-icon-pencil').addClass('edit_note');
                $parent.find('.date').html(date).attr('data-formated', date);
                $parent.find('strong').html(job_title);
                $parent.find('.note_text').html(data.text);
                $parent.find('select[name=edit_type]').hide();
                if (user_type_text == '-') {
                    user_type_text = '';
                }

                $parent.find('.info_block').attr('class', '').text(user_type_text).addClass('info_block').addClass(type).show();

            }, 'json');
        }

    });

    /*
     * Task details. Restore removed note
     */
    $('.restore_note').live('click', function() {
        var $this = $(this),
                id = $this.data('id');
        $.post('/admin/sales/ajax', {
            'func': 'restore_note',
            'id': id
        }, function(data) {
            if (data.ok) {
                $this.parents('.removed').removeClass('removed');
                $this.remove();
            }
        }, 'json');
    });

    /*
     * Task details. When note is required
     */
    $('input[name=note_required]').live('change', function() {
        if ($(this).is(':checked')) {
            $('select[name=assign_to],.assign_to').show();
        } else {
            $('select[name=assign_to],.assign_to').hide();
        }
    });


    /*
     * Task details. Add new Job/Estimate form
     */
    $('.add_job').live('click', function() {
        var comp_id = $(this).parents('.task_details').prev().data('cid');
        $('.modal_bg').show();
        //load modal form
        $.post('/admin/sales/ajax', {
            'func': 'get_job_form',
            'comp_id': comp_id
        }, function(data) {
            $('.loading').hide();
            $('.modal_bg').find('.contents').html(data);
        });
    });

    $('select[name=job_type]').live('change', function() {
        $('.type_job').text($(this).val());
        $('input[name=edit_num_job]').trigger('keyup');
//        $('.num_job').text('');
//        $.post('/admin/sales/ajax', {
//            'func': 'get_correct_job_number',
//            'cid': $('input[name=save_current_num]').data('cid'),
//            'type': $(this).val()
//        }, function(data) {
//            $('.num_job').text(data.count);
//        }, 'json');
    });

    $('input[name=save_current_num]').live('click', function() {
        var type = $('select[name=job_type]').val(),
                job_abbr = $('input[name=job_abbr]').val(),
                num_job = $('.num_job').text(),
                job_user = $('select[name=job_user]').val(),
                alt_num = $('.additional').text(),
                prefix = $('input[name=prefix]').val();

        $.post('/admin/sales/ajax', {
            'func': 'add_new_job',
            'type': type,
            'job_abbr': job_abbr,
            'job_user': job_user,
            'num_job': num_job,
            'comp_id': $(this).data('cid'),
            'new': $(this).data('new'),
            'prefix': prefix,
            'alt_num': alt_num
        }, function(data) {
            //update main uid
            $('.task_details').prev().data('uid', job_user);

            $('.close_modal').trigger('click');
            if (data.id) {
                $('.job_menu').prepend('<li data-id="' + data.id + '" class="clickable clickme"></li>');
                $('.clickme').trigger('click');
            }
//            $('.clickable.ui-tabs-selected').trigger('click');
        }, 'json');
    });

    $('input[name=job_abbr]').live('keyup', function() {
        $('.additional').text('');
        if ($(this).val().length === 3) {
            //check new abbr
            $.post('/admin/sales/ajax', {
                'func': 'check_manual_job',
                'val': $('input[name=job_abbr]').val(),
                'cid': $('input[name=save_current_num]').data('cid')
            }, function(data) {
                if (data.ok) {
                    $('.exist_job_company').html('');
                    $('input[name=save_current_num]').show();
                } else if (data.err_comp) {
                    $('.exist_job_company').html('You can\'t use this Job prefix. It already exists in DB and belongs to "' + data.err_comp + '". Please, use altenative option below or try to change the Job prefix manually.');
                    $('input[name=save_current_num]').hide();
                    if (data.alt && data.alt.abbr) {
                        $('.exist_job_company').append('<hr><span style="color:#000">Alternative: <input type="checkbox" value="' + data.alt.abbr + '" name="use_alternative">' + data.alt.abbr + '</span>');
                    }
                }
            }, 'json');
        } else {
            $('.exist_job_company').html('');
            $('input[name=save_current_num]').hide();
        }
    });

    $('input[name=use_alternative]').live('change', function() {
        if ($(this).is(':checked')) {
            var s = $(this).val().substr(0, 3);
            var addit = $(this).val().substr(3);
            $('.additional').text(addit);
            $('input[name=job_abbr]').val(s);
            $('input[name=save_current_num]').show();
        } else {
            $('.additional').text('');
            $('input[name=save_current_num]').hide();
        }
    });

    $('.add_estimate').live('click', function() {
        var user_id = $(this).parents('.task_details').prev().data('uid');
        $.post('/admin/sales/ajax', {
            'func': 'add_new_estimate',
            'user_id': user_id
        }, function(data) {
            if (data.name) {
                $('<li data-id="' + data.name + '" class="clickable"><a>' + data.name + '</a><span class="ui-icon ui-icon-pencil pointer marg_t5 edit_job" style="margin-right: 0">edit</span><span class="ui-icon ui-icon-close pointer marg_t5 remove_job">del</span></li>').insertAfter($('.job_menu li').first());
                $('select[name=select_one_job],select[name=select_note_job],select[name=payment_job_id],select[name=ch_note_job]').append('<option value="' + data.name + '">' + data.name + '</option>');
                $('li[data-id="' + data.name + '"] a').trigger('click');
            } else if (data.err) {
                alert(data.err);
            } else {
                alert('Server error!');
            }
        }, 'json');
    });

    /*
     * Task details. Change select with jobs list
     */
    $('select[name=select_one_job]').live('change', function() {
        var val = $(this).val();
        $('li[data-id="' + val + '"]').trigger('click');
    });

    /*
     * Task details -> add note. Change select with note type
     */
    $('.tasks_table select[name=type]').live('change', function() {
        var val = $(this).val();
        $('.date_time').mask('9999-99-99 99:99:00');
        if (val == 'payment' || val == 'credit') {
            $('.add_payment_options').show();
        } else {
            $('.add_payment_options').hide();
        }
    });


    /* --- Credit Card --- */

    /*
     * Task details -> Credit cards. Add credit card form
     */
    $('.add_credit_card').live('click', function() {

        if ($('.card_add_form').is(':visible')) {
            $('.cancel_save_card').trigger('click');
        }

        $('.card_add_form').show();
        $('.save_card').show();
        $(".credit_date").mask("99/99");
        $(".phone").mask("(999) 999-9999");
        $('.card_edit_form').hide();
        $('.check_tranfer').hide();
        $('.card_process_form').hide();
        $('.save_billing_changes').addClass('hide');

        get_billing_list();

        //select default if first
        if ($('.card_process').length === 0) {
            $('input[name=set_default]').attr('checked', 'checked').attr('readonly', 'readonly').attr('disabled', 'disabled');
        } else {
            $('input[name=set_default]').removeAttr('readonly').removeAttr('disabled');
        }
        $('.form_title').text('Add new payment method');
        billing_set_company_name($(this).parents('.task_details').prev().data('cid'));
    });

    /*
     * Task details -> Credit cards. Get billing autofill list for current user.
     */
    function get_billing_list(id) {
        $.post('/admin/sales/ajax', {
            'func': 'get_autofill_billing',
            'job_id': $('.clickable.ui-tabs-selected').data('id')
        }, function(data) {
            $('.sel_list li').remove();
            if (data.all.length > 0) {
                $('.sel_list').css('opacity', 1);
                $.each(data.all, function(i, v) {
                    var sel = '';

                    if (id && parseInt(v.id) === id) {
                        sel = 'class="ok"';
                    }
                    $('.sel_list').append('<li data-id="' + v.id + '" ' + sel + '>' + v.title + ' <span class="remove_bill_element"></span></li>');
                });
            } else {
                $('.sel_list').css('opacity', 0);
            }
            //update
            if (id) {
                if (!$('.sel_list .ok').length) {
                    $('.sel_list li').first().addClass('ok');
                }
                $('.sel_list').change();
            } else {
                $('.ok').removeClass('ok');
                $('.sel_arrows').html('');
            }
        }, 'json');
    }

    $('.sel_list').live('change', function() {
        var text = $(this).find('.ok').html(),
                id = $(this).find('.ok').data('id');
        $('.sel_arrows').html(text);
//        $('input[name=same_as_shipping]').removeAttr('checked')
        //load other data
        if ($(this).is('.billing_selector')) {
            autofill_billing(id);
        } else if ($(this).is('.shipping_selector')) {
            autofill_shipping(id);
        }
    });


    /*
     * Task details -> Credit cards. Autofill billing fields for current user.
     * @param (int) val: billing id
     */
    function autofill_billing(val) {
        if (!val) {
            //none
            $('input[name^=billing_]:visible').val('');
        } else {

            $.post('/admin/sales/ajax', {
                'func': 'get_card_billing',
                'id': val,
                'job_id': $('.clickable.ui-tabs-selected').data('id')
            }, function(data) {
                $('input[name^=billing_]:visible').val('');
                if (data.info) {
                    $('input[name=billing_company]:visible').val(data.info.company);
                    $('input[name=billing_fname]:visible').val(data.info.first_name);
                    $('input[name=billing_lname]:visible').val(data.info.last_name);
                    $('input[name=full_card_name]:visible').val(data.info.full_name);
                    $('input[name=billing_address]:visible').val(data.info.address);
                    $('input[name=billing_suite]:visible').val(data.info.suite);
                    $('input[name=billing_address2]:visible').val(data.info.address2);
                    $('input[name=billing_city]:visible').val(data.info.city);
                    $('input[name=billing_state]:visible').val(data.info.state);
                    $('input[name=billing_zip]:visible').val(data.info.zip);
                    $('input[name=billing_country]:visible').val(data.info.country);
                    $('input[name=billing_email]:visible').val(data.info.email);
                    $('input[name=billing_phone]:visible').val(data.info.phone);
                    $('input[name=billing_phone_ext]:visible').val(data.info.phone_ext);

                    update_billing_full_name();
                }
            }, 'json');
        }
    }

    function autofill_shipping(val) {
        $('input[name^=shipping_]:visible').val('');
        if (val) {
            $.post('/admin/sales/ajax', {
                'func': 'get_shipping_details',
                'id': val
            }, function(data) {
                if (data.shipp) {
                    $('input[name=shipping_company]:visible').val(data.shipp.company);
                    $('input[name=shipping_fname]:visible').val(data.shipp.first_name);
                    $('input[name=shipping_lname]:visible').val(data.shipp.last_name);
                    $('input[name=shipping_address]:visible').val(data.shipp.address);
                    $('input[name=shipping_suite]:visible').val(data.shipp.suite);
                    $('input[name=shipping_address2]:visible').val(data.shipp.address2);
                    $('input[name=shipping_city]:visible').val(data.shipp.city);
                    $('input[name=shipping_state]:visible').val(data.shipp.state);
                    $('input[name=shipping_zip]:visible').val(data.shipp.zip);
                    $('input[name=shipping_country]:visible').val(data.shipp.country);
                    $('input[name=shipping_email]:visible').val(data.shipp.email);
                    $('input[name=shipping_phone]:visible').val(data.shipp.phone);
                    $('input[name=shipping_phone_ext]:visible').val(data.shipp.phone_ext);
                }
            }, 'json');
        }
    }

    $('input[name^=billing_]').live('keyup', function() {
        var show = false;
        $.each($('input[name^=billing_]'), function() {
            if ($(this).val()) {
                show = true;
            }
        });
        if (show) {

            $('.save_billing_changes').show();
            $('.save_card').hide();
        } else {
            $('.save_as_billing').hide();
            $('.save_card').show();
        }
    });

    /*
     * Task details -> Credit cards. Clear billing fields
     */
    $('.clear_billing').live('click', function(e) {
        $('.left_col:visible').find('input[type=text]').val('');
        $('.ok').removeClass('ok');
        $('.sel_arrows').html('');

        if (!e.clientX) {
            $('input[name^=billing_]:visible').first().keyup();
        }
        //set company name
        billing_set_company_name($(this).parents('.task_details').prev().data('cid'));
        $('input[name=full_card_name]').val('');
        $('.save_billing_changes').hide();
        $('.save_changed_info').show();
        $('.save_card').show();
    });

    function billing_set_company_name(id) {
        $.post('/admin/sales/ajax', {
            func: 'get_company_name',
            id: id
        }, function(data) {
            if (data.company) {
                $('.left_col:visible').find('input[name=billing_company]').val(data.company);
            } else {
                alert('Server error!');
            }
        }, 'json');
    }

    function update_billing_full_name() {
        var fname = $('input[name=billing_fname]:visible').val(),
                lname = $('input[name=billing_lname]:visible').val();
        $('input[name=full_card_name]:visible').val(fname + ' ' + lname);
    }

    $('body').on('keyup', 'input[name=billing_fname],input[name=billing_lname]', function() {
        update_billing_full_name();
    }).on('keyup', 'input[name^=billing_], input[name=full_card_name]', function() {
        $('.save_billing_changes').removeClass('hide');

        //chcked autofill, show update
        if ($('.ok').length) {
            $('.bill_save_update').show();
            $('.bill_save_update').next().show();
        } else {
            $('.bill_save_update').hide();
            $('.bill_save_update').next().hide();
        }

    }).on('click', '.bill_save_continue', function() {
        $('.ok').removeClass('ok');
        $('.sel_arrows').html('');
        $('.save_billing_changes').addClass('hide');
        $('.save_card').show();
        $('.save_changed_info').show();

    }).on('click', '.bill_not_save_continue', function() {

        $('.ok').removeClass('ok');
        $('.sel_arrows').html('');
        $('.save_billing_changes').addClass('hide');
        $('.save_card').show();
        $('.save_changed_info').show();

    }).on('click', '.bill_save_add', function() {
        $.post('/admin/sales/ajax', {
            func: 'bill_save_add',
            data: $(this).parents('form').serialize(),
            uid: $(this).parents('.task_details').prev().data('uid')
        }, function(data) {
            if (data.ok) {

                $('.ok').removeClass('ok');
                $('.sel_list').append('<li data-id="' + data.ok.id + '" class="ok">' + data.ok.billing_company + '<br>' + data.ok.billing_fname + ' ' + data.ok.billing_lname + '<br>' + data.ok.billing_address + '<br>' + data.ok.billing_city + ' ' + data.ok.billing_zip + ' ' + data.ok.billing_state + ' <span class="remove_bill_element"></span></li>');
                $('.sel_list').change();

                $('.save_billing_changes').addClass('hide');
                $('.save_card').show();
                $('.save_changed_info').show();
            } else {
                alert('Server error!');
            }
        }, 'json');

    }).on('click', '.bill_save_update', function() {

        $.post('/admin/sales/ajax', {
            func: 'bill_save_update',
            data: $(this).parents('form').serialize(),
            id: $('.ok').data('id')
        }, function(data) {
            if (data.ok) {
                $('.ok').html(data.ok.billing_company + '<br>' + data.ok.billing_fname + ' ' + data.ok.billing_lname + '<br>' + data.ok.billing_address + '<br>' + data.ok.billing_city + ' ' + data.ok.billing_zip + ' ' + data.ok.billing_state + ' <span class="remove_bill_element"></span>');
                $('.sel_list').change();

                $('.save_billing_changes').addClass('hide');
                $('.save_card').show();
                $('.save_changed_info').show();
            } else {
                alert('Server error!');
            }
        }, 'json');

    });


    $('.clear_shipping').live('click', function() {
        $('input[name^=shipping_]:visible').val('');
        $('.ok').removeClass('ok');
        $('.sel_arrows').html('');
        $('.shipp_add_contact').hide();
        $('.shipp_update_contact').hide();
        $('.right_col > div').show();
    });

    $('input[name^=shipping_]').live('keyup', function() {
        $('.shipp_add_contact').show();
        $('.right_col:visible > div').hide();
        if ($('.shipping_selector .ok').length > 0) {
            $('.shipp_update_contact').show();
        } else {
            $('.shipp_update_contact').hide();
        }
    });

    //Update Shipping info
    $('.shipp_update_contact').live('click', function() {
        $('.right_col > div').show();
        $('.shipp_add_contact, .shipp_update_contact').hide();
        var id = $('.shipping_selector .ok').data('id');
        if (id) {
            var data = {};
            data.id = id;
            data.company = $('input[name=shipping_company]').val();
            data.fname = $('input[name=shipping_fname]').val();
            data.lname = $('input[name=shipping_lname]').val();
            data.address = $('input[name=shipping_address]').val();
            data.suite = $('input[name=shipping_suite]').val();
            data.address2 = $('input[name=shipping_address2]').val();
            data.city = $('input[name=shipping_city]').val();
            data.state = $('input[name=shipping_state]').val();
            data.zip = $('input[name=shipping_zip]').val();
            data.country = $('input[name=shipping_country]').val();
            data.phone = $('input[name=shipping_phone]').val();
            data.email = $('input[name=shipping_email]').val();

            $.post('/admin/sales/ajax', {
                'func': 'shipp_update_contact',
                'data': data
            }, function() {
                $('.shipping_selector').find('li[data-id="' + id + '"]').html(data.fname + ' ' + data.lname + '<br>' + data.city + ' ' + data.state + '<br>' + data.address)
                $('.sel_list').change();
            }, 'json');
        }
    });

    //Add Shipping info
    $('.shipp_add_contact').live('click', function() {
        $('.modal_bg').show();
        $('.modal_bg .loading').hide();
        $('.modal_bg .contents').html('<p><h3>Adding Shipping Information</h3><br><span style="color: red">Add this Shipping address to Contact Profile (Backend only).</span><br><br><label>View as:</label> <input type="text" name="shipping_title" style="width: 50%">\n\
         <br><input type="checkbox" name="public_profile"> Add to contacts (Company profile)<br><br>\n\
         <button class="button_small whitishBtn close_shipping_form">Cancel</button><button class="button_small dblueBtn confirm_save_shipping">Add</button></p><span class="err_save error"></span><br>');
    });

    $('.confirm_save_shipping').live('click', function() {
        var data = {};
        data.job_id = $('select[name=payment_job_id]').val();
        data.company = $('input[name=shipping_company]').val();
        data.fname = $('input[name=shipping_fname]').val();
        data.lname = $('input[name=shipping_lname]').val();
        data.address = $('input[name=shipping_address]').val();
        data.suite = $('input[name=shipping_suite]').val();
        data.address2 = $('input[name=shipping_address2]').val();
        data.city = $('input[name=shipping_city]').val();
        data.state = $('input[name=shipping_state]').val();
        data.zip = $('input[name=shipping_zip]').val();
        data.country = $('input[name=shipping_country]').val();
        data.title = $('input[name=shipping_title]').val();
        data.phone = $('input[name=shipping_phone]').val();
        data.email = $('input[name=shipping_email]').val();
        data.public = $('input[name=public_profile]').is(':checked');

        $.post('/admin/sales/ajax', {
            'func': 'shipp_add_contact',
            'data': data
        }, function(datas) {
            $('.shipp_add_contact').hide();
            $('.shipp_update_contact').hide();
            $('.close_modal').trigger('click');

            $('.shipping_selector').find('.ok').removeClass('ok');
            $('.shipping_selector').append('<li data-id="' + datas.id + '" class="ok">' + data.fname + ' ' + data.lname + '<br>' + data.city + ' ' + data.state + '<br>' + data.state + ' <span class="remove_shipp_element"></span></li>');
            $('.sel_list').change();

            $('.right_col > div').show();
            $('.shipp_add_contact, .shipp_update_contact').hide();
        }, 'json');
    });

    $('.close_shipping_form').live('click', function() {
        $('.close_modal').trigger('click');
    });

    /*
     * Task details -> Credit cards. Add new Credit Card for user
     */
    $('.save_card').live('click', function() {
        var parent = $(this).parent(),
                cc_number = parent.find('input[name="card_number"]').val(),
                cc_ccv = parent.find('input[name=cc_ccv]').val(),
                cc_date = parent.find('input[name=cc_date]').val(),
                view_as = parent.find('input[name=view_as]').val();

        parent.find('.card_err').text('');
        if (cc_number.length < 10) {
            parent.find('.card_err').text('*Invalid Credit Card Number');
            return false;
        } else if (!cc_date) {
            parent.find('.card_err').text('*Invalid "Exp.Date" field');
            return false;
        } else if (cc_ccv && (cc_ccv.lenth < 3 || cc_ccv.lenth > 4)) {
            parent.find('.card_err').text('*Invalid CCV Number');
            return false;
        } else if (!view_as) {
            parent.find('.card_err').text('*"View as" field is empty!');
            return false;
        } else {
            parent.find('.card_err').text('');
            var def_card = ($('input[name=set_default]:visible').is(':checked')) ? 1 : 0;
            $.post('/admin/sales/ajax', {
                'func': 'add_credit_card',
                'data': $('form[name=card_info]').serialize(),
                'def_card': def_card,
                'card_number': cc_number,
                'job_id': $('.clickable.ui-tabs-selected').data('id'),
                'autofill_billing': $('.card_add_form .sel_list .ok').data('id'),
                'card_description': $('input[name=card_description]').val(),
                view_as: view_as
            }, function(data) {
                if (data.id) {
                    $('.clickable.ui-tabs-selected').trigger('click');
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }
    });

    /*
     * Task details -> disable propagation click by name.
     */
    $('.aval_company_users').live('click', function(e) {
        e.stopPropagation();
    });
    $('.tasks_table, .jobs_list').live('click', function() {
        $('.aval_company_users').hide();
        if ($('.edit_industry:visible').length > 0) {
            $('.edit_industry').find('input[type=button][value="Save"]').trigger('click');
            $('.edit_industry').fadeOut(100, function() {
                $('.edit_industry').hide();
            });
        }
    });

    /*
     * Task details -> Credit cards. Close Add credit card form without saving
     */
    $('.cancel_save_card').live('click', function() {
        $('.card_add_form').hide();
        $('.card_add_form input').val('');
        $('.card_err').text('');
        $('input[name=card_number]').trigger('keyup');
        $('input[name=set_default]').removeAttr('checked');
        $('.save_billing_changes').hide();
    });

    $('.cancel_edit_card').live('click', function() {
        $('.card_edit_form').hide();
        $('.card_edit_form input').val('');
        $('.card_err').text('');
        $('.save_billing_changes').hide();
    });

    /*
     * Task details -> Credit cards. Close Process credit card form
     */
    $('.cancel_process_card').live('click', function() {
        $('.card_process_form input').val('');
        $('.card_process_form input[name=charge]').trigger('keyup');
        $('textarea[name=description]').val('');
        $('.save_changed_info, .shipp_add_contact, .shipp_update_contact, .card_process_form, .card_edit_form').hide();
        $('.card_err').text('');
        $('input[name=edg]').removeAttr('checked');
    });

    $('input[name=set_card_default]').live('change', function() {
        $.post('/admin/sales/ajax', {
            'func': 'set_card_default',
            'id': $(this).val()
        }, function() {
            $('.clickable.ui-tabs-selected').trigger('click');
        });
    });

    function get_cardtype(number) {
        var re = {
            visa: /^4[0-9]{12}(?:[0-9]{3})?$/,
            mastercard: /^5[1-5][0-9]{14}$/,
            amex: /^3[47][0-9]{13}$/,
            diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,
            discover: /^6(?:011|5[0-9]{2})[0-9]{12}$/,
            jcb: /^(?:2131|1800|35\d{3})\d{11}$/
        };
        if (re.visa.test(number)) {
            return 'Visa';
        } else if (re.mastercard.test(number)) {
            return 'MasterCard';
        } else if (re.amex.test(number)) {
            return 'American Express';
        } else if (re.diners.test(number)) {
            return 'Diners Club';
        } else if (re.discover.test(number)) {
            return 'Discover';
        } else if (re.jcb.test(number)) {
            return 'JCB';
        } else {
            return '';
        }
    }

    $('input[name=card_number]').live('keyup', function() {
        var $this = $(this),
                nums = $this.val();
        num = $.trim(nums.replace(/\D/g, ''));
        var type = get_cardtype(num);
        $this.removeAttr('style');
        if (!num) {
            return '';
        }
        if (type || !num) {
            $this.removeAttr('style');
            //server side CC check
            if (num) {
                $.post('/admin/sales/ajax', {
                    'func': 'validate_CC',
                    'number': num
                }, function(data) {
                    if (data.rez) {
                        $this.removeAttr('style');
                    } else {
                        $this.css('border', '1px solid red').css('background', 'rgba(255,0,0,0.1)');
                    }
                }, 'json');
            }
        } else {
            $this.css('border', '1px solid red').css('background', 'rgba(255,0,0,0.1)');
        }
        if (num) {
            $('input[name=card_description]:visible').val(type);
        }
    });

    //user adress my select
    $('.sel_selected').live('click', function() {
        $('.sel_list').show();
    });

    $('.sel_list li').live('click', function() {
        $('.sel_list .ok').removeClass('ok');
        $(this).addClass('ok');
        $('.sel_arrows').html($(this).html());
        $('.sel_list').change();
        $('.sel_list').hide();

        $('.save_billing_changes').hide();
        $('.save_card').show();

        //if edit - show save button
        if ($('.card_edit_form').is(':visible')) {
            $('.save_changed_info').show();
        }
    });

    $('body').click(function() {
        $('.sel_list').hide();
    });

    /*
     * Task details -> Credit cards. Open Process credit card form
     */
    $('.card_process').live('click', function() {
        var $this = $(this),
                id = $this.data('id'),
                title = $this.parent().parent().find('strong').text();
        $('.card_add_form').hide();
        $('.check_tranfer').hide();
        $('.card_process_form').show();
        $('.card_edit_form').hide();
        $('.credit_date').mask('99/99');
        $('.form_title').text('Process Credit Card "' + $.trim(title) + '"');
        $('.process_card').data('id', id);
        $('.card_err').text('');
        get_card_shipping(id, $this);
        $('.number').numeric(',');
        $(".phone").mask("(999) 999-9999");
    });

    function get_card_shipping(id, elem) {
        $.post('/admin/sales/ajax', {
            'func': 'get_card_shipping',
            'id': id,
            'cid': elem.parents('.task_details').prev().data('cid')
        }, function(data) {
            $('.card_process_form .sel_list li').remove();
            if (data.all.length > 0) {
                $('.card_process_form .sel_list').css('opacity', 1);
                $.each(data.all, function(i, v) {
                    var sel = '';
                    if (v.uid === v.curr_uid && !sel) {
                        sel = 'class="ok"';
                    }
                    $('.card_process_form .sel_list').append('<li data-id="' + v.id + '" ' + sel + '>' + v.title + ' <span class="remove_shipp_element"></span></li>');
                });

                if (!$('.card_process_form .sel_list .ok').length) {
                    $('.card_process_form .sel_list li').first().addClass('ok');
                }

            } else {
                $('.card_process_form .sel_list').css('opacity', 0);
            }
            $('.card_process_form .sel_list').change();
        }, 'json');
    }

    $('body').on('click', '.remove_shipp_element', function(e) {
        e.stopPropagation();
        var $this = $(this),
                id = $this.parent().data('id');
        if (confirm('Remove this address?')) {
            $.post('/admin/sales/ajax', {
                func: 'remove_shipp_address',
                id: id
            }, function() {
                $('.clickable.ui-tabs-selected').trigger('click');
            });
        }
    }).on('click', '.remove_bill_element', function(e) {
        e.stopPropagation();
        var $this = $(this),
                id = $this.parent().data('id');
        if (confirm('Remove this address?')) {
            $.post('/admin/sales/ajax', {
                func: 'remove_bill_address',
                id: id
            }, function() {
                $('.clickable.ui-tabs-selected').trigger('click');
            });
        }
    });

    /*
     * Task details -> Credit cards. Delete note from payment history
     */
    $('.del_payment').live('click', function() {
        var $this = $(this),
                id = $this.data('id');

        if (confirm('Remove payment note?')) {
            $this.parent().parent().hide();

            $.post('/admin/sales/ajax', {
                'func': 'remove_payment_note',
                'id': id
            }, function(data) {
                if (data.ok) {
                    $this.parent().parent().remove();
                } else {
                    $this.parent().parent().show();
                    alert('Server error!');
                }
            }, 'json');
        }
    });

    /*
     * Task details -> Credit cards. Process new payment for credit card
     */

    $('.tooltip span').live('click', function(e) {
        e.stopPropagation();
    });
    $('.tooltip').live('click', function(e) {
        $(this).find('span').fadeIn();
    });
    $('body').on('click', function() {
        $('.tooltip span').hide();
    });

    $('.process_card').live('click', function() {
        var $this = $(this),
                parent = $this.parents('form'),
                charge = $('input[name=charge]').val(),
                request_id = $(this).parents('.task_details').prev().data('id'),
                id = $this.data('id');
        if (!charge) {
            parent.find('.card_err:visible').text('*Charge AMT is empty');
        } else if ($('input[name=edg]:checked').length === 0) {
            parent.find('.card_err:visible').text('*EDG?');
        } else {

            parent.find('.card_err:visible').text('');
            $('<img src="/images/admin/info-loader.gif" class="load">').insertBefore($this);
            $this.removeClass('process_card');
            
            $.post('/admin/sales/ajax', {
                'func': 'run_card_payment',
                'data': $('form[name=card_info_procc]:visible').serialize(),
                'charge': charge,
                'request_id': request_id,
                'user_type': $('.user_type.active').data('type'),
                'client_id': $this.parents('.task_details').prev().data('uid'),
                'card_id': id,
                'billing_id': $('.ok').data('id')
            }, function(data) {
                $this.addClass('process_card');
                
                $('.load').remove();
                if (data.ok) {
                    $('input[name=pay_summ]').val('');
                    $('.card_err:visible').html('<span style="color: green">' + data.ok + '</span>').animate({
                        'opacity': '0.3'
                    }, 700, function() {
                        $(this).animate({
                            'opacity': '1'
                        }, 600, function() {
                            $('li.ui-tabs-selected').trigger('click');
                        });
                    });
                } else if (data.err) {
                    $('.card_err:visible').html(data.err);
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }
    });

    /*
     * Edit order total
     */
    $('.edit_order_total').live('click', function() {
        var job = $('.clickable.ui-tabs-selected a').text();
        $('.modal_bg').show();
        $('.modal_bg .loading').hide();
        $('.modal_bg .contents').html('<p><h3>Edit Order Total for "' + job + '"</h3><br><span style="color: red">You want to change order total, please specify the reason for this changes.</span><br><br><label>New Order Total:</label> <input type="text" name="new_total_summ" style="width: 80px">\n\
         <br><label>Reason text:</label><textarea name="reason_text"></textarea><br>\n\
         <button class="button_small whitishBtn close_shipping_form">Cancel</button><button class="button_small dblueBtn confirm_change_order_total">Change</button></p><span class="err_save error"></span><br>');
    });

    $('.confirm_change_order_total').live('click', function() {
        var text = $('textarea[name=reason_text]').val(),
                summ = parseFloat($('input[name=new_total_summ]').val());

        if (!summ) {
            $('.err_save').text('Fill "New Order Total" field');
        } else if (!text) {
            $('.err_save').text('Fill "Reason text" field');
        } else {
            $.post('/admin/sales/ajax', {
                'func': 'change_order_total',
                'job_id': $('.clickable.ui-tabs-selected').data('id'),
                'summ': summ,
                'text': text,
                'req_id': $('.task_details').prev().data('id'),
                'comp_id': $('.task_details').prev().data('cid'),
            }, function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            });
        }
    });

    /*
     * Task details -> Credit cards. Change edg field
     */
    $('input[name=edg]').live('change', function() {
        if ($('input[name=edg]:checked').val() == 1) {
            $('input[name=payment_type][value="100"]').trigger('click');
        }
    });

    $('.credit_payment').live('click', function(e) {
        $('.modal_bg').show();
        $.post('/admin/sales/ajax', {
            'func': 'get_credit_form',
            'pay_id': $(this).data('id')
        }, function(data) {
            $('.loading').hide();
            $('.modal_bg').find('.contents').html(data);
            $('input[name=credit_amount]').numeric(',');
        });
        e.stopPropagation();
    });

    $('button[name=run_credit]').live('click', function() {
        var amount = $('input[name=credit_amount]').val(),
                note = $('textarea[name=credit_note]').val();
        $('.error').text('');

        if (!note) {
            $('.error').text('Fill "Note" field');
        } else if (amount > 0) {
            $.post('/admin/sales/ajax', {
                'func': 'run_credit',
                'id': $(this).data('id'),
                'amount': amount,
                'note': note,
                'req_id': $('.task_details').prev().data('id'),
                'user_type': $('.user_type.active').data('type')
            }, function(data) {
                if (data.ok) {
                    $('.error').text('Credit success!');
                    setTimeout(function() {
                        $('.close_modal').trigger('click');
                        $('.clickable.ui-tabs-selected').trigger('click');
                    }, 3000);
                } else if (data.err) {
                    $('.error').text(data.err);
                } else {
                    alert('Server error!');
                }
            }, 'json');
        } else {
            $('.error').text('Fill "Credit amount" field');
        }
    });

    /*
     * Task details -> Credit cards. Show saving link for credit card if change 
     */
    $('input[name^=card_], input[name^=full_card_name],input[name^=cc_date],input[name^=cc_ccv],input[name^=full_user_name]').live('keyup', function(e) {
        if (!e.isTrigger && !$('.save_billing_changes').is(':visible')) {
            $('.save_changed_info').show();
        }
    });
    $('input[name^=billing_]').live('keyup', function(e) {
        if (!e.isTrigger) {
            $('.save_changed_info').hide();
        }
    });
    $('input[name=set_default]').live('click', function(e) {
        if (!$('.save_billing_changes').is(':visible')) {
            $('.save_changed_info').show();
        }
    });

    /*
     * Task details -> Credit cards. Save changes in credit card
     */
    $('.save_changed_info').live('click', function() {
        var $this = $(this),
                id = $this.data('id');

        if (id) {
            $.post('/admin/sales/ajax', {
                'func': 'save_changed_info',
                'card_number': $('input[name="card_number"]:visible').val(),
                'data': $('form[name=card_info_procc]').serialize(),
                'id': id,
                'autofill_billing': $('.ok').data('id')
            }, function(data) {
                if (data.ok) {
                    if (!$('.process_card').is(":visible")) {
                        $('.clickable.ui-tabs-selected').trigger('click');
                    } else {
                        $this.parent().hide();
                    }
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }
    });

    $('input[name=credit_amount]').live('keyup', function() {
        if (parseFloat($(this).val()) > parseFloat($('.order_totals').text())) {
            $(this).val(parseFloat($('.order_totals').text()));
            $(this).css('background', '#ffaaaa').css('border', '1px solid red');
        } else {
            $(this).removeAttr('style');
        }
    });

    /*
     * Task details -> Credit cards. Delete credit card for user
     */
    $('.card_delete').live('click', function() {
        var $this = $(this),
                id = $this.data('id');
        if (confirm('Remove Credit Card?')) {
            $('.card_process_form').hide();

            $.post('/admin/sales/ajax', {
                'func': 'delete_credit_card',
                'id': id
            }, function(data) {
                if (data.ok) {
                    $this.parent().parent().remove();
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }
    });

    //Change charge
    $('input[name=charge]').live('keyup', function() {
        if ($('input[name=balance_user]').length > 0) {
            if (parseInt($(this).val()) > parseInt($('input[name=balance_user]').val())) {
                $(this).val($('input[name=balance_user]').val())
                $(this).css('border', '1px solid red').css('background', 'rgba(255, 0, 0, 0.0980392)');
            } else {
                $(this).css('border', '1px solid #ccc').css('background', '#f8f8f8');
            }
        } else {
            if (parseInt($(this).val()) > parseInt($('input[name=order_total]').val())) {
                $(this).val($('input[name=order_total]').val())
                $(this).css('border', '1px solid red').css('background', 'rgba(255, 0, 0, 0.0980392)');
            } else {
                $(this).css('border', '1px solid #ccc').css('background', '#f8f8f8');
            }
        }
    });

    /*
     * Task details -> Credit cards. Calculate "Charge AMT" from "Order Total"
     */
    function calck_amount() {
        var need = 0,
                type = 0,
                summ = $('input[name=order_total]').val(),
                edg = $('input[name=edg]:checked').val();

//        if (!$('input[name=charge]').is('.disabled')) {
        summ = summ.replace(',', '.');
        if (summ < 500) {
            need = $('input[name=order_total]').val();
            $('input[name=payment_type][value=100]').trigger('click');
//                $('input[name=payment_type][value="100"]').attr('checked', 'checked');
//                need = summ;
        } else {
            if ($('input[name=payment_type]:checked').length > 0) {
                type = $('input[name=payment_type]:checked').val();
                if (type == 100) {
                    if ($('input[name=balance_user]').length > 0) {
                        need = $('input[name=balance_user]').val();
                    } else {
                        need = $('input[name=order_total]').val();
                    }
                } else {
                    need = (summ / 100) * type;
                }
            } else {
                if ($('input[name=edg][value=1]').is(':checked')) { //edg
                    $('input[name=payment_type][value=100]').trigger('click');
//                        $('input[name=payment_type][value="100"]').attr('checked', 'checked');
//                        type = 100;
                } else {
                    $('input[name=payment_type][value="0"]').attr('checked', 'checked');
                    type = 0;
                    need = 0;
                }
            }
        }
        $('input[name=charge]').val(Math.ceil(need)); //.trigger('keyup')
//        }
    }

    $('input[name=charge]').live('keyup', function() {
        var val = parseInt($(this).val()),
                total = $('input[name=order_total]').val();
        if (val) {
//            $(this).addClass('disabled');
            if (total == val) {
                $('input[name=payment_type][value=100]').attr('checked', 'checked');
            } else if (total / val == 2) {
                $('input[name=payment_type][value=50]').attr('checked', 'checked');

            } else if (parseFloat(total / val) == 3.3333333333333335) {
                $('input[name=payment_type][value="30"]').attr('checked', 'checked');

            } else if (total / val == 5) {
                $('input[name=payment_type][value="20"]').attr('checked', 'checked');

            } else {
                $('input[name=payment_type][value="0"]').attr('checked', 'checked');
            }
//          $('input[name=payment_type]').attr('readonly', 'readonly').attr('disabled', 'disabled');
        } else {
//            $(this).removeClass('disabled');
//            $('input[name=payment_type]').removeAttr('readonly').removeAttr('disabled');
        }
    });

    /*
     * Task details -> Credit cards. Calculate amaunt by changinf total field
     */
    $('input[name=order_total]').live('keyup', function() {
        calck_amount();
    });

    /*
     * Task details -> Credit cards. Calculate amaunt by changing payment type
     */
    $('input[name=payment_type]').live('change', function() {
        calck_amount();
        $('input[name=charge]').trigger('keyup');
    });

    /*
     * Task details -> Credit cards. If we change charge, set payment type as installment
     */
    $('input[name=charge]').live('keyup', function(e) {
        if (e.keyCode >= 9 && e.keyCode <= 45) {
            return false;
        }
        $('select[name=payment_type] option:checked').removeAttr('selected');
        $('select[name=payment_type] option[value=installment]').attr('selected', 'selected');
    });

    /*
     * Task details -> Credit cards. Show card details when we click on card number
     */
    $('.show_card_details').live('click', function() {
        var id = $(this).data('id'),
                inf1 = $.trim($(this).parent().find('.show_card_details .view_as').html()),
                inf2 = $.trim($(this).parent().find('.show_card_details .view_as').next().html());
        get_card_details(id);

        $('.save_changed_info').hide();
        $('.card_add_form').hide();
        $('.check_tranfer').hide();
        $('.card_process_form').hide();
        $('.card_edit_form').show();
        $('input[name=set_default]').removeAttr('checked');
        $('.credit_date').mask('99/99');
        $(".phone").mask("(999) 999-9999");
//        $('input[name=card_number]').mask();

        $('.form_title').html('Edit Credit Card "<span class="edit_me" data-id="' + id + '" data-func="save_view_as">' + inf1 + '</span>"' + inf2);
        $('.save_billing_changes').addClass('hide');

        //edit
        $('.save_changed_info').data('id', id).hide();
    });

    //edit on click
    $('body').on('click', '.edit_me', function() {
        var text = $(this).text(),
                id = $(this).data('id'),
                func = $(this).data('func');
        $(this).hide();
        $('<input class="save_val small" type="text" value="' + text + '"><input type="button" value="Save" class="fast_save_me" data-func="' + func + '" data-id="' + id + '">').insertAfter($(this));
    }).on('click', '.fast_save_me', function() {
        var $this = $(this),
                func = $(this).data('func'),
                id = $(this).data('id'),
                val = $.trim($(this).prev().val());
        if (val) {
            $.post('/admin/sales/ajax', {
                func: func,
                id: id,
                val: val
            }, function() {
                $this.prev().remove();
                $this.remove();
                $('.edit_me').text(val).show();
                $('.clickable.ui-tabs-selected').trigger('click');
            });
        }
    }).on('click', '.show_user_cards', function() {
        var elem = $(this).next().find('td'),
                hide = elem.is('.hide');
        $('.all_user_cards').addClass('hide');
        if (hide) {
            elem.removeClass('hide');
        }
    }).on('click', '.slide_payments', function() {
        var next = $(this).next(),
                vis = next.is(':visible');
        $.each($('.slide_payments'), function() {
            $(this).next().slideUp();
        });
        if (!vis) {
            next.slideDown().css('background-color', '#FFF');
        }
    }).on('click', '.edit_manual_payment', function(e) {
        var id = $(this).data('id');
        e.stopPropagation();
        $('.modal_bg').fadeIn();
        $.post('/admin/sales/ajax', {
            func: 'edit_transaction',
            id: id,
            cid: $('.task_details').prev().data('cid'),
            uid: $('.task_details').prev().data('uid')
        }, function(data) {
            $('.loading').hide();
            $('.modal_bg .contents').html(data);
            $(".datepicker_mod").datepicker({
                changeMonth: true,
                changeYear: true
            });
        });
    });

    function get_card_details(id) {
        $.post('/admin/sales/ajax', {
            'func': 'get_card_details',
            'id': id
        }, function(data) {
            if (data.info) {
                var bill_id = parseInt(data.info.billing_id);
                get_billing_list(bill_id);
                $('input[name="card_number"]:visible').val(data.info.card_number);
                $('input[name=card_description]:visible').val(data.info.title);
                $('input[name=full_card_name]:visible').val(data.info.full_card_name);
                $('input[name=cc_date]:visible').val(data.info.exp_date);
                $('input[name=cc_ccv]:visible').val(data.info.ccv);
                $('input[name=full_user_name]:visible').val(data.info.full_user_name);
                if (data.info.default == 1) {
                    $('input[name=set_default]:visible').attr('checked', 'checked');
                } else {
                    $('input[name=set_default]:visible').removeAttr('checked');
                }
                //validate
                $('input[name=card_number]').keyup();
                if (!bill_id) {
                    //set from card data
                    $('input[name="billing_company"]:visible').val(data.info.bill_company);
                    $('input[name="billing_fname"]:visible').val(data.info.bill_fname);
                    $('input[name="billing_lname"]:visible').val(data.info.bill_lname);
                    $('input[name="full_card_name"]:visible').val(data.info.full_card_name);
                    $('input[name="billing_address"]:visible').val(data.info.bill_address);
                    $('input[name="billing_address2"]:visible').val(data.info.bill_address2);
                    $('input[name="billing_city"]:visible').val(data.info.bill_city);
                    $('input[name="billing_state"]:visible').val(data.info.bill_state);
                    $('input[name="billing_suite"]:visible').val(data.info.bill_suite);
                    $('input[name="billing_zip"]:visible').val(data.info.bill_zip);
                    $('input[name="billing_country"]:visible').val(data.info.bill_country);
                    $('input[name="billing_email"]:visible').val(data.info.bill_email);
                    $('input[name="billing_phone"]:visible').val(data.info.bill_phone);
                    $('input[name="billing_phone_ext"]:visible').val(data.info.bill_phone_ext);
                }
            }
        }, 'json');
    }

    /*
     * Additional email/phone action
     */
    $('.industry_field,.additional_phone,.additional_email').live('mouseenter', function() {
        $(this).find('.iconsweet.right').css('opacity', 1);
    }).live('mouseleave', function() {
        $(this).find('.iconsweet.right').css('opacity', 0);
    });

    /*
     * Show additional email/phone form
     */
    $('.additional_phone .iconsweet, .additional_email .iconsweet').live('click', function(e) {
        $('.aval_company_users').hide();
        $('.edit_industry').remove();

        var $this = $(this).parent(),
                class_name = $this.attr('class'),
                type = (class_name === 'additional_phone') ? 'phone' : 'email';

        $.post('/admin/sales/ajax', {
            'func': 'get_fast_editform',
            'type': type,
            'uid': $this.parents('.print_row').data('uid')
        }, function(data) {
            $this.append('<div class="edit_industry">' + data + '</div>');
            $(".phone").mask("(999) 999-9999");
        });
    });

    /*
     * Add fast additional fields
     */
    $('.add_fast_additional').live('click', function() {
        var type = $(this).data('type');
        if (type === 'email') {
            $('<div class="all_additional" data-type="email"><input type="text" name="addtional_email" value=""><br class="clear"></div>').insertAfter($(this).parent().find('.all_additional:last'));
        } else {
            $('<div class="all_additional" data-type="phone"> <select name="addtional_phone_type"><option value="cell">cell</option><option value="home">home</option><option value="office">office</option></select> <input type="text" name="addtional_phone" class="phone"> <input type="text" name="addtional_phone_ext" placeholder="ext" style="width: 30px"><br class="clear"></div>').insertAfter($(this).parent().find('.all_additional:last'));
            $(".phone").mask("(999) 999-9999");
        }
    });

    /*
     * Save additional email/phone
     */
    $('input[name=save_additional_phone], input[name=save_additional_email]').live('click', function() {
        var $this = $(this),
                type = ($this.is('input[name=save_additional_phone]')) ? 'phone' : 'email',
                func = $this.attr('name'),
                uid = $this.parents('.print_row').data('uid'),
                values = {},
                filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
                err = false;
        $.each($this.parents('td').find('.all_additional'), function(i) {
            if ($(this).data('type') === 'phone') {
                values[i] = {
                    'type': $(this).find('select[name=addtional_phone_type]').val(),
                    'num': $(this).find('input[name=addtional_phone]').val(),
                    'ext': $(this).find('input[name=addtional_phone_ext]').val(),
                }
            } else {
                var email = $(this).find('input').val();
                if ($.trim(email)) {
                    if (!filter.test(email)) {
                        $(this).find('input').css('border', '1px solid red').css('background', '#FFCCCC');
                        err = true;
                    } else {
                        values[i] = {
                            'email': email
                        }
                    }
                }
            }
        });

        if (!err) {
            $.post('/admin/sales/ajax', {
                'func': func,
                'uid': uid,
                'values': values
            }, function(data) {
                if (data.text_rez) {
                    $this.parents('.additional_' + type).find('.v1').html(data.text_rez);
                    $this.next().trigger('click');
                } else {
                    alert('Server error!');
                }
            }, 'json');
        }

    });

    // Samples Collection. Create content pack from items form
    $('input[name=create_content]').live('click', function() {
        $('.sel_products').html('<img src="/images/admin/table-loader.gif" class="load">');
        $('.edit_industry').addClass('details');
        $('.nav_buttons').hide();
        $('input[name=industry_item]').removeAttr('checked');
        $('.used').text('0');
        $(this).parent().html('<input type="text" name="content_name" placeholder="Content Name"> <input type="button" name="save_new_content" value="Add"> <input type="button" name="cancel_new_content" value="Cancel">');
        $.post('/admin/sales/ajax', {
            'func': 'create_content_block'
        }, function(data) {
            $('.sel_products').html(data);
        });
    });

    /*
     * Samples Collection. Create content pack from items save
     */
    $('input[name=save_new_content]').live('click', function() {
        var name = $('input[name=content_name]').val(),
                checked = $('input.ind_new:checked').serialize();
        if (!name) {
            alert('Fill "Content Name" field');
            return false;
        } else if (!checked) {
            alert('Check some items for content');
            return false;
        } else {
            $.post('/admin/sales/ajax', {
                'func': 'save_new_content',
                'name': name,
                'checked': checked
            }, function(data) {
                if (data.id) {
                    $('input[name=industry_collection]:checked').removeAttr('checked');
                    $('.collections').append('<label><input type="radio" name="industry_collection" value="' + data.id + '" class="left"><small>' + name + '</small><a class="iconsweet tip_north remove_collection" data-id="' + data.id + '" style="float: right; margin-right: 4px" original-title="Delete">X</a></label>');
                    if ($('.collections').is('.hide')) {
                        $('.saved_button').trigger('click');
                    }
                    $('.edit_industry').removeClass('details');
                    $('.sel_products').html('');
                    $('.new_ind_form').html('<input type="button" name="create_content" value="+ Create Content">');
                    $('.nav_buttons').show();
                } else {
                    alert('Server Error!');
                }
            }, 'json');
        }
    });

    /*
     * Samples Collection. Show saved content packs
     */
    $('.saved_button').live('click', function() {
        if ($('.content_industry_list').is('.hide')) {
            $('.content_industry_list').removeClass('hide');
            $('.collections').addClass('hide');
            $(this).html('Saved Contents »');
        } else {
            $('.content_industry_list').addClass('hide');
            $('.collections').removeClass('hide');
            $(this).html('Industries »');
        }
    });

    /*
     * Samples Collection. Fast Search by items
     */
    $('input[name=content_search]').live('keyup', function() {
        var val = $(this).val();
        $.each($('.one_item'), function() {
            if ($(this).find('.title').text().toLowerCase().indexOf(val.toLowerCase()) != '-1') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('.sel_products > div').show();
        $.each($('.sel_products > div'), function() {
            if ($(this).find('.one_item:visible').length === 0) {
                $(this).hide();
            }
        });
    });

    /*
     * Samples Collection. Fast Search by items
     */
    $('input[name=content_search]').live('click', function() {
        $(this).trigger('keyup');
    });

    /*
     * Fedex labels Print button
     */
    $('.print_button').live('click', function() {
        $(this).text('Printed');
    });

    /*
     * Samples Collection. Check saved collection, show items inside
     */
    $('input[name=industry_collection]').live('change', function() {
        var id = $(this).val();
        $('input[name=industry_item]').removeAttr('checked');
        $.post('/admin/sales/ajax', {
            'func': 'get_sample_collection',
            'id': id
        }, function(data) {
            $('.edit_industry').addClass('details');
            $('.sel_products').html(data);
        });
    });

    /*
     * Samples Collection. Change selected items in collection 
     */
    $('input.ind_new').live('change', function() {
        var $this = $(this),
                id = $this.val(),
                type_index = $this.data('type'),
                type = 0,
                counter = $('input[name=industry_item][value="' + type_index + '"]').next().find('.used');
        if ($this.is(':checked')) {
            type = 1;
            counter.text(parseInt(counter.text()) + 1);
            $('input[name=industry_item][value="' + type_index + '"]').attr('checked', 'checked');
        } else {
            counter.text(parseInt(counter.text()) - 1);
            if (counter.text() == 0) {
                $('input[name=industry_item][value="' + type_index + '"]').removeAttr('checked');
            }
        }
        $.post('/admin/sales/ajax', {
            'func': 'change_need_collection',
            'id': id,
            'type_index': type_index,
            'type': type
        });
    });

    /*
     * Samples Collection. Remove collection
     */
    $('.remove_collection').live('click', function() {
        var id = $(this).data('id');
        if (confirm('Remove content?')) {
            $(this).parents('label').remove();
            $.post('/admin/sales/ajax', {
                'func': 'remove_sample_collection',
                'id': id
            });
        }
    });

    /*
     * Samples Collection. Cancel adding new content without saving
     */
    $('input[name=cancel_new_content]').live('click', function() {
        $('.new_ind_form').html('<input type="button" name="create_content" value="+ Create Content">');
        $('.nav_buttons').show();
        $('.edit_industry').removeClass('details');
        $('.sel_products').html('');
        $('input[name=industry_item]').removeAttr('checked');
        $('.used').text('0');
    });


    /* 
     * Advanced search. Search "Data entry" filter 
     */
    $('input[name=print_filter]').live('change', function() {
        var val = $(this).val();
        $('.search_menu input[type=checkbox]').removeAttr('checked');
        $('.disable_advanced_search').attr('checked', 'checked');
        $('.search_menu input[type=text]').val('');
        $.post('/admin/print/ajax', {
            'func': 'print_dataentry_filter',
            'val': val
        }, function(data) {
            $('.buttons_block').hide();
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
        });
    });

    /*
     * Task details. Change user types (processed,revision)
     */
    $('input[name=set_user_processed], input[name=set_user_revision]').live('change', function() {
        var val = ($(this).is(':checked')) ? 1 : 0;
        var type = $(this).data('type');

        $.post('/admin/users/ajax', {
            'func': 'change_user_status',
            'val': val,
            'type': type,
            'uid': $('.task_details').prev().data('uid')
        });
    });

    /*
     * Advanced search. Clear date field
     */
    $('.clear_search_date').live('click', function() {
        $(this).hide();
        $('input[name=datepick_hide]').val('');
        $('input[name=datepick]').val('');
        $('input[name=search_print]').trigger('keyup');
        $('input[name=entry_type]').trigger('change');
    });

    /*
     * Scroll tasks
     */
    $('.job_slider .left_arrow').live('click', function() {
        $('.job_menu').animate({
            left: '0'
        }, 500);
    });
    $('.job_slider .right_arrow').live('click', function() {
        $('.job_menu').css('left', 'auto').css('right', 0);
    });

    /*
     * Add new customer from search form
     */
    $('input[name=add_new_contact]').click(function() {

        $('.modal_bg').show();
        $.post('/admin/sales/ajax', {
            'func': 'get_new_customer_form'
        }, function(data) {
            $('.loading').hide();
            $('.modal_bg .contents').html(data);
            $('#phone').mask("(999) 999-9999");
            $('input[name=zip]').numeric();
        });

    });

    /*
     * Cloce add new user form
     */
    $('.modal_bg').on('click', 'input[name=cancel_add_new_contact]', function() {
        $('.close_modal').trigger('click');
    });

    /*
     * Add user - search same users, company...
     */
    $('.modal_bg').on('blur', '.add_customer_form input[type=text]', function() {
        $.post('/admin/sales/ajax', {
            'func': 'check_add_user',
            'data': $('.add_customer_form').serialize()
        }, function(data) {

            var results = Object.keys(data.counts).length;
            if (results) {
                $('.modal_bg .error').html('*We find similar results in:<br><br>');
                $.each(data.counts, function(i, v) {
                    $('.modal_bg .error').append(v.company + '<br>');
                });
            } else {
                $('.modal_bg .error').html('');
            }
        }, 'json');

    });

    /*
     * Add new user
     */

    $('.modal_bg').on('click', 'input[name=add_new_user]', function() {
        $('.add_customer_form .error').text('');
        $.post('/admin/sales/ajax', {
            'func': 'add_new_user',
            'data': $('.add_customer_form').serialize()
        }, function(data) {
            if (data.err) {
                $('.add_customer_form .error').text('*' + data.err);
            } else if (data.ok) {
                $('.add_customer_form').html('Request was successfully added.');
                setTimeout(function() {
                    $('.close_modal').trigger('click');
                    $('input[name=search_print]').trigger('keyup');
                }, 2000);
            } else {
                alert('Server error!');
            }
        }, 'json');
    });

    /*
     * Enter credit card - separate fields
     */

    $('.edit_user_address').live('click', function() {
        $('.edit_user_address_form').show();
        $(".phone").mask("(999) 999-9999");
        $(".zip").mask("99999");
        $(this).parent().hide();
    });

    /*
     * Close Job modal window
     */
    $('input[name=close_modal_window]').live('click', function() {
        $('.close_modal').trigger('click');
    });

    /*
     * Close modal window
     */
    $('.close_modal').click(function() {
        $('.modal_bg').hide();
        $('.modal_bg .contents').html('');
        $('.modal_bg .err').html('');
        $('.loading').show();
    });

    $('input[name=save_user_address_form]').live('click', function() {
        var $this = $(this),
                fname = $('input[name=addr_first_name]').val(),
                lname = $('input[name=addr_last_name]').val(),
                phone = $('input[name=addr_phone]').val(),
                phone_ext = $('input[name=addr_phone_ext]').val(),
                company = $('input[name=addr_company]').val(),
                id = $(this).data('id');
        $.post('/admin/sales/ajax', {
            'func': 'save_user_addr',
            'id': id,
            'req_id': $(this).parents('.task_details').prev().data('id'),
            'company': company,
            'street': $('input[name=addr_street]').val(),
            'city': $('input[name=addr_city]').val(),
            'state': $('input[name=addr_state]').val(),
            'first_name': fname,
            'last_name': lname,
            'zipcode': $('input[name=addr_zipcode]').val(),
            'phone': phone,
            'phone_ext': phone_ext
        }, function(data) {
            if (data.address) {
                $('.compleate_user_address').html(data.address);
                $('.edit_user_address_form').hide();
                $('.edit_user_address_form').prev().show();
                $this.parents('.task_details').prev().find('td:eq(3)').text(company);

                $this.parents('.task_details').prev().find('.set_username').text(fname + ' ' + lname);
                $this.parents('.task_details').prev().find('.additional_phone .v1').text(phone);
                if (phone_ext) {
                    $this.parents('.task_details').prev().find('.additional_phone .v1').append(' ext ' + phone_ext);
                }
            } else {
                alert('Server error!');
            }
        }, 'json');
    });

    $('input[name=cancel_user_address_form]').live('click', function() {
        $('.edit_user_address_form').hide();
        $('.edit_user_address_form').prev().show();
    });

    $('.edit_user_address_form input').live('keyup', function() {
        $(this).parent().find('input[name=save_user_address_form]').show();
    });

    $('input[name=entry_type]').change(function() {
        var type = [],
                date = $('input[name=datepick_hide]').val();
        $.each($('input[name=entry_type]:checked'), function(i) {
            type[i] = $(this).val();
        });

        $.post('/admin/sales/ajax', {
            'func': 'set_data_entry_session',
            'type': type,
            'date': date
        }, function() {
            document.location = '/admin/Sales/dataentry';
        });

    });

    $('.eye_company_change').live('click', function() {
        var $this = $(this),
                active = '/images/admin/eye.png',
                not_active = '/images/admin/no_eye.png',
                type = '0';

        if ($this.attr('src') === active) {
            $this.attr('src', not_active);
            $this.attr('title', 'Enable to be viewable in Active Customers tab');
        } else {
            $this.attr('src', active);
            $this.attr('title', 'Disable displaying at Active Customers tab');
            type = '1';
        }
        $.post('/admin/sales/ajax', {
            'func': 'eye_company_change',
            'type': type,
            'id': $this.parents('.task_details').prev().data('cid'),
            'req_id': $this.parents('.task_details').prev().data('id'),
        }, function(data) {
            if (!data.ok) {
                alert('Server error!');
            } else if (data.ok == 'err_reqired') {
                alert('We are sorry but you can\'t enable this option because there are notes with Action Required status. Please check notes section.');
                $this.attr('src', active);
            }
        }, 'json');
    });

    $('.show_trans_details').live('click', function() {
        $('.show_trans_details').removeAttr('style');
        if (!$(this).next().is(':visible')) {
            $('.trans_details').hide();
            $(this).next().show();
            $(this).css('font-weight', 'bold');
        } else {
            $('.trans_details').hide();
        }
    });


    $('body').on('click', '.add_check_trans', function() {
        $('.modal_bg').fadeIn();
        $.post('/admin/sales/ajax', {
            func: 'get_add_trans_form',
            uid: $('.task_details').prev().data('uid'),
            cid: $('.task_details').prev().data('cid'),
            active: $('.clickable.ui-tabs-selected').data('id')
        }, function(data) {
            $('.loading').hide();
            $('.modal_bg .contents').html(data);
        });
    }).on('click', '.redestribute_payment', function(e) {
        var id = $(this).data('id'),
                trans = $(this).parents('tr').find('span[data-trans!=""]').data('trans');
        e.stopPropagation();
        $('.modal_bg').fadeIn();
        $.post('/admin/sales/ajax', {
            func: 'redistribute_payment',
            id: id,
            cid: $('.task_details').prev().data('cid'),
            trans: trans
        }, function(data) {
            $('.loading').hide();
            $('.modal_bg .contents').html(data);
            $(".numeric").numeric();
        });
    });

    $('.modal_bg').on('change', 'select[name=trans_type]', function() {
        var val = $(this).val();
        $('.block_trans > div').addClass('hide');
        $('.block_' + val).removeClass('hide');
        $(".datepicker_mod").datepicker({
            changeMonth: true,
            changeYear: true
        });
        $('input[name=trans_amount],.numeric').numeric();

        if (val == 'credit' || val == 'failed' || val == 'confirm' || val == '') {
            $('.set_order_total').addClass('hide');
        } else {
            $('.set_order_total').removeClass('hide');
        }

    }).on('change', 'select[name=trans_credit_card]', function() {
        var val = $(this).val();
        if (val === 'add') {
            $('.add_new_card').removeClass('hide');
        } else {
            $('.add_new_card').addClass('hide');
        }

    }).on('click', 'input[name=add_card_transaction]', function() {
        var select_job_trans = $('select[name=select_job_trans]').val(),
                trans_date = $('input[name=trans_date]').val(),
                card = $('select[name=trans_credit_card]').val(),
                add_card_name = $('input[name=add_card_name]').val(),
                add_card_type = $('select[name=add_card_type]').val(),
                last_digits = $('input[name=last_digits]').val(),
                trans_amount = $('input[name=trans_amount]').val(),
                trans_note = $('textarea[name=trans_note]').val(),
                set_order_total = $('input[name=set_trans_total]:visible').val() || '';

        $('.trans_card_error').text('');

        if (!trans_date) {
            $('.trans_card_error').text('*Date field is empty');
            return false;
        } else if (!card) {
            $('.trans_card_error').text('*Please, select or add credit card');
            return false;
        } else if (!trans_amount) {
            $('.trans_card_error').text('*Amount field is empty');
            return false;
        } else if (card === 'add') {
            if (!add_card_name) {
                $('.trans_card_error').text('*Enter Name on Card');
                return false;
            } else if (!last_digits) {
                $('.trans_card_error').text('*Enter Last 4 digits');
                return false;
            }
        }
        $.post('/admin/sales/ajax', {
            func: 'add_card_transaction',
            job_id: select_job_trans,
            date: trans_date,
            card: card,
            add_card_name: add_card_name,
            add_card_type: add_card_type,
            last_digits: last_digits,
            amount: trans_amount,
            trans_note: trans_note,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid'),
            order_total: set_order_total
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction added.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        });

    }).on('change', 'select[name=select_job_trans]', function() {
        var val = $(this).val();
        $.post('/admin/sales/ajax', {
            func: 'getJobCC',
            card: val
        }, function(data) {
            $('select[name=trans_credit_card] option').remove();
            $('select[name=trans_credit_card]').append('<option></option>');
            if (data) {
                $.each(data, function(i, v) {
                    var card = v.card_number.slice(-4);
                    $('select[name=trans_credit_card]').append('<option value="' + v.id + '">' + v.view_as + ' - X' + card + '</option>');
                });
            }
            $('select[name=trans_credit_card]').append('<option value="add">Add New...</option>');
        }, 'json');

    }).on('click', 'input[name=add_cash_transaction]', function() {
        var date = $('input[name=cash_trans_date]').val(),
                amount = $('input[name=cash_trans_amount]').val(),
                note = $('textarea[name=cash_trans_note]').val(),
                select_job_trans = $('select[name=select_job_trans]').val(),
                set_order_total = $('input[name=set_trans_total]:visible').val() || '';
        $('.trans_cash_error').text('');
        if (!date) {
            $('.trans_cash_error').text('*Date field is empty');
            return false;
        } else if (!amount) {
            $('.trans_cash_error').text('*Amount field is empty');
            return false;
        }
        $.post('/admin/sales/ajax', {
            func: 'add_cash_transaction',
            job_id: select_job_trans,
            date: date,
            amount: amount,
            note: note,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid'),
            order_total: set_order_total
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction added.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        }, 'json');

    }).on('click', 'input[name=add_misc_transaction]', function() {
        var date = $('input[name=misc_trans_date]').val(),
                amount = $('input[name=misc_trans_amount]').val(),
                note = $('textarea[name=misc_trans_note]').val(),
                select_job_trans = $('select[name=select_job_trans]').val(),
                set_order_total = $('input[name=set_trans_total]:visible').val() || '';
        $('.trans_misc_error').text('');
        if (!date) {
            $('.trans_misc_error').text('*Date field is empty');
            return false;
        } else if (!amount) {
            $('.trans_misc_error').text('*Amount field is empty');
            return false;
        }
        $.post('/admin/sales/ajax', {
            func: 'add_misc_transaction',
            job_id: select_job_trans,
            date: date,
            amount: amount,
            note: note,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid'),
            order_total: set_order_total
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction added.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        }, 'json');

    }).on('click', 'input[name=add_check_transaction]', function() {
        var date = $('input[name=check_trans_date]').val(),
                amount = $('input[name=check_trans_amount]').val(),
                number = $('input[name=check_number]').val(),
                note = $('textarea[name=check_note]').val(),
                select_job_trans = $('select[name=select_job_trans]').val(),
                set_order_total = $('input[name=set_trans_total]:visible').val() || '';
        $('.trans_check_error').text('');
        if (!date) {
            $('.trans_check_error').text('*Date field is empty');
            return false;
        } else if (!amount) {
            $('.trans_check_error').text('*Amount field is empty');
            return false;
        } else if (!number) {
            $('.trans_check_error').text('*Check# is empty');
            return false;
        }
        $.post('/admin/sales/ajax', {
            func: 'add_check_transaction',
            job_id: select_job_trans,
            date: date,
            amount: amount,
            number: number,
            note: note,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid'),
            order_total: set_order_total
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction added.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        }, 'json');
    }).on('click', 'input[name=edit_transaction]', function() {
        var type = $('select[name=editing_trans_type]').val(),
                trans_date = $('input[name=trans_date]').val(),
                trans_amount = $('input[name=trans_amount]').val(),
                trans_note = $('textarea[name=trans_note]').val(),
                job = $('select[name=select_job_trans]').val(),
                id = $(this).data('id'),
                check = $('input[name=check_number]').val(),
                card = $('select[name=trans_credit_card]').val(),
                add_card_name = $('input[name=add_card_name]').val(),
                add_card_type = $('select[name=add_card_type]').val(),
                last_digits = $('input[name=last_digits]').val();
        $('.trans_card_error').text('');

        if (!trans_date) {
            $('.trans_card_error').text('*Date field is empty');
            return false;
        } else if (!trans_amount) {
            $('.trans_card_error').text('*Amount field is empty');
            return false;
        }
        $.post('/admin/sales/ajax', {
            func: 'edit_save_transaction',
            date: trans_date,
            type: type,
            amount: trans_amount,
            trans_note: trans_note,
            job: job,
            id: id,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid'),
            check: check,
            card: card,
            add_card_name: add_card_name,
            add_card_type: add_card_type,
            last_digits: last_digits
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction saved.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        });

    }).on('click', '.split_redistr', function() {
        $(this).parents('tr').next().show();
        $(this).hide();
    }).on('click', '.remove_redistr_job', function() {
        $('.split_redistr').show();
        $(this).parents('tr').hide();
        $('input[name=redis_summ2]').val('').keyup();
    }).on('keyup', 'input[name=redis_summ1],input[name=redis_summ2]', function() {
        var val1 = parseFloat($('input[name=redis_summ1]').val()) || 0,
                val2 = parseFloat($('input[name=redis_summ2]').val()) || 0,
                aval = parseFloat($('input[name=redist_amount]').val()),
                can_pay = 0, balance = 0;
        if (val1 + val2 > aval) {
            if ($(this).is('input[name=redis_summ2]')) {
                can_pay = aval - val1;
                val2 = can_pay;
            } else {
                can_pay = aval - val2;
                val1 = can_pay;
            }
            $(this).val(can_pay);
        }

        balance = aval - (val1 + val2);
        $('input[name=trans_balance]').val(balance);

    }).on('click', '.run_redistribute', function() {
        var job1 = $('select[name=job1]').val(),
                job2 = ($('select[name=job2]').is(':visible')) ? $('select[name=job2]').val() : '',
                summ1 = $('input[name=redis_summ1]').val() || 0,
                summ2 = $('input[name=redis_summ2]').val() || 0,
                counts = ($('input[name=redis_summ2]').is(':visible')) ? 2 : 1,
                small_descr = $('.trans_code').text();
        $('.redist_err').text('');
        if (!job1) {
            $('.redist_err').text('Please select a job');
        } else if (!summ1) {
            $('.redist_err').text('Please enter all amounts');
        } else if ($('input[name=redis_summ2]').is(':visible') && !summ2) {
            $('.redist_err').text('Please enter all amounts');
        } else {
            $(this).hide();
            $('<img src="/images/admin/info-loader.gif" class="right load">').insertAfter($(this));
            $.post('/admin/sales/ajax', {
                func: 'run_redistribute_payment',
                job1: job1,
                job2: job2,
                summ1: summ1,
                summ2: summ2,
                base_id: $(this).data('id'),
                count_redistr: counts,
                small_descr: small_descr,
                req_id: $('.task_details').prev().data('id'),
                comp_id: $('.task_details').prev().data('cid')
            }, function() {
                $('.modal_bg .contents').html('Transaction redesrtibuted.');
                setTimeout(function() {
                    $('.close_modal').trigger('click');
                    $('.clickable.ui-tabs-selected').trigger('click');
                }, 2500);
            });
        }

    }).on('click', 'input[name=add_confirm_transaction]', function() {

        var date = $('input[name=confirm_trans_date]').val(),
                amount = $('input[name=confirm_trans_amount]').val(),
                select_job_trans = $('select[name=select_job_trans]').val();
        $('.trans_confirm_error').text('');
        if (!date) {
            $('.trans_confirm_error').text('*Date field is empty');
            return false;
        } else if (!amount) {
            $('.trans_confirm_error').text('*Amount field is empty');
            return false;
        }
        $.post('/admin/sales/ajax', {
            func: 'add_confirm_transaction',
            job_id: select_job_trans,
            date: date,
            amount: amount,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid')
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction added.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        }, 'json');

    }).on('click', 'input[name=add_failed_transaction]', function() {

        var date = $('input[name=failed_trans_date]').val(),
                amount = $('input[name=failed_trans_amount]').val(),
                note = $('textarea[name=failed_note]').val(),
                select_job_trans = $('select[name=select_job_trans]').val();
        $('.trans_failed_error').text('');
        if (!date) {
            $('.trans_failed_error').text('*Date field is empty');
            return false;
        } else if (!amount) {
            $('.trans_failed_error').text('*Amount field is empty');
            return false;
        }
        $.post('/admin/sales/ajax', {
            func: 'add_failed_transaction',
            job_id: select_job_trans,
            date: date,
            amount: amount,
            note: note,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid')
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction added.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        }, 'json');

    }).on('click', 'input[name=add_credit_transaction]', function() {

        var date = $('input[name=credit_trans_date]').val(),
                amount = $('input[name=credit_trans_amount]').val(),
                note = $('textarea[name=credit_note]').val(),
                select_job_trans = $('select[name=select_job_trans]').val();
        $('.trans_credit_error').text('');
        if (!date) {
            $('.trans_credit_error').text('*Date field is empty');
            return false;
        } else if (!amount) {
            $('.trans_credit_error').text('*Amount field is empty');
            return false;
        }
        $.post('/admin/sales/ajax', {
            func: 'add_credit_transaction',
            job_id: select_job_trans,
            date: date,
            amount: amount,
            note: note,
            req_id: $('.task_details').prev().data('id'),
            comp_id: $('.task_details').prev().data('cid')
        }, function(data) {
            $('.modal_bg .contents').html('<b>Transaction added.</b>');
            setTimeout(function() {
                $('.close_modal').trigger('click');
                $('.clickable.ui-tabs-selected').trigger('click');
            }, 2000);
        }, 'json');

    }).on('change', 'select[name=editing_trans_type]', function() {
        var val = $(this).val();
        $('input[name=edit_transaction]').show();
        $('.for_card_edits').hide();
        $('.for_check_edits').hide();

        if (val == 'manual_cc') {
            $('.for_card_edits').show();
        } else if (val == 'manual_check') {
            $('.for_check_edits').show();
        } else if (val == '') {
            $('input[name=edit_transaction]').hide();
        }
    });

    $('body').on('click', '.show_more_note', function() {
        $(this).next().removeClass('hide');
        $(this).next().append('<span class="hide_more_note"><i>Hide</i></span>');
        $(this).hide();
    }).on('click', '.hide_more_note', function() {
        $(this).parent().addClass('hide');
        $(this).parent().prev().show();
        $(this).remove();
    });
    

});