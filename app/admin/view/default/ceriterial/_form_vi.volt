<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-5">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>
    
<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiêu đề tiếng anh
    </label>
    <div class="col-sm-5">
        {{ form.render('name_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name_eng'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Kiểu
    </label>
    <div class="col-sm-5">
        {{ form.render('attribute_type', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'attribute_type[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'attribute_type'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hướng nhìn
    </label>
    <div class="col-sm-5">
        {{ form.render('attribute_view', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'attribute_view[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'attribute_view'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiện ích
    </label>
    <div class="col-sm-5">
        {{ form.render('attribute_utility', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'attribute_utility[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'attribute_utility'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hướng
    </label>
    <div class="col-sm-5">
        {{ form.render('direction', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'direction[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'direction'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Dự án
    </label>
    <div class="col-sm-5">
        {{ form.render('project_ids', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'project_ids[]'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'project_ids'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Số phòng ngủ
    </label>
    <div class="col-sm-3">
        {{ form.render('bedroom_count', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'bedroom_count'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Số phòng tắm
    </label>
    <div class="col-sm-3">
        {{ form.render('bathroom_count', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'bathroom_count'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Diện tích sản phẩm
    </label>
    <div class="col-sm-3">
        {{ form.render('total_area', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'total_area'} %}
    </div>
</div>


