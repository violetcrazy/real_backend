{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Chỉnh sửa nhà cung cấp nội thất</h3>
            </div>
        </div>
    </div>

    <form id="form-furniture" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <h3>Tiếng Việt</h3>
        <hr />
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tên nhà nội thất
            </label>
            <div class="col-sm-3">
                {{ form.render('name', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Giới thiệu
            </label>
            <div class="col-sm-3">
                {{ form.render('intro', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Địa chỉ
            </label>
            <div class="col-sm-3">
                {{ form.render('address', {'class': 'form-control'}) }}
            </div>
        </div>

        <h3>Tiếng Anh</h3>
        <hr />
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tên nhà nội thất
            </label>
            <div class="col-sm-3">
                {{ form.render('name_eng', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Giới thiệu
            </label>
            <div class="col-sm-3">
                {{ form.render('intro_eng', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Địa chỉ
            </label>
            <div class="col-sm-3">
                {{ form.render('address_eng', {'class': 'form-control'}) }}
            </div>
        </div>
        <hr>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Email
            </label>
            <div class="col-sm-3">
                {{ form.render('email', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Điện thoại
            </label>
            <div class="col-sm-3">
                {{ form.render('phone', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Trạng thái
            </label>
            <div class="col-sm-3">
                {{ form.render('status', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-8">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    Cập nhật
                </button>
                <a href="{{ url({'for': 'apartment_furniture_list'}) }}" class="btn btn-primary">
                    <span class="fa-mail-reply fa"></span>
                    Trở lại
                </a>
            </div>
        </div>
    </form>
{% endblock %}
