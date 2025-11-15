var browser=navigator.userAgent;
var regV=/firefox/i;
var regV1=/opera/i;
var regV2=/chrome/i;
var regV3=/msie/i;
var regV4=/safari/i;
var msie8 =/msie 8.0/i;
var msie9 =/msie 9.0/i;
var msie10 =/msie 10.0/i;
var msie7 =/msie 7.0/i;
            
if(browser.search(regV)!==-1){
    $('body').append('<link href="/css/styleFireFox.css" type="text/css" rel="stylesheet">');
}
if(browser.search(regV1)!==-1){
    $('body').append('<link href="/css/styleOpera.css" type="text/css" rel="stylesheet">');
}
if(browser.search(regV3)!==-1){
    if(browser.search(msie8)!==-1 || browser.search(msie7)!==-1 || browser.search(msie9)!==-1 || browser.search(msie10)!==-1 ){
        $('body').append('<link href="/css/styleMsie.css" type="text/css" rel="stylesheet"><script src="/js/jquery.corners.js" type="text/javascript"><\/script><script type="text/javascript" src="/js/jquery.corners_ie_fix.js"><\/script>');
    }
}
if(browser.search(regV4)!==-1 && browser.search(regV2)===-1){
    $('body').append('<link href="/css/styleSafari.css" type="text/css" rel="stylesheet">');
}
if(browser.search(regV2)!==-1 && browser.search(regV4)!==-1){
    $('body').append('<link href="/css/styleChrome.css" type="text/css" rel="stylesheet">');
}