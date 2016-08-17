<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên
    </label>
    <div class="col-sm-5">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Kiểu
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

<input type="hidden" id="type_upload" name="type_upload" value="{{ constant('\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_CATEGORY') }}" />
<input type="hidden" id="load_upload_image_ajax" value="{{ url({"for": "load_upload_image_ajax"}) }}" />
<input type="hidden" id="load_delete_image_ajax_url" value="{{ url({"for": "load_delete_image_ajax"}) }}" />
<input type="hidden" id="asset_choose_image_url" value="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" />
{% block bottom_js %}
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/category/upload_image.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true
            });
        });
    </script>
{% endblock %}