$(function() {

    var interval_home = setInterval(home_rotator, 8000);
    var interval_design = setInterval(design_rotator, 8000);

    function home_rotator() {
        if ($('.plus.clicked').length > 0)
            return false;
        if ($('.rotatorbutton.checked').next().is('.rotatorbutton')) {
            $('.rotatorbutton.checked').next().trigger('click');
        } else {
            $('.rotatorbutton').first().trigger('click');
        }
    }
    
     function design_rotator() {
        if ($('.plus.clicked').length > 0)
            return false;
        if ($('.rotatordesign.checked').next().is('.rotatordesign')) {
            $('.rotatordesign.checked').next().trigger('click');
        } else {
            $('.rotatordesign').first().trigger('click');
        }
    }
    
    
    function mail_rotator() {
        if ($('.plus.clicked').length > 0)
            return false;
        if ($('.rotatordesign.checked').next().is('.rotatordesign')) {
            $('.rotatordesign.checked').next().trigger('click');
        } else {
            $('.rotatordesign').first().trigger('click');
        }
    }
    

    $('body').on('click', '.rotatorbutton', function(e) {
        var $this = $(this);
        var id = $this.attr('name');
        if (e.hasOwnProperty('originalEvent')) {
            clearInterval(interval_home);
            interval_home = setInterval(home_rotator, 8000);
        }
        if ($('.mainLeft > div:visible').is(':animated')) {
            $('.mainLeft > div:animated').stop();
        }
        $('.rotatorbutton').removeClass('checked');
        $('.mainLeft > div[id!=' + id + ']').animate({
            'opacity': 0
        }, 1500, function() {
            $(this).hide();
        }).css('zIndex', '0');
        $('#' + id).animate({
            'opacity': 1
        }, 1500).show().css('zIndex', '2');
        $this.addClass('checked');
        e.stopPropagation();
        
    }).on('click', '.rotatordesign', function(e) {
        var $this = $(this);
        var id = $this.attr('name');
        if (e.hasOwnProperty('originalEvent')) {
            clearInterval(interval_design);
            interval_design = setInterval(design_rotator, 8000);
        }
        if ($('.right_block_design > div:visible').is(':animated')) {
            $('.right_block_design > div:animated').stop();
        }
        $('.rotatordesign').removeClass('checked');
        $('.right_block_design > div[id!=' + id + ']').animate({
            'opacity': 0
        }, 1500, function() {
            $(this).hide();
        }).css('zIndex', '0');
        $('#' + id).animate({
            'opacity': 1
        }, 1500).show().css('zIndex', '2');
        $this.addClass('checked');
        e.stopPropagation();
    }).on('click', '.rotatordesign', function(e) {
        var $this = $(this);
        var id = $this.attr('name');
        if (e.hasOwnProperty('originalEvent')) {
            clearInterval(interval_design);
            interval_design = setInterval(design_rotator, 8000);
        }
        if ($('.right_block_mail > div:visible').is(':animated')) {
            $('.right_block_mail > div:animated').stop();
        }
        $('.rotatordesign').removeClass('checked');
        $('.right_block_mail > div[id!=' + id + ']').animate({
            'opacity': 0
        }, 1500, function() {
            $(this).hide();
        }).css('zIndex', '0');
        $('#' + id).animate({
            'opacity': 1
        }, 1500).show().css('zIndex', '2');
        $this.addClass('checked');
        e.stopPropagation();
    }).on('click', '.plus', function() {
        var $this = $(this);
        if ($('.customPrintSelect').is(':visible')) {
            $('.customPrintSelect').hide();
            $this.find('img').attr('src', '/images/ButtonPlus.png');
            $this.css('borderBottomLeftRadius', '8px');
            $this.removeClass('clicked');
        } else {
            $('.customPrintSelect').show();
            $this.find('img').attr('src', '/images/ButtonMinus.png');
            $this.css('borderBottomLeftRadius', '0');
            $this.addClass('clicked');
        }
    }).on('mouseleave', '.custPrintPancer', function() {
        if ($('.customPrintSelect').is(':visible')) {
            $('.plus').trigger('click');
        }
    });

});