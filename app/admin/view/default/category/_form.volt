<div class="form-group">
    <label class="col-sm-2 control-label">
        Icon
    </label>
    <div class="col-sm-4 block-upload-img">
        {% if category.icon is defined and category.icon != '' %}
            <div class="thumbnail-img" style="width: 60px; height: 60px">
                <img src="{{ category.image_icon_url }}" alt="Icon" />
                <input type="file" class="hidden" accept="image/*" value="{{ category.icon }}" />
                <input type="hidden" class="input_image" name="icon" value="{{ category.icon }}" />
                <span class="fa delete-img"></span>
            </div>
        {% else %}
            <div class="thumbnail-img" style="width: 60px; height: 60px">
                <img src="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" alt="Icon" />
                <input type="file" class="hidden" accept="image/*" value="" />
                <input type="hidden" class="input_image" name="icon" value="" />
                <span class="fa delete-img"></span>
            </div>
        {% endif %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Hình đại diện
    </label>
    <div class="col-sm-4 block-upload-img">
        {% if category.image is defined and category.image != '' %}
            <div class="thumbnail-img" style="width: 120px; height: 120px">
                <img src="{{ category.image_default_url }}" alt="Icon" />
                <input type="file" class="hidden" accept="image/*" value="{{ category.image }}" />
                <input type="hidden" class="input_image" name="image" value="{{ category.image }}" />
                <span class="fa delete-img"></span>
            </div>
        {% else %}
            <div class="thumbnail-img" style="width: 120px; height: 120px">
                <img src="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" alt="Icon" />
                <input type="file" class="hidden" accept="image/*" value="" />
                <input type="hidden" class="input_image" name="image" value="" />
                <span class="fa delete-img"></span>
            </div>
        {% endif %}
    </div>
</div>
    
<h3>Tiếng Việt</h3>
<hr />
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
        Meta title
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_title', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_title'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta keyword
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_keyword', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_keyword'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta description
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_description', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_description'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Danh mục cha
    </label>
    <div class="col-sm-5">
        {{ form.render('parent_id', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'parent_id'} %}
    </div>
</div>
    
<h3>Tiếng Anh</h3>
<hr />
<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên
    </label>
    <div class="col-sm-5">
        {{ form.render('name_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta title
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_title_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_title_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta keyword
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_keyword_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_keyword_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta description
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_description_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_description_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Danh mục cha
    </label>
    <div class="col-sm-5">
        {{ form.render('parent_id', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'parent_id'} %}
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
<input type="hidden" id="type_upload_default" name="type_upload_default" value="{{ constant('\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_CATEGORY_DEFAULT') }}" />
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