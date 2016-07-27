<div class="form-group">
    <label class="col-sm-2 control-label">
        Ảnh đại diện
    </label>
    <div class="col-sm-5">
        <div>
            {% if article.image_default is defined and article.image_default is not empty %}
                <img width="150px" src="{{ article.image_default}}" id="view_thumbnail">
                 <input type="hidden" name="thumbnail" id="thumbnail" value="{{ article.image_default}}"/>
            {% else %}
                <img width="150px" src="{{ config.asset.backend_url}}images/choose.png" id="view_thumbnail">
                 <input type="hidden" name="thumbnail" id="thumbnail"/>
            {% endif %}
        </div>
        <br>
        <button type="button" class="open-upload btn btn-primary btn-sm" data-sendValue="thumbnail">Chọn ảnh đại diện</button>
    </div>
</div>
<h3>Tiếng Việt</h3>
<hr />
<div class="form-group">
    <label class="col-sm-2 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-5">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Giới thiệu
    </label>
    <div class="col-sm-8">
        {{ form.render('intro', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'intro'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Nội dung
    </label>
    <div class="col-sm-8">
        <button type="button" class="open-upload-editor btn btn-primary btn-sm" data-sendValue="description">Chèn hình ảnh</button>
        {{ form.render('description', {'class': 'form-control editor'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description'} %}
    </div>
</div>
<h3>Tiếng Anh</h3>
<hr />
<div class="form-group">
    <label class="col-sm-2 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-5">
        {{ form.render('name_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Giới thiệu
    </label>
    <div class="col-sm-8">
        {{ form.render('intro_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'intro_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Nội dung
    </label>
    <div class="col-sm-8">
        <button type="button" class="open-upload-editor btn btn-primary btn-sm" data-sendValue="description">Chèn hình ảnh</button>
        {{ form.render('description_eng', {'class': 'form-control editor'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
    </label>
    <div class="col-sm-8">
        <div class="clearfix"></div>
        <div class="article-box-show-gallery">
            {% if article.gallery is defined and article.gallery|length %}
                {% for item in article.gallery %}
                    <div class="block-upload-img">
                        <div class="col-sm-3 col-0">
                            <div class="thumbnail-img" style="width: 120px; height: 120px">
                                <img src="{{ config.asset.frontend_url ~ 'upload/article/thumbnail/' ~ item.image ~ config.asset.version }}" alt="Icon" />
                                <input type="file" class="hidden" accept="image/*" value="" />
                                <input type="hidden" class="input_image" name="image[]" value="{{ item.image }}" />
                                <span class="fa delete-img"></span>
                            </div>
                        </div>
                        <div class="col-sm-9 col-0">
                            <textarea class="gallery_description form-control float-left" name="image_description[]">{{ item.description }}</textarea>
                        </div>
                        <div class="btn btn btn-primary btn-xs cancel-gallery m-t-10 float-right">Hủy</div>
                    </div>
                    <div class="clearfix m-b-15"></div>
                {% endfor %}
            {% endif %}
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <div class="btn btn btn-primary add-gallery float-right">Thêm</div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Loại
    </label>
    <div class="col-sm-5">
        {{ form.render('type', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'type'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Trạng thái
    </label>
    <div class="col-sm-5">
        {{ form.render('status', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
    </div>
</div>

<input type="hidden" id="type_upload" name="type_upload" value="{{ constant('\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_ARTICLE_DEFAULT') }}" />
<input type="hidden" id="load_upload_image_ajax" value="{{ url({"for": "load_upload_image_ajax"}) }}" />
<input type="hidden" id="load_delete_image_ajax_url" value="{{ url({"for": "load_delete_image_ajax"}) }}" />
<input type="hidden" id="asset_choose_image_url" value="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" />
{% block bottom_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/tinymce/tinymce.min.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true
            });
            
            $(document).on('click', '.add-gallery', function() {
                $('.article-box-show-gallery').append(
                    '<div class="block-upload-img">'
                    +    '<div class="col-sm-3 col-0">'
                    +    '        <div class="thumbnail-img" style="width: 120px; height: 120px">'
                    +    '            <img src={{ config.asset.backend_url ~ "img/choose.png?" ~ config.asset.version }}" alt="Icon" />'
                    +    '            <input type="file" class="hidden" accept="image/*" value="" />'
                    +    '            <input type="hidden" class="input_image" name="image[1]" value="" />'
                    +    '            <span class="fa delete-img"></span>'
                    +    '        </div>'
                    +    '</div>'
                    +    '<button type="button" class="open-upload-galery btn btn-primary btn-sm" data-sendValue="image[1]">Chọn ảnh đại diện</button>'
                    +    '    <div class="col-sm-9 col-0">'
                    +    '        <textarea class="gallery_description form-control float-left" name="image_description[]"></textarea>'
                    +    '    </div>'
                    +    '</div>'
                    +    '<div class="clearfix m-b-15"></div>'
                );
            });
        });
    </script>
    <script>

        function getFile(option) {
            $('#' + option.element).val(option.result.url);
            $('#view_' + option.element).attr('src', option.result.url);
            $.fancybox.close();
        }

        function getFileEditor(option) {
            console.log(option);
            var html = buildImageEditor(option.result);
            tinyMCE.activeEditor.insertContent(html);
            $.fancybox.close();
        }

        function buildImageEditor(data) {
            var html = '';
            if (typeof data.attribute.link != 'undefined') {
                html = '<a href="' + data.attribute.link + '" title="' + data.attribute.title + '">\
                            <img src="' + data.url + '" alt="' + data.attribute.title + '">\
                        </a>';
            } else {
                html = '<img src="' + data.url + '" alt="' + data.attribute.title + '">';
            }
            return html;
        }

        $(document).ready(function () {
            tinyMCE.init({
                selector: "textarea.editor",
                theme: "modern",
                skin: "lightgray",
                plugins: [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor colorpicker textpattern"
                ],
                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link print preview media | forecolor backcolor | emoticons",
                image_advtab: true,
                fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
                height: 450

            });
            $('.search-select').select2({
                allowClear: true
            });
        }).on('click', '.open-upload', function (event) {
            event.preventDefault();
            var elGetData = $(this).attr('data-sendValue');

            var url = url_uload_media + '?callback=getFile&sendToElement=' + elGetData;
            $.fancybox({
                'width': '90%',
                'height': '90%',
                'autoScale': true,
                'transitionIn': 'fade',
                'transitionOut': 'fade',
                'type': 'iframe',
                'href': url
            });
        }).on('click', '.open-upload-galery', function (event) {
            event.preventDefault();
            var elGetData = $(this).attr('data-sendValue');

            var url = url_uload_media + '?callback=getFile&sendToElement=' + elGetData;
            $.fancybox({
                'width': '90%',
                'height': '90%',
                'autoScale': true,
                'transitionIn': 'fade',
                'transitionOut': 'fade',
                'type': 'iframe',
                'href': url
            });
        }).on('click', '.open-upload-editor', function (event) {
            event.preventDefault();
            var elGetData = $(this).attr('data-sendValue');

            var url = url_uload_media + '?callback=getFileEditor&sendToElement=' + elGetData;
            $.fancybox({
                'width': '90%',
                'height': '90%',
                'autoScale': true,
                'transitionIn': 'fade',
                'transitionOut': 'fade',
                'type': 'iframe',
                'href': url
            });
        })
                ;
    </script>
{% endblock %}