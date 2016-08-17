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