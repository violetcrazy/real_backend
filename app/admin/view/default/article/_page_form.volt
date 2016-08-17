<div class="form-group">
    <label class="col-sm-2 control-label">
        Ảnh đại diện
    </label>
    <div class="col-sm-5">
        {% set value = '' %}
        {% set src = '' %}

        {% if article.image_default is defined and article.image_default != '' %}
            {% set value = article.image_default %}
            {% set src = config.cdn.file_url ~  article.image_default %}
        {% endif %}

        {{ templateUpload('thumbnail', src, value) }}
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
        Trạng thái
    </label>
    <div class="col-sm-5">
        {{ form.render('status', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
    </div>
</div>

{% block bottom_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/tinymce/tinymce.min.js?' ~ config.asset.version }}"></script>

    <script>

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
        }).on('click', '.open-upload-editor', function (event) {
            event.preventDefault();
            var elGetData = $(this).attr('data-sendValue');

            var url = url_uload_media + '?callback=getFileEditor&input-receive=' + elGetData;
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
            var html = buildImageEditor(option.data);
            tinyMCE.activeEditor.insertContent(html);
            $.fancybox.close();
        }
        function buildImageEditor(data) {
            var html = '';
            for (i in data) {
                if (typeof data[i].link != 'undefined') {
                    html += '<p><img src="' + data[i].link + '" alt=""></p>';
                }
            }
            return html;
        }
    </script>
{% endblock %}