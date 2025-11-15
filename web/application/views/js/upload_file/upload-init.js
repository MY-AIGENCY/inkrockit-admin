$(function () {
    $('#fileupload').fileupload({
        dataType: 'json',
        sequentialUploads : true,
        add: function (e, data) {
            data.url = '/admin/inspiration/upload';
            data.submit();
            $('#input_uploads').show()
        },
        done: function (e, data) {
            $.each(data.result, function (index, file) {
                if(file.err){
                    $('#input_uploads').append('<p class="error">'+file.name+' <span class="right">Error: '+file.err+'</span></p>');
                }else{
                    $('#input_uploads').append('<p>'+file.name+' <span class="right">Uploaded</span></p>');
                    $('.button_contin').show()
                }
                
            });
        },
        start: function (e) {
            $('#progressbar').css('opacity', '1')
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var proc = 100 - progress;
            $('#progressbar span').css('right', proc+'%')
            if(progress == 100){
                $('#progressbar').css('opacity', '0')
            }
        }
    })
    
    
    $('#fileupload_add').fileupload({
        dataType: 'json',
        sequentialUploads : true,
        add: function (e, data) {
            data.url = '/admin/inspiration/upload';
            data.value = 'item_id';
            data.submit();
        },
        done: function (e, data) {
            $.each(data.result, function (index, file) {
                if(file.err){
                    $('#input_uploads').show()
                    $('#input_uploads').append('<p class="error">'+file.name+' <span class="right">Error: '+file.err+'</span></p>');
                }else{
                    if($('.items_img a[data-img="'+file.id+'"]').length > 0){
                        $('.items_img a[data-img="'+file.id+'"]').parent().remove()
                    }
                    $('<div><a class="delete ui-icon ui-icon-closethick" data-img="'+file.id+'"></a><img src="/files/items/thumbnails/'+file.name+'"></div>').insertAfter('.items_img div:last')
                }
                
            });
        },
        stop: function(){
            $('#progressbar').hide()
        },
        start: function (e) {
            $('#progressbar').css('opacity', '1').show()
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var proc = 100 - progress;
            $('#progressbar span').css('right', proc+'%')
            if(progress == 100){
                $('#progressbar').css('opacity', '0')
            }
        }
    })
    
})