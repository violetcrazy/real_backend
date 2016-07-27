
$(document).ready(function(){

    $("#image-view").dropzone({
        url: upload_url,
        previewsContainer: '',
        clickable: ['#image-view','#image-view .btn-upload'],
        addedfile: function(file){
            $('.select-folder-upload').addClass('loadding-file');

        },
        queuecomplete: function(){
            $('.select-folder-upload').addClass('done');
            setTimeout(function(){
                $('.select-folder-upload').removeClass('loadding-file done');
            },1000);

        },
        success: function(file){
            file = JSON.parse(file.xhr.response);
            if (file.status == 200) {
                var html = BuildItem(file.result);
                $('#image-view .preview-area').prepend(html);
            } else {
                var html = '<div class="alert alert-danger">'+ file.message +'</div>';
                $('.block-preview .entry-preview').html(html);
            }
        }
    });
});

$(document)

    .on('click', '#view-list', function (event){
        event.preventDefault();
        $this = $(this);
        var page =1;
        var query  = 'page=' + page;
        query += '&' + $('#form-filter-media').serialize();
        $.ajax({
            url: media_list,
            data: query,
            dataType: 'json',
            success: function (data) {
                if (data.status == 200) {
                    $('.media-list').find('.wrap-list').find('.entry-content').html('');
                    $(data.result).each(function(){
                        var html = BuildItem(this);
                        $('.media-list').find('.wrap-list').find('.entry-content').append(html);
                    });
                    $('.btn-loadmore-media').data('page',page).slideDown();
                    $('.entry-preview').html('');
                }
            }
        });
    })
    .on('submit', '.form-edit-media', function (event){
        event.preventDefault();
        $this = $(this);
        $('.header-area').addClass('loadding-file');
        var data = $this.serialize();
        $.ajax({
            url: add_media,
            method: 'post',
            data: data,
            dataType: 'json',
            success: function(data){
                $('.header-area').removeClass('loadding-file');
                if (data.status == 200) {
                    var html = BuildPreview(data.result);
                    $this
                        .closest('.block-preview ')
                        .find('.entry-preview')
                        .html(html)
                        .find('select').val(data.result.category_id)
                    ;
                }
            }
        });
    })
    .on('submit', '.form-add-cate', function (event){
        event.preventDefault();
        $this = $(this);
        var data = $this.serialize();
        $.ajax({
            url: add_folder,
            method: 'post',
            data: data,
            dataType: 'json',
            success: function(data){
                if (data.status == 200) {
                    var html = '<li><a>'+ data.result.name +'</a><a data-id="'+ data.result.category_id +'" href="" class="delete-category">x</a></li>';
                    $('.list-folder').prepend(html);
                    $this.trigger('reset');
                    reloadCategory($);
                }
            }
        });
    })
    .on('click', '.btn-loadmore-media', function (event){
        event.preventDefault();
        $this = $(this);
        var pageCurrent = $this.data('page') + 1;
        var query  = 'page=' + pageCurrent;

        query += '&' + $('#form-filter-media').serialize();

        console.log(query);
        $.ajax({
            url: media_list,
            data: query,
            dataType: 'json',
            success: function (data) {
                if (data.status == 200) {
                    $this.data('page', pageCurrent);
                    $(data.result).each(function(){
                        var html = BuildItem(this);
                        $('.media-list').find('.wrap-list').find('.entry-content').append(html);
                    });
                }
                if (pageCurrent == data.total_pages) {
                    $this.slideUp();
                }
            }
        });
    })
    .on('submit', '#form-filter-media', function (event){
        event.preventDefault();
        $this = $(this);
        var url = $this.attr('action');
        var data = $this.serialize();
        $('.media-list').find('.wrap-list').find('.entry-content').html('');

        $.ajax({
            url: url,
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.status == 200) {
                    $(res.result).each(function(){
                        var html = BuildItem(this);
                        $('.media-list').find('.wrap-list').find('.entry-content').append(html);
                    });
                    $('.btn-loadmore-media').data('page', 1).slideDown('fast');
                }
            }
        });
    })
    .on('change', '#category_id_upload', function (event){
        $this = $(this);
        var category_id = $this.val();
        $.post(change_folder_upload, {'category_id':category_id});
    })
    .on('click', '.delete-category', function (event){
        $this = $(this);
        event.preventDefault();
        var category_id = $(this).data('id');
        $.post(delete_folder, {'category_id':category_id}, function(data){
            if (data.status == 200) {
                $this.closest('li').slideUp('fast', function(){
                    $(this).remove();
                });
                reloadCategory($);
            } else {
                var html = '<div class="alert alert-danger">'+ data.message +'</div>';
                $('.list-folder #mess-ajax').html(html);
            }
        });
    })
;
function BuildItem(data){
    var out= '';
    out += '<div class="form-group dz-preview dz-file-preview" data-id="'+data.id+'">\
                   <div class="item dz-details">\
                       <div class="thumbnail-img"><span class="dz-upload" data-dz-uploadprogress></span>\
                           <img  data-dz-thumbnail src="'+ data.thumbnail +'" alt="">\
                           <span data-dz-remove class="delete-img dz-remove data-dz-remove" style="display:block"></span>\
                       </div>\
                       <div class="clearfix"></div>\
                   </div>\
               </div>';
    return out;
}

function reloadCategory($) {
    $.ajax({
        url: folder_list,
        dataType: 'json',
        success: function(data) {
            if (data.status == 200) {
                var result = data.result;
                listCategory = '<option value="-1">Tất cả</option>';
                $.each(data.result, function(index, value){
                    listCategory += '<option value="' + value.category_id + '">' + value.name + '</option>';
                });

                $('.select_category_id').each(function(){
                    var oldVal = $(this).val();
                    $(this).html(listCategory).val(oldVal);
                })
            }
        }
     });
}