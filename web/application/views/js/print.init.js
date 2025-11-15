$(function() {

    /*--- Right menu ---*/
    $('body').on('mouseenter', '.customMenu_print', function() {
        var $this = $(this);
        $this.find('.coating').show();
        $this.css('backgroundColor', '#000');
        $this.find('.custommenuText').css('color', '#FFF');
    }).on('mouseleave', '.customMenu_print', function() {
        var $this = $(this);
        $this.find('.coating').hide();
        $this.css('backgroundColor', '#EBEBEB');
        $this.find('.custommenuText').css('color', '#222');
    }).on('click', '.customMenu_print', function() {
        var $this = $(this),
                $inp = $this.find('input');
        if ($inp.attr('disabled') === 'disabled') {
            return false;
        }
        if ($inp.attr('checked') === 'checked') {
            $inp.removeAttr('checked').change();
            //find end disable children
            if ($this.next().is('.sub')) {
                $this.next().find('input[type="checkbox"]').attr('disabled', 'disabled');
            }
        } else {
            $inp.attr('checked', 'checked').change();
            //find end enable children
            if ($this.next().is('.sub')) {
                $this.next().find('input[type="checkbox"]').removeAttr('disabled');
            }
        }
    }).on('mouseenter', '.insp_more_block li', function() {
        var $this = $(this);
        $this.css('backgroundColor', '#000');
        $this.find('.arrow_right').attr('src', '/images/customArrovSel.png');
        $this.find('.sub_select').show();
    }).on('mouseleave', '.insp_more_block li', function() {
        var $this = $(this);
        $this.css('backgroundColor', '#3c3c3c');
        $this.find('.arrow_right').attr('src', '/images/customArrov.png');
        $this.find('.sub_select').hide();
    }).on('click', '.remove', function() {
        var $this = $(this);
        if ($this.parents('.mainLeftMenuTop').prev().is('.mainLeftMenuTop')) {
            $this.parents('.mainLeftMenuTop').remove();
        }
    }).on('click', '.customMenu_print input', function() {
        var $inp = $(this);
        if ($inp.attr('checked') === 'checked') {
            $inp.removeAttr('checked');
        } else {
            $inp.attr('checked', 'checked');
        }
    }).on('change','.unselect_child',function(){
        var $this = $(this);
        if ($this.not(':checked')) {
            $this.parent().next().find('input[name^=sub_order]').removeAttr('checked');
        }
    }).on('click','.open_status', function(){
        $par = $(this).parent();
        if ($par.is('.opened')) {
            $par.removeClass('opened');
        } else {
            $('.open_status').parent().removeClass('opened');
            $par.addClass('opened');
        }
    });


    /*--- Print Gallery ---*/
    
    $('.pring_gall_next .button').click(function() {
        if ($('.item_prew_block.active').next().is('.item_prew_block')) {
            $('.item_prew_block.active').next().trigger('click');
        } else {
            $('.item_prew_block').first().trigger('click');
        }
    });
    $('.pring_gall_prew .button').click(function() {
        if ($('.item_prew_block.active').prev().is('.item_prew_block')) {
            $('.item_prew_block.active').prev().trigger('click');
        } else {
            $('.item_prew_block').last().trigger('click');
        }
    });
    
    $('body').on('click', '.item_prew_block', function() {
        if ($('.item_prew_block').is(':animated')) {
            return false;
        }
        $this = $(this);
        var anim_pos = false;
        if ($this.next().is('.active')) {
            anim_pos = '+=148';
            var last_left = parseInt($('.item_prew_block:first-child').css('left'));
            last_left = (last_left - 148) + 'px';
            $('.item_prew_block:last-child').css('left', last_left).insertBefore($('.item_prew_block:first-child'));
        } else if ($this.prev().is('.active')) {
            anim_pos = '-=148';
            var last_left = parseInt($('.item_prew_block:last-child').css('left'));
            last_left = (last_left + 148) + 'px';
            $('.item_prew_block:first-child').css('left', last_left).insertAfter($('.item_prew_block:last-child'));
        }
        var id = $this.data('id');
        if (anim_pos) {
            $('#print_big_preview img').attr('src', '/files/print/view/' + id + '.jpg');

            $('.item_prew_block').removeClass('active');
            $.each($('.item_prew_block .print_preview'), function() {
                var bg = $(this).css('background-image');
                var new_bg = bg.replace('active_preview', 'preview');
                $(this).css('background-image', new_bg);
            });

            $this.addClass('active');
            var bg = $this.find('.print_preview').css('background-image');
            var new_bg = bg.replace('preview', 'active_preview');
            $this.find('.print_preview').css('background-image', new_bg);

            //Animate
            $('.item_prew_block').animate({
                left: anim_pos
            }, 300);
        }
        //Get item data
        $.post('/print/ajax', {
            'func': 'get_item_data',
            'id': id
        }, function(data) {
            if (data) {
                $('.opened .more_info').html(data);
            }
        });
    }).on('change', '.sel_count', function() {
        /* Select Order Option */
        var val = $(this).val();
        if (val === 'on') {
            val = $(this).next().val();
        }
        $('.opened .item_quantity').text(val);
        if ($('.save_quantity').is(':checked')) {
            $.post('/print/ajax', {
                'func': 'item_quantity',
                'save': val
            });
        }
    }).on('change', 'input[name^=coating]', function() {
        var val = $(this).next().text();
        $('.opened .coating_sel').text(val);
    }).on('change', 'input[name^=finishes]', function() {
        var val = $(this).next().text();
        $('.opened .finishes_sel').text(val);
    }).on('change', '.items_custom_count', function() {
        if ($(this).prev().is(':checked')) {
            var val = $(this).val();
            $('.opened .item_quantity').text(val);
            $.post('/print/ajax', {
                'func': 'item_quantity',
                'save': val
            });
        }
    }).on('change', '.save_quantity', function() {
        var save = 0;
        if ($(this).is(':checked')) {
            save = $('.opened .item_quantity').text();
        }
        $.post('/print/ajax', {
            'func': 'item_quantity',
            'save': save
        });
    }).on('change', '.set_pockets_count', function() {
        var val = $(this).val();
        $('.opened .pockets_count').text(val);
    }).on('change', '.set_slits', function() {
        $this = $(this);
        var val = $(this).val();
        $.post('/print/ajax', {
            'func': 'get_slit_detail',
            'id': val
        }, function(data) {
            $('.opened .slits_name').text(data.name);
            $this.parents('dt').find('img').attr('src', '/files/print/slits/' + data.id + '.jpg');
            $this.parents('dt').find('p').html(data.description);
        }, 'json');
    }).on('change', '.set_inks', function() {
        $this = $(this);
        var val = $(this).val();
        $.post('/print/ajax', {
            'func': 'get_inks_detail',
            'id': val
        }, function(data) {
            $('.opened .ink_name').text(data.name);
            $this.parents('dt').find('p').html(data.description);
        }, 'json');
    }).on('change', '.set_paper', function() {
        $this = $(this);
        var val = $(this).val();
        $.post('/print/ajax', {
            'func': 'get_paper_detail',
            'id': val
        }, function(data) {
            $('.opened .paper_name').text(data.name);
            $this.parents('dt').find('p').html(data.description);
        }, 'json');
    });
    
    /*--- Defaults ---*/
    if ($('.mainLeftMenuTop').length > 0) {
        $('.mainLeftMenuTop').first().addClass('opened');
    }
    if ($('.item_prew_block').length > 0) {
        $('.item_prew_block.active').trigger('click'); //load active item to default
    }
});