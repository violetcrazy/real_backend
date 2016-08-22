<div class="form-group">
    <label class="col-sm-3 control-label">
        Nhà nội thất
    </label>
    <div class="col-sm-3">
        {{ form.render('furniture_id', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'furniture_id'} %}
    </div>
</div>