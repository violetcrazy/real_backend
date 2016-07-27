<div class="form-group">
    <label class="col-sm-3 control-label">
        Ảnh vị trí
    </label>
    <div class="col-sm-5">
        {% set value = '' %}
        {% set src = '' %}

        {% if apartment.position_image is defined and apartment.position_image != '' %}
            {% set value = apartment.position_image %}
            {% set src = config.cdn.dir_upload ~ 'thumbnail/' ~ apartment.position_image %}
        {% endif %}

        {{ templateUpload('position_image', src,value) }}
    </div>
</div>

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