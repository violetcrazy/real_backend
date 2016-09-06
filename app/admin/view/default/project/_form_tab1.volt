<div id="form_tab1_error_message" class="alert alert-danger" style="display: none;"></div>

<h3>Tiếng Việt</h3>
<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên dự án <span class="symbol required"></span>
    </label>
    <div class="col-sm-8">
        <input type="text" name="name" class="form-control" value="{% if project.name is defined %}{{ project.name }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_name" class="error_message"></span></div>
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">
        Địa chỉ <span class="symbol required"></span>
    </label>
    <div class="col-sm-8">
        <input type="text" name="address" class="form-control" value="{% if project.address is defined %}{{ project.address }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_address" class="error_message"></span></div>
    </div>
</div>

<h3>Tiếng Anh</h3>
<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên dự án <span class="symbol required"></span>
    </label>
    <div class="col-sm-8">
        <input type="text" name="name_eng" class="form-control" value="{% if project.name_eng is defined %}{{ project.name_eng }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_name_eng" class="error_message"></span></div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Địa chỉ <span class="symbol required"></span>
    </label>
    <div class="col-sm-8">
        <input type="text" name="address_eng" class="form-control" value="{% if project.address_eng is defined %}{{ project.address_eng }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_address_eng" class="error_message"></span></div>
    </div>
</div>

<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
        Tỉnh thành <span class="symbol required"></span>
    </label>
    <div class="col-sm-3">
        <select id="form_tab1_select_province" name="province_id" class="form-control">
            <option value="">--- Chọn ---</option>
            {% for key, value in provinces %}
                {% if project.province_id is defined and project.province_id == key %}
                    <option value="{{ key }}" selected="selected">{{ value }}</option>
                {% else %}
                    <option value="{{ key }}">{{ value }}</option>
                {% endif %}
            {% endfor %}
        </select>
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_province_id" class="error_message"></span></div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Quận huyện
    </label>
    <div class="col-sm-3">
        <select id="form_tab1_select_district" name="district_id" class="form-control">
            <option value="">--- Chọn ---</option>
            {% if project.province_id is defined and project.province_id != '' %}
                {% if districts[project.province_id] is defined and districts[project.province_id]|length %}
                    {% for key, value in districts[project.province_id] %}
                        {% if project.district_id is defined and project.district_id == key %}
                            <option value="{{ key }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ key }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                {% endif %}
            {% endif %}
        </select>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var process = false;

        $(document).on('change', '#form_tab1_select_province', function() {
            var provinceId = $(this).val();

            if (!process) {
                process = true;

                $.ajax({
                    url: '{{ url({'for': 'load_district_ajax'}) }}',
                    type: 'GET',
                    data: {'province_id': provinceId},
                    success: function(response) {
                        $('#form_tab1_select_district').html(response);
                    }
                }).done(function() {
                    process = false;
                });
            }
        });
    });
</script>
