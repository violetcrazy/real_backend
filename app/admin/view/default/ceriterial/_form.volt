<div class="tabbable">
    <div class="tab-content">
        <div class="tab-pane in active" id="panel_tab1">
            {% include 'default/ceriterial/_form_vi.volt' %}
        </div>
    </div>
    <hr />
    <h3>Tiếng Việt</h3>
    <hr />
    <div class="form-group">
        <label class="col-sm-3 control-label">
            Giá thấp nhất
        </label>
        <div class="col-sm-3">
            {{ form.render('price_min', {'class': 'form-control format-number'}) }}
            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_min'} %}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">
            Giá cao nhất
        </label>
        <div class="col-sm-3">
            {{ form.render('price_max', {'class': 'form-control format-number'}) }}
            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_max'} %}
        </div>
    </div>
    <h3>Tiếng Anh</h3>
    <hr />
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
    <hr />
    {% if f != 'smart-search' %}
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Hiển thị trang chủ
            </label>
            <div class="col-sm-3">
                {{ form.render('is_home', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'is_home'} %}
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
                Hiển thị
            </label>
            <div class="col-sm-3">
                {{ form.render('template', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'template'} %}
            </div>
        </div>
    {% endif %}

    <div class="form-group">
        <label class="col-sm-3 control-label">
            Trạng thái
        </label>
        <div class="col-sm-3">
            {{ form.render('status', {'class': 'form-control'}) }}
            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.search-select').select2({
            allowClear: true
        });
    });
</script>