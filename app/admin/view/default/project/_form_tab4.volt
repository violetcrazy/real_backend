<div id="form_tab4_error_message" class="alert alert-danger" style="display: none;"></div>

<h3>Tiếng Việt</h3>
<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
        Kiểu
    </label>
    <div class="col-sm-8">
        <input type="text" id="property_type" name="property_type" class="form-control" value="{% if projectPropertyTypeVie is defined %}{{ projectPropertyTypeVie }}{% endif %}" />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        View
    </label>
    <div class="col-sm-8">
        <input type="text" id="property_view" name="property_view" class="form-control" value="{% if projectPropertyViewVie is defined %}{{ projectPropertyViewVie }}{% endif %}" />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Tiện ích
    </label>
    <div class="col-sm-8">
        <input type="text" id="property_utility" name="property_utility" class="form-control" value="{% if projectPropertyUtilityVie is defined %}{{ projectPropertyUtilityVie }}{% endif %}" />
    </div>
</div>

<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
        Hướng
    </label>
    <div class="col-sm-3">
        <select name="direction" class="form-control">
            <option value="">--- Chọn ---</option>
            {% for key, value in trends %}
                {% if project.direction is defined and project.direction == key %}
                    <option value="{{ key }}" selected="selected">{{ value }}</option>
                {% else %}
                    <option value="{{ key }}">{{ value }}</option>
                {% endif %}
            {% endfor %}
        </select>
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_direction" class="error_message"></span></div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Tổng diện tích
    </label>
    <div class="col-sm-3">
        <input type="text" name="total_area" class="form-control format-number" value="{% if project.total_area is defined %}{{ project.total_area }}{% endif %}" />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Diện tích cây xanh
    </label>
    <div class="col-sm-3">
        <input type="text" name="green_area" class="form-control format-number" value="{% if project.green_area is defined %}{{ project.green_area }}{% endif %}" />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Số Block/Khu
    </label>
    <div class="col-sm-3">
        <input type="text" name="block_count" class="form-control format-number" value="{% if project.block_count is defined %}{{ project.block_count }}{% endif %}" />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Số sản phẩm
    </label>
    <div class="col-sm-3">
        <input type="text" name="apartment_count" class="form-control format-number" value="{% if project.apartment_count is defined %}{{ project.apartment_count }}{% endif %}" />
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#property_type').tagator({
            autocomplete: [{{ propertyTypeVie }}],
            showAllOptionsOnFocus: true
        });
        $('#property_view').tagator({
            autocomplete: [{{ propertyViewVie }}],
            showAllOptionsOnFocus: true
        });
        $('#property_utility').tagator({
            autocomplete: [{{ propertyUtilityVie }}],
            showAllOptionsOnFocus: true
        });
    });
</script>
