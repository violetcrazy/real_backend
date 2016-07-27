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
        Nội dung
    </label>
    <div class="col-sm-8">
        <button type="button" class="open-upload-editor btn btn-primary btn-sm" data-sendValue="description">Chèn hình ảnh</button>
        {{ form.render('description', {'class': 'editor form-control', 'id': 'editor'}) }}
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
        Nội dung
    </label>
    <div class="col-sm-8">
        <button type="button" class="open-upload-editor btn btn-primary btn-sm" data-sendValue="description">Chèn hình ảnh</button>
        {{ form.render('description_eng', {'class': 'editor form-control', 'id': 'editor'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description_eng'} %}
    </div>
</div>
    
<div class="form-group">
    <label class="col-sm-2 control-label">
        Dự án
    </label>
    <div class="col-sm-5">
        {{ form.render('project_id', {'class': 'form-control search-select'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'project_id'} %}
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
        function getFile(option) 
        {
            $('#' + option.element).val(option.result.url);
            $('#view_' + option.element).attr('src', option.result.url);
            $.fancybox.close();
        }

        function getFileEditor(option) 
        {
            console.log(option);
            var html = buildImageEditor(option.result);
            tinyMCE.activeEditor.insertContent(html);
            $.fancybox.close();
        }

        function buildImageEditor(data) 
        {
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
    </script>
{% endblock %}