<h3>Tiếng Việt</h3>
<hr />

<div class="form-group">
    <label class="col-sm-3 control-label">
        Mô tả vị trí
    </label>
    <div class="col-sm-8 block-upload-img">
        {{ form.render('position', {'class': 'form-control'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Nội dung
    </label>
    <div class="col-sm-8">
        {{ form.render('description', {'class': 'form-control', 'style': 'height: 300px'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description'} %}
    </div>
</div>

<h3>Tiếng Anh</h3>
<hr />

<div class="form-group">
    <label class="col-sm-3 control-label">
        Mô tả vị trí
    </label>
    <div class="col-sm-8 block-upload-img">
        {{ form.render('position_eng', {'class': 'form-control'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Nội dung
    </label>
    <div class="col-sm-8">
        {{ form.render('description_eng', {'class': 'form-control', 'style': 'height: 300px'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description_eng'} %}
    </div>
</div>