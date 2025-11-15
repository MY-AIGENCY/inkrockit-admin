$('.main').corner('tl tr cc:#075887').corner('bl br cc:#91c8e7');
//$('#contentNav').corner('8px cc:#e2e2e2');
$('.mainLeft').corner('tl tr cc:#e2e2e2');
$('.TopHeader').corner('bl br cc:#095684');
$('.mediakits').corner('tr tl cc:#e2e2e2');
$('.headbutton').corner('cc:#f5f5f5 6px');
$('.rotatorbuttonMedia').corner('cc:#ffffff 7px');
$('.rotatorbutton').corner('cc:#ffffff 7px');


$(function(){
    $('.rotatorbutton').click(function(e){
        $('.rotatorbutton').each(function(){
            $(this).corner('cc:#ffffff 7px')
        })
        $(this).corner('cc:#ffffff 7px')
    })
})