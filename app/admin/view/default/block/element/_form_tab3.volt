<div class="form-group">
    <label class="col-sm-3 control-label">
        Hướng
    </label>
    <div class="col-sm-3">
        {{ form.render('direction', {'class': 'form-control'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Tổng diện tích (m<sup>2</sup>)
    </label>
    <div class="col-sm-3">
        {{ form.render('total_area', {'class': 'form-control format-number'}) }}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Diện tích cây xanh (m<sup>2</sup>)
    </label>
    <div class="col-sm-3">
        {{ form.render('green_area', {'class': 'form-control format-number'}) }}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Kiểu
    </label>
    <div class="col-sm-8">
        <input id="attribute_type" class="form-control" type="text" value="{{ result is defined and result.attribute_type is defined ? result.attribute_type : '' }}" name="attribute_type">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hướng nhìn
    </label>
    <div class="col-sm-8">
        <input id="attribute_view" class="form-control" type="text" value="{{ result is defined and result.attribute_view is defined ? result.attribute_view : '' }}" name="attribute_view">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiện ích
    </label>
    <div class="col-sm-8">
        <input id="attribute_utility" name="attribute_utility" class="form-control" type="text" value="{{ result is defined and result.attribute_utility is defined ? result.attribute_utility : '' }}">
    </div>
</div>
    
<h3>Tiếng Việt</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Chính sách
    </label>
    <div class="col-sm-8">
        {{ form.render('policy', {'class': 'form-control', 'style': 'height: 300px'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'policy'} %}
    </div>
</div>

<h3>Tiếng Anh</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Chính sách
    </label>
    <div class="col-sm-8">
        {{ form.render('policy_eng', {'class': 'form-control', 'style': 'height: 300px'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'policy_eng'} %}
    </div>
</div>
