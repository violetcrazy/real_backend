<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
        <script type="text/javascript" src="js/dropzone.js"></script>
        <script type="text/javascript">
        $(document).ready(function(){
            $("#gallery").dropzone({
                url: "upload.php",
                previewsContainer: '',
                clickable: ['#gallery','#gallery .btn-upload'],
                addedfile: function(file){
                },
                success: function(file){
                    file = JSON.parse(file.xhr.response);
                    console.log(file);
                    var html = BuildItem('gallery',file.result.link);
                    $('#gallery .preview-area').prepend(html);
                }
            });

            $("#image-view").dropzone({
                url: "upload.php",
                previewsContainer: '',
                clickable: ['#image-view','#image-view .btn-upload'],
                addedfile: function(file){
                },
                success: function(file){
                    file = JSON.parse(file.xhr.response);
                    console.log(file);
                    var html = BuildItem('image-view',file.result.link);
                    $('#image-view .preview-area').prepend(html);
                }
            });

        });

        $(document).on('click', '.remove-this-img', function(event){
            event.preventDefault();
            $(this).closest('.dz-preview.dz-file-preview').slideUp('fast', function(){
                $(this).remove();
            })
        });

        function BuildItem(name,linkImg){
            var out= '';
            var id = 'id_'+(new Date()).getTime();
            out += '<div class="form-group dz-preview dz-file-preview">\
                       <div class="">\
                           <div class="item dz-details">\
                               <div class="thumbnail-img"><span class="dz-upload" data-dz-uploadprogress></span>\
                                   <img  data-dz-thumbnail src="'+linkImg+'" alt="">\
                                   <input type="hidden" value="'+linkImg+'" name="'+name+'_img['+id+']" id="gallery1">\
                                   <span data-dz-remove class="delete-img dz-remove data-dz-remove" style="display:block"></span>\
                               </div>\
                               <div class="input-textarea">\
                                   <textarea rows="3" name="'+name+'_content['+id+']" placeholder="Description (option)" class="form-control caption-img" id="contentMain"></textarea>\
                                   <p class="text-right"><a href="#" class="remove-this-img text-danger"><i>Xóa hình này</i></a></p>\
                               </div>\
                               <div class="clearfix"></div>\
                           </div>\
                       </div>\
                   </div>';
            return out;
        }
        </script>
    </head>
    <body>
    <?php
        var_dump(@$_REQUEST);
    ?>
        <form action="" method="post">

            <div class="dropzone">
                <div class="header">
                    Tải lên hình ảnh <b>Gallery</b>
                    <button class="btn btn-success pull-right" type="submit">Submit all</button>
                    <div class="clearfix"></div>
                </div>
                <div class="wrap-list" id="gallery">
                    <div class="text-center click-area">
                        <br>
                        Kéo thả các hình ảnh vào đây hoặc <br>
                        <a class="btn btn-default btn-upload">Tải lên hình ảnh</a>
                    </div>
                    <div class="block-img-upload"></div>
                    <div class="preview-area"></div>
                </div>
            </div>

            <div class="dropzone">
                <div class="header">
                    Tải lên hình ảnh <b>Image View</b>
                    <button class="btn btn-success pull-right" type="submit">Submit all</button>
                    <div class="clearfix"></div>
                </div>
                <div class="wrap-list" id="image-view">
                    <div class="text-center click-area">
                        <br>
                        Kéo thả các hình ảnh vào đây hoặc <br>
                        <a class="btn btn-default btn-upload">Tải lên hình ảnh</a>
                    </div>
                    <div class="block-img-upload"></div>
                    <div class="preview-area"></div>
                </div>
            </div>

        </form>
    </body>
</html>