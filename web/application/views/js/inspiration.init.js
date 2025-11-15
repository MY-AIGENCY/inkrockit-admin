$(function() {
    var add_blick = 0;
    var mouse_in = '';

    $('body').mouseover(function(e) {
        mouse_in = e.target;
    });

    //Gallery
    function update_category_arrows() {
        var current = $('.all_thumbs .current').parent();
        //prev
        var prev = $('.all_thumbs li').last().children().first().children();
        $('.go_prev').find('img').attr('src', prev.attr('src'));
        //next
        var next = current.next().children().first('span').children();
        $('.go_next').find('img').attr('src', next.attr('src'));

        //add preload
        $.each($('.all_thumbs li:lt(5)'), function() {
            img_preload = $(this).find('img').attr('rel');
            if ($('.next_preload').find('img[src="' + img_preload + '"]').length === 0) {
                $('.next_preload').append('<img src="' + img_preload + '">');
                if ($('.next_preload img').length > 14) {
                    $('.next_preload img').first().remove();
                }
            }
        });
        $.each($('.all_thumbs li').slice(-5), function() {
            img_preload = $(this).find('img').attr('rel');
            if ($('.next_preload').find('img[src="' + img_preload + '"]').length === 0) {
                $('.next_preload').append('<img src="' + img_preload + '">');
                if ($('.next_preload img').length > 14) {
                    $('.next_preload img').first().remove();
                }
            }
        });
    }

    function blink_arrows() {
        $('.go_next, .go_prev').show().stop().animate({
            opacity: 1
        }, 400, function() {

            if (!$(mouse_in).is('.go_prev') && !$(mouse_in).parent().is('.go_prev')) {
                $('.go_prev').delay(1000).animate({
                    opacity: 0
                }, 200, function() {
                    $(this).hide();
                });
            }
            if (!$(mouse_in).is('.go_next') && !$(mouse_in).parent().is('.go_next')) {
                $('.go_next').delay(1000).animate({
                    opacity: 0
                }, 200, function() {
                    $(this).hide();
                });
            }
        });
    }

    function update_left_item_info(item_id) {
        $.post('/ajax', {'fn': 'getLeftItemInfo', 'item_id': item_id}, function(data) {
            $('.mainLeftMenu').html(data);
        });
    }


    $('body').on('click', '.mainLeftMenuTop > .open_status', function() {
        var more_info = $(this).parent();
        if (more_info.is('.opened')) {
            more_info.removeClass('opened');
            more_info.find('.more_info').hide();
        } else {
            more_info.addClass('opened');
            more_info.find('.more_info').show();
        }
    }).on('click', '.insp_more_bock .open_status', function() {
        var li_elem = $(this).parent().parent();
        if (li_elem.is('.opened')) {
            li_elem.removeClass('opened');
        } else {
            li_elem.addClass('opened');
        }
    }).on('mouseenter', '.insp_gallery', function() {
        if (add_blick) {
            blink_arrows();
            add_blick = 0;
        }
    }).on('click', '.all_thumbs span', function(e, move) {
        $this = $(this);
        var pos = $this.parent().position();
        var big_src = $this.children().attr('rel');
        var item_class = null;
        $('.gallery_large .big').attr('src', big_src);

        if (move) {
            //move back
            $('.all_thumbs').animate({
                left: '-' + pos.left + 'px'
            }, 200, function() {
                //update category arrows
                update_category_arrows();
            });
            item_class = $this.parent().attr('class');
        } else {
            if ($this.parent().prev().is('li')) {
                //animate forvard
                $('.all_thumbs').animate({
                    left: '-' + pos.left + 'px'
                }, 200, function() {
                    //move element to end
                    prev_elems = $this.parent().prevAll();
                    if (prev_elems.length > 0) {
                        var add_elements = [];
                        $.each(prev_elems, function() {
                            add_elements.push($(this));
                        });
                        add_elements.reverse();
                        $.each(add_elements, function() {
                            $(this).appendTo('.all_thumbs');
                        });
                        $('.all_thumbs').css('left', 0);
                        //update category arrows
                        update_category_arrows();
                    }
                });
            }
            item_class = $this.parent().attr('class');
        }
        var item_parts = item_class.split('_');
        update_left_item_info(item_parts[1]);

        $('.all_thumbs .current').removeClass('current');
        $this.addClass('current');
    }).on('click', '.next_one', function() {
        if ($('.all_thumbs .current').next().is('span')) {
            $('.all_thumbs .current').next().trigger('click');
        } else {
            $('.all_thumbs .current').parent().next().find('span').first().trigger('click');  //scroll
        }
    }).on('click', '.prev_one', function() {
        if ($('.all_thumbs .current').prev().is('span')) {
            $('.all_thumbs .current').prev().trigger('click', ['true']); //move
        } else {
            if (!$('.all_thumbs .current').parent().prev().is('li')) {
                $('.all_thumbs li').last().prependTo('.all_thumbs');  //move
            }
            $('.all_thumbs .current').parent().prev().find('span').last().trigger('click', ['true']);  //scroll
        }
    }).on('click', '.go_prev', function() {
        if (!$('.all_thumbs .current').parent().prev().is('li')) {
            $('.all_thumbs li').last().prependTo('.all_thumbs')  //move
        }
        $('.all_thumbs .current').parent().prev().find('span').first().trigger('click', ['true'])  //scroll
    }).on('click', '.go_next', function() {
        var src = $(this).find('img').attr('src');
        $('.all_thumbs img[src="' + src + '"]').trigger('click');
    }).on('mouseleave', '.hot_area', function() {
        $(this).children().stop().animate({
            opacity: 0
        }, 300, function() {
            $(this).hide();
        });
    }).on('mouseover', '.hot_area', function() {
        $(this).children().show().stop().animate({
            opacity: 1
        }, 300);
    }).on('click', '.big', function() {
        var src = $(this).attr('src');
        $('.modal img').attr('src', src);
        $('.modal').fadeIn(600);
    }).on('click', '.modal', function() {
        $(this).fadeOut(600);
    }).on('mouseleave', '.main', function() {
        add_blick = 1;
    });


    /*--- Default ---*/
    var active = $('input[name=active_element]').val();
    if (active) {
        $('.item_' + active).children().first().trigger('click');
    }
    update_category_arrows();
    blink_arrows();

});