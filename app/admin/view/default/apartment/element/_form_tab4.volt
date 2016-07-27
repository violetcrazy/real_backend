<div class="form-group">
    <label class="col-sm-3 control-label">
        Số phòng ngủ
    </label>
    <div class="col-sm-3">
        {{ form.render('bedroom_count', {'class': 'form-control format-number'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Số phòng tắm
    </label>
    <div class="col-sm-3">
        {{ form.render('bathroom_count', {'class': 'form-control format-number'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Loại
    </label>
    <div class="col-sm-3">
        {{ form.render('type', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'type'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hướng nhìn
    </label>
    <div class="col-sm-3">
        {{ form.render('direction', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'direction'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Người lớn
    </label>
    <div class="col-sm-3">
        {{ form.render('adults_count', {'class': 'form-control format-number'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Trẻ em
    </label>
    <div class="col-sm-3">
        {{ form.render('children_count', {'class': 'form-control format-number'}) }}
    </div>
</div>

<h3>Thuộc tính</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Kiểu sản phẩm
    </label>
    <div class="col-sm-8">
        <input id="property_type" class="form-control" type="text" value="{{ apartmentPropertyType is defined ? apartmentPropertyType : '' }}" name="attribute_type">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Môi trường sống
    </label>
    <div class="col-sm-8">
        <input id="property_view" class="form-control" type="text" value="{{ apartmentPropertyView is defined ? apartmentPropertyView : '' }}" name="attribute_view">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Dịch vụ - Tiện ích
    </label>
    <div class="col-sm-8">
        <input id="property_utility" name="attribute_utility" class="form-control" type="text" value="{{ apartmentPropertyUtility is defined ? apartmentPropertyUtility : '' }}">
    </div>
</div>
