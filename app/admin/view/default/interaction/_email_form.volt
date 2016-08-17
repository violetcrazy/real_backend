<div class="form-group">
    <label class="col-sm-2 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-5">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Email gửi
    </label>
    <div class="col-sm-5">
        {{ form.render('email', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'email'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Nội dung
    </label>
    <div class="col-sm-5">
        {{ form.render('description', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Trạng thái
    </label>
    <div class="col-sm-5">
        {{ form.render('status', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
    </div>
</div>