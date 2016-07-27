<h3>Tiếng việt</h3>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_title', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_title'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Từ khóa
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_keywords', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_keywords'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Mô tả
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_description', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_description'} %}
    </div>
</div>
<hr>
<h3>Tiếng anh</h3>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_title_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_title_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Từ khóa
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_keywords_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_keywords_eng'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">
        Mô tả
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_description_eng', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_description_eng'} %}
    </div>
</div>