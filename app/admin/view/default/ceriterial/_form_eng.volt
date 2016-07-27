<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-5">
        {{ form.render('name_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Kiểu
    </label>
    <div class="col-sm-5">
        {{ form.render('property_type_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'property_type_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'property_type'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hướng nhìn
    </label>
    <div class="col-sm-5">
        {{ form.render('property_view_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'property_view_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'property_view'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiện ích
    </label>
    <div class="col-sm-5">
        {{ form.render('property_utility_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'property_utility_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'property_utility_eng'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hệ thống điều khiển tiết kiệm điện
    </label>
    <div class="col-sm-5">
        {{ form.render('energy_control_system_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'energy_control_system_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'energy_control_system'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hệ thống giải trí âm nhạc
    </label>
    <div class="col-sm-5">
        {{ form.render('entertaining_control_system_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'entertaining_control_system_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'entertaining_control_system'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hệ thống kiểm soát môi trường
    </label>
    <div class="col-sm-5">
        {{ form.render('environment_control_system_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'environment_control_system_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'environment_control_system'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hệ thống an ninh
    </label>
    <div class="col-sm-5">
        {{ form.render('security_control_system_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'security_control_system_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'security_control_system'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hướng
    </label>
    <div class="col-sm-5">
        {{ form.render('trend_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'trend_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'trend'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Dự án
    </label>
    <div class="col-sm-5">
        {{ form.render('project_ids_eng', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'project_ids_eng[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'project_ids_eng'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Số phòng ngủ
    </label>
    <div class="col-sm-3">
        {{ form.render('bedroom_count_eng', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'bedroom_count_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Số phòng tắm
    </label>
    <div class="col-sm-3">
        {{ form.render('bathroom_count_eng', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'bathroom_count_eng'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Diện tích sản phẩm
    </label>
    <div class="col-sm-3">
        {{ form.render('area_eng', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'area_eng'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Giá thấp nhất
    </label>
    <div class="col-sm-3">
        {{ form.render('price_min_eng', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_min_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Giá cao nhất
    </label>
    <div class="col-sm-3">
        {{ form.render('price_max_eng', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_max_eng'} %}
    </div>
</div>