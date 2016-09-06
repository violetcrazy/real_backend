<div id="form_tab5_error_message" class="alert alert-danger" style="display: none;"></div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Trạng thái
    </label>
    <div class="col-sm-3">
        {{ form.render('status', {'class': 'form-control'}) }}
    </div>
</div>
    
<h3>Tiếng Việt</h3>
<hr />
<div class="form-group">
    <label class="col-sm-2 control-label">
        Giá tổng quan
    </label>
    <div class="col-sm-8">
        <textarea name="price" id="" cols="30" rows="3" class="form-control">{{ result.price is defined and result.price != '' ? result.price : '' }}</textarea>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price'} %}
    </div>
</div>
    
<h3>Tiếng Anh</h3>
<hr />  
<div class="form-group">
    <label class="col-sm-2 control-label">
        Giá tổng quan
    </label>
    <div class="col-sm-8">
        <textarea name="price_eng" id="" cols="30" rows="3" class="form-control">{{ result.price_eng is defined and result.price_eng != '' ? result.price_eng : '' }}</textarea>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_eng'} %}
    </div>
</div>