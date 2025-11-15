$(function(){
    $('#fileupload').fileupload({
        dataType: 'json',
        done: function (e, data) {
            if($('img[src="/files/upload/'+data.result.name+'"]').length > 0){
                $('img[src="/files/upload/'+data.result.name+'"]').parent().remove();
            }
            $('.files_list').append('<div class="img_block">\n\
            <small class="remove">remove</small> <small class="title">'+data.result.name+'</small>\n\
        </div>');
//            if($.browser.msie) {
//                $('#progress').html('');
//                $('#fileupload').show();
//            }
        },
        start: function(){
//            if($.browser.msie) { 
//                $('.proc').hide();
//                $('#progress').html('Uploading... <img src="/images/admin/info-loader.gif">');
//                $('#fileupload').hide();
//            }
        },
        end: function(){
            $('.proc').text('100% - Files successfully uploaded');
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
                );
            $('.proc').text(progress+'%');
        }
    });
    
    $('body').on('click','.remove', function(){
        var file = $(this).parent().find('.title').text();
        $.post('/upload/ajax/remove_file',{
            'file': file
        });
        $(this).parent().remove();
    });
    
});