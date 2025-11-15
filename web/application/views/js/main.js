$(function() {
    if ($(".block_listing").is(":visible")) {
        $(".block_listing").addClass("hide")
        $('.bottom_design').removeClass('cuurent_bottom_design')
    } else {
        $(".block_listing").removeClass("hide")
        $('.bottom_design').addClass('cuurent_bottom_design')

    }
//    var $height = $('.block_shipping_date_content').height();
//    if($height){
//        $('.block_left_shipping_info').css('min-height', $height);
//    }
    $('body').on('mouseenter', '.mainFooterFirstBlock', function() {
        $('.arrow_hover_fitst').css('display', 'block');

    }).on('mouseleave', '.mainFooterFirstBlock', function() {
        $('.arrow_hover_fitst').css('display', 'none');

    }).on('mouseenter', '.mainFooterSecondBlock', function() {
        $('.arrow_hover_second').css('display', 'block');

    }).on('mouseleave', '.mainFooterSecondBlock', function() {
        $('.arrow_hover_second').css('display', 'none');

    }).on('mouseenter', '.mainFooterThirdBlock', function() {
        $('.arrow_hover_third').css('display', 'block');

    }).on('mouseleave', '.mainFooterThirdBlock', function() {
        $('.arrow_hover_third').css('display', 'none');
    }).on('mouseenter', '.mainFooterdesign_it', function() {
        $('.arrow_hover_mail_it_firts').css('display', 'block');

    }).on('mouseleave', '.mainFooterdesign_it', function() {
        $('.arrow_hover_mail_it_firts').css('display', 'none');
    }).on('click', '.bottom_design', function(e) {
        if ($(".block_listing").is(":visible")) {
            $(".block_listing").addClass("hide")
            $('.bottom_design').removeClass('cuurent_bottom_design')
        } else {
            $(".block_listing").removeClass("hide")
            $('.bottom_design').addClass('cuurent_bottom_design')

        }
        e.stopPropagation();
    }).on('click', '.pages', function(e) {
        $(".block_listing").addClass("hide")
        $('.bottom_design').removeClass('cuurent_bottom_design');
        e.stopPropagation();
    })

    /*--- Login ---*/
    $('body').on('mouseenter', '.loginMainPageTest', function() {
        $('.logmenu').show();
        $('.firstLog').css('color', '#000').css('backgroundColor', '#FFF').css('paddingRight', '9px');
        $(this).next().css('borderLeft', 'none');
    }).on('mouseleave', '.loginMainPageTest', function() {
        $('.logmenu').hide();
        $('.firstLog').css('color', '#FFF').css('backgroundColor', 'transparent').css('paddingRight', '7px');
        $(this).next().css('borderLeft', '1px solid #FFF');
    });


    /*--- Basket ---*/
    $('body').on('mouseenter', '#shop', function() {
        $('.bascetslidebar').show();
        $(this).css('backgroundColor', '#FFF').css('margin', '3px 4px 0 0').css('padding', '6px 4px 10px 6px').css('color', '#000').css('borderRadius', '10px 10px 0 0');
    }).on('mouseleave', '#shop', function() {
        $('.bascetslidebar').hide();
        $(this).css('backgroundColor', 'transparent').css('margin', '10px 0 5px 0').css('padding', '0px 7px 3px 7px').css('color', '#FFF').css('borderRadius', '0');
    }).on('click', '#imgShop', function() {
        var detail = $(this).parent().parent().find('.details');
        if (detail.is(':visible')) {
            detail.hide();
            $(this).attr('src', '/images/insphide.png');
        } else {
            detail.show();
            $(this).attr('src', '/images/inspshow.png');
        }
    });


    /*--- Top Menu ---*/
    $('body').on('mouseenter', '.customMenu', function() {
        var coating = $(this).find('.coating');
        coating.show();
        $(this).css('backgroundColor', '#000').find('.custommenuText').css('color', '#FFF');
        $(this).find('.customArrow').children('img').attr('src', '/images/customArrovSel.png');
    }).on('mouseleave', '.customMenu', function() {
        var coating = $(this).find('.coating');
        coating.hide();
        $(this).css('backgroundColor', '#EBEBEB').find('.custommenuText').css('color', '#333');
        $(this).find('.customArrow').children('img').attr('src', '/images/customArrov.png');
    }).on('mouseenter', '.linkNavAll', function() {
        $(this).children('.submenu').show();
        $(this).css('backgroundImage', 'url(/images/NavBgSelect2.png)');
        if ($(this).is('.active')) {
            $(this).find('b').css('color', '#FFF');
        }
    }).on('mouseleave', '.linkNavAll', function() {
        $(this).children('.submenu').hide();
        if ($(this).is('.active')) {
            $(this).css('backgroundImage', 'url(/images/NavBgSelect.png)');
            $(this).find('b').css('color', '#91DB47');
        } else {
            $(this).css('backgroundImage', 'none');
        }
    }).on('mouseenter', '.product_hover', function() {
        $('.product_list').show();
        $('.submenu_product_list').show();
    }).on('mouseleave', '.product_hover', function() {
        $('.product_list').hide();
        $('.submenu_product_list').hide();
    }).on('mouseenter', '#InkContent ul li', function() {
        $(this).find('#designImg').css('backgroundColor', '#91DB47');
    }).on('mouseleave', '#InkContent ul li', function() {
        $(this).find('#designImg').css('backgroundColor', '#FFF');
    }).on('mouseenter', '#Home', function() {
        if ($(this).parent().is('.active')) {
            $(this).children().attr('src', '/images/Home.png');
        }
    }).on('mouseleave', '#Home', function() {
        if ($(this).parent().is('.active')) {
            $(this).children().attr('src', '/images/newHome.png');
        }
    }).on('click', '.InspirationArtikle', function() {
        var elem = $(this).next('.inspcontent');
        $('.InspirationArtikle').css('backgroundColor', '#3C3C3C').css('borderRadius', '8px');
        $('.InspirationArtikle').find('.Insp').children().attr('src', '/images/insphide.png');
        $('.inspcontent').slideUp();
        if (!elem.is(':visible')) {
            elem.slideDown();
            $(this).css('backgroundColor', '#2D2D2D').css('borderRadius', '8px 8px 0 0');
            $(this).find('.Insp').children().attr('src', '/images/show.png');
        }
    }).on('click', '.menuLeftArrow', function() {
        var elements = $(this).parents('.inspcontent').find('.separatorImg');
        $(elements.find('#imgRel').last()).prependTo(elements.find('.moreItem'));
    }).on('click', '.menuRightArrow', function() {
        var elements = $(this).parents('.inspcontent').find('.separatorImg');
        $(elements.find('#imgRel').first()).appendTo(elements.find('.moreItem'));
    });


    /* INSPIRATION MENU MAIN PRELOAD */
    function inspMenuLoad() {
        var by = $('.inspMenuRadioButtons input[type=radio]:checked').val();
        if (by == 1) {
            by = 'types';
        } else {
            by = 'cats';
        }
        $.post('/ajax', {'fn': 'inspMenuShowBy', 'by': by}, function(data) {
            if (data) {
                $('#inspMenuLoad').html(data);
            }
        });
    }
    inspMenuLoad();
    $('body').on('change', '.inspMenuRadioButtons .inspMenuButton', inspMenuLoad)

    /*--- Modal ---*/
    $('.close_modal').click(function() {
        $('.modal_win').fadeOut();
    });

//    Box Shipping Address

    $('body').on('click', '.new_address', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.new_ship_to_adres_box').fadeIn(500);
        });
    }).on('click', '.close_block_new_ship', function() {
        $('.new_ship_to_adres_box').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.cancel_buttom', function() {
        $('.new_ship_to_adres_box').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.new_ship_to_adres_box').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('change', '.add_my_acaunt', function() {

        if ($(".view_as").is(":hidden")) {
            $(".view_as").slideDown("fast");
        } else {
            $(".view_as").slideUp("fast");
        }
    });

//    CALENDAR BOX
    $('body').on('click', '.calendar', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.calendar_block').fadeIn(500);
        });
    }).on('click', '.close_calendar', function() {
        $('.calendar_block').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.calendar_block').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    });


    $('body').on('click', '.one_question', function() {
        if ($(this).find(".description_question").is(":hidden")) {
            $(this).find('.title_bottom').removeClass('title_question');
            $(this).find('.title_bottom').addClass('title_question_active');
            $(".description_question").slideDown("fast");
        } else {
            $(this).find('.title_bottom').addClass('title_question');
             $(this).find('.title_bottom').removeClass('.title_question_active');
            $(this).find(".description_question").slideUp("fast");
        }
    });



//    PRODUCKT DETAIL BOX
    $('body').on('click', '.bold_title_pocket_folders_payment', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.block_box_produckt_detail').fadeIn(500);
        });
    }).on('click', '.close_block_new_ship', function() {
        $('.block_box_produckt_detail').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.block_box_produckt_detail').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });

    });

//    PRODUCKT DETAIL BOX
    $('body').on('click', '.to_city_payment', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.block_box_sypping_detail').fadeIn(500);
        });
    }).on('click', '.close_block_new_ship', function() {
        $('.block_box_sypping_detail').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.block_box_sypping_detail').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });

    });


    //    SYPPING INFO EDIT BOX
    $('body').on('click', '.submit_my_order', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.block_box_payment_info_edit').fadeIn(500);
        });
    }).on('click', '.close_block_new_ship', function() {
        $('.block_box_payment_info_edit').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.block_box_payment_info_edit').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });

    });
//EDIT MY ACCOUNT ORDERS
    $('body').on('click', '.box_details_order_my_acount', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.edit_order_account_box').fadeIn(500);
        });
    }).on('click', '.close_block_new_ship', function() {
        $('.edit_order_account_box').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.edit_order_account_box').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });

    });

    //EDIT MY ship to ardress
    $('body').on('click', '.edit_my_ship_adress_submit', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.edit_my_ship_adress').fadeIn(500);
        });
    }).on('click', '.close_block_new_ship', function() {
        $('.edit_my_ship_adress').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.edit_my_ship_adress').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });

    });


//    PRITN IT HOME PRODUCKT SEE
    $('body').on('click', '.plus_product', function() {
        var $this = $(this);
        if ($('.view_prodyct_detail_action').is(':visible')) {
            $('.view_prodyct_detail_action').hide();
            $this.find('img').attr('src', '/images/plus.png');
            $('.title_from_js').removeClass('title_prod_remove_view');
            $this.css('borderBottomLeftRadius', '8px');
            $('.title_prod_remove').removeClass('clicked_prod');
        } else {
            $('.view_prodyct_detail_action').show();
            $this.find('img').attr('src', '/images/minus.png');
            $this.css('borderBottomLeftRadius', '0');
            $('.title_prod_remove').addClass('clicked_prod');
        }
    })


    $('body').on('mouseenter', '.action_produckt', function(e) {
        var style = $(this).find('.block_style');
        var coating = $(this).find('.block_coating');
        var costumer = $(this).find('.block_custom');
        style.show();
        coating.show();
        costumer.show();
        $(this).css('backgroundColor', '#4e9000');
        $(this).find('img.arrow').attr('src', '/images/arrow_left_active_action.png');
        e.stopPropagation();
    }).on('mouseleave', '.action_produckt', function(e) {
        var coating = $(this).find('.block_coating');
        var style = $(this).find('.block_style');
        var costumer = $(this).find('.block_custom');
        style.hide();
        coating.hide();
        costumer.hide();
        $(this).css('backgroundColor', '#666666');
        $(this).find('img.arrow').attr('src', '/images/customArrov.png');
        e.stopPropagation();
    });

    $('body').on('mouseenter', '.active_hover', function(e) {
        var coating = $(this).find('.block_style');
        coating.show();
        $(this).css('backgroundColor', '#4e9000');
        $(this).find('img.arrow').attr('src', '/images/arrow_left_active_action.png');
        e.stopPropagation();
    }).on('mouseleave', '.active_hover', function(e) {
        var coating = $(this).find('.block_style');
        coating.hide();
        $(this).css('backgroundColor', '#666666');
        $(this).find('img.arrow').attr('src', '/images/customArrov.png');
        e.stopPropagation();
    });




//    SAMPLE PACK

//    Box Shipping Address

    $('body').on('click', '.vew_sample_pack', function() {
        $('.body_opacity_35').fadeIn(300, function() {
            $('.box_sample_pack').fadeIn(500);
        });
    }).on('click', '.close_block_new_ship', function() {
        $('.box_sample_pack').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.cancel_buttom_sample_pack', function() {
        $('.box_sample_pack').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    }).on('click', '.body_opacity_35', function() {
        $('.box_sample_pack').fadeOut(500, function() {
            $('.body_opacity_35').fadeOut(300);
        });
    });


//  bling_checkbox
    $('body').on('change', '.bling_checkbox', function() {
        if ($(this).is(':checked')) {
            $(this).next().css('font-weight', 'bold');
        } else {
            $(this).next().css('font-weight', 'normal');
        }
    });

});