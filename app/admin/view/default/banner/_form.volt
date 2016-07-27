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
        Banner
    </label>
    <div class="col-sm-4 block-upload-img">
        {% if banner.image is defined and banner.image != '' %}
            <img src="{{ banner.image_url }}" alt="Icon" />
        {% endif %}
        <div class="m-t-10"></div>
        <input type="file" id="img-file" name="image">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Liên kết
    </label>
    <div class="col-sm-5">
        {{ form.render('url', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'url'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Mô tả
    </label>
    <div class="col-sm-5">
        {{ form.render('description', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Nhóm
    </label>
    <div class="col-sm-5">
        {{ form.render('group_id', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'group_id'} %}
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

<input type="hidden" id="type_upload" name="type_upload" value="{{ constant('\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BANNER') }}" />
<input type="hidden" id="load_upload_image_ajax" value="{{ url({"for": "load_upload_image_ajax"}) }}" />
<input type="hidden" id="load_delete_image_ajax_url" value="{{ url({"for": "load_delete_image_ajax"}) }}" />
<input type="hidden" id="asset_choose_image_url" value="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" />
{% block bottom_js %}
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/banner/upload_image.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true
            });
        });
    </script>
{% endblock %}