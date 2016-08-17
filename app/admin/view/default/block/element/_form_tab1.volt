<div class="form-group">
    <label class="col-sm-3 control-label">
        Dự án
    </label>
    <div class="col-sm-3">
        {{ form.render('project_id', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'project_id'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Shortname
    </label>
    <div class="col-sm-3">
        {{ form.render('shortname', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'shortname'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Số tầng
    </label>
    <div class="col-sm-3">
        <div class="input-group">
            <span class="input-group-btn">
                {{ form.render('floor_count', {'class': 'form-control', 'data-name': 'floor', 'data-bind': 'floor_name_list'}) }}
                <button type="button" data-name="floor" data-bind="floor_name_list" class="btn btn-primary popup-edit-name">
                    Sửa tên tầng
                </button>
                <input type="hidden" name="floor_name_list" id="floor_name_list" value="{{ result is defined and result.floor_name_list is defined ? result.floor_name_list|e  : '' }}">
            </span>
        </div>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'floor_count'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Số sản phẩm mỗi tầng
    </label>
    <div class="col-sm-3">
        <div class="input-group">
            <span class="input-group-btn">
                {{ form.render('apartment_count', {'class': 'form-control format-number2', 'data-name': 'index', 'data-bind': 'apartment_name_list'}) }}
                <button type="button" data-name="index" data-bind="apartment_name_list" class="btn btn-primary popup-edit-name">
                    Sửa tên vị trí
                </button>
                <input type="hidden" name="apartment_name_list"  id="apartment_name_list" value="{{ result is defined and result.apartment_name_list is defined ? result.apartment_name_list|e  : '' }}" />
            </span>
        </div>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'apartment_count'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Trạng thái
    </label>
    <div class="col-sm-3">
        {{ form.render('status', {'class': 'form-control'}) }}
    </div>
</div>
<hr>

<h3>Tiếng Việt</h3>
<hr />

<div class="form-group">
    <label class="col-sm-3 control-label">
        Tên Block/Khu <span class="symbol required"></span>
    </label>
    <div class="col-sm-8">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Nội dung
    </label>
    <div class="col-sm-8">
        {{ form.render('description', {'class': 'form-control', 'style': 'height: 300px'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description'} %}
    </div>
</div>

<h3>Tiếng Anh</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Tên Block/Khu <span class="symbol required"></span>
    </label>
    <div class="col-sm-8">
        {{ form.render('name_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Nội dung
    </label>
    <div class="col-sm-8">
        {{ form.render('description_eng', {'class': 'form-control', 'style': 'height: 300px'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description_eng'} %}
    </div>
</div>