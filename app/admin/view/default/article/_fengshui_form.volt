<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên (tháng)
    </label>
    <div class="col-sm-8">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên âm lịch
    </label>
    <div class="col-sm-8">
        {{ form.render('intro', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'intro'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Chọn năm
    </label>
    <div class="col-sm-8">
        {% if categoryList is defined and  categoryList|length %}
            <select name="category_id" id="" class="form-control">
                {% for itemCate in categoryList %}
                    {% set selected = '' %}
                    {% if article.category_id is defined and  itemCate['id'] == article.category_id %}
                        {% set selected = 'selected' %}
                    {% endif %}
                    <option {{ selected }} value="{{ itemCate['id'] }}">{{ itemCate['name'] }} ({{ itemCate['middle_name'] }})</option>
                {% endfor %}
            </select>
        {% endif %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Ảnh đại diện
    </label>
    <div class="col-sm-8">
        {% set value = '' %}
        {% set src = '' %}

        {% if article.image_default is defined and article.image_default != '' %}
            {% set value = article.image_default %}
            {% set src = config.cdn.dir_upload ~ 'thumbnail/' ~ article.image_default %}
        {% endif %}

        {{ templateUpload('image_default', src, value) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Gallery hình ảnh
    </label>
    <div class="col-sm-8">
        <button class="btn btn-primary btn-upload-multiple" data-sendValue="gallery" >Upload</button>
        <div class="clearfix"></div>
        <div class="box-show-gallery preview_gallery preiview_upload_multiple">
            {% if article.gallery is defined and article.gallery|length %}
                {% for item in article.gallery %}
                    <div class="thumbnail thumbnail-upload-tools inlineB item">
                        <div class="clear-image-multiple  clip-close" data-clear="gallery"></div>
                        <img height="100px;" src="{{ config.cdn.dir_upload }}thumbnail/{{ item.image  }}" alt="" id="view_gallery">
                        <input name="gallery[]" value="{{ item.image }} " type="hidden" />
                    </div>
                {% endfor %}
            {% endif %}
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Mô tả
    </label>
    <div class="col-sm-10">
        <button type="button" class="open-upload-editor btn btn-primary btn-sm" data-sendValue="description">Chèn hình ảnh vào bài viết</button>
        {{ form.render('description', {'class': 'form-control editor'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Mô tả tiếng anh
    </label>
    <div class="col-sm-10">
        <button type="button" class="open-upload-editor btn btn-primary btn-sm" data-sendValue="description_eng">Chèn hình ảnh vào bài viết</button>
        {{ form.render('description_eng', {'class': 'form-control editor'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Giới tính
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

{% block bottom_js %}

    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/tinymce/tinymce.min.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            tinyMCE.init({
                selector: "textarea.editor",
                theme : "modern",
                skin : "lightgray",
                plugins: [
                    "advlist autolink lists link charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor colorpicker textpattern"
                ],
                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link print preview media | forecolor backcolor | emoticons",
                image_advtab: true,
                fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
                height : 450

            });
            $('.search-select').select2({
                allowClear: true
            });

            $('#name').blur(function(){
                var vl = $(this).val();
                if (vl > 0 && vl < 13) {
                    var name = ['Tý', 'Sửu', 'Dần', 'Mẹo', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tức', 'Hợi'];
                    var _name = name[vl - 1];
                    $('#intro').val(_name);
                }
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
        });

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
    </script>
{% endblock %}