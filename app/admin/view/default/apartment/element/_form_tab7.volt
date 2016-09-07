<div class="form-group">
    <label class="col-sm-3 control-label">
        Tình trạng
    </label>
    <div class="col-sm-5">
        {{ form.render('condition', {'class': 'form-control', 'disabled': 'disabled'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'condition'} %}
    </div>
</div>

{% if requestInfo is defined and requestInfo['user_name'] is defined %}
    <div class="form-group">
        <label class="col-sm-3 control-label">
            Người đặt mua
        </label>
        <div class="col-sm-7">
            <div class="input-group">
                <input type="text" class="form-control" disabled="disabled" value="{{ requestInfo['user_name'] ~ ' (' ~ requestInfo['user_phone'] ~ ')' ~ ' - ' ~ requestInfo['user_email'] }}" />
                <span class="input-group-btn">
                    <button type="button" class="btn btn-edit btn-warning" onclick="javascript:window.location.href = '{{ url({'for': 'user_edit_member', 'query': '?' ~ http_build_query({'id': requestInfo['user_id']})}) }}';">
                        Thông tin
                    </button>
                </span>
            </div>
        </div>
    </div>
{% endif %}

<div class="form-group">
    <label class="col-sm-3 control-label">
        Trạng thái
    </label>
    <div class="col-sm-5">
        {{ form.render('status', {'class': 'form-control'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Hoa hồng
    </label>
    <div class="col-sm-5">
        {{ form.render('rose', {'class': 'form-control'}) }}
    </div>
</div>
<h3>Tiếng Việt</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Giá bán
    </label>
    <div class="col-sm-5">
        {{ form.render('price', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Giá khuyến mãi
    </label>
    <div class="col-sm-5">
        {{ form.render('price_sale_off', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_sale_off'} %}
    </div>
</div>
    
<h3>Tiếng Anh</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Giá bán
    </label>
    <div class="col-sm-5">
        {{ form.render('price_eng', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_eng'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Giá khuyến mãi
    </label>
    <div class="col-sm-5">
        {{ form.render('price_sale_off_eng', {'class': 'form-control format-number'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_sale_off_eng'} %}
    </div>
</div>