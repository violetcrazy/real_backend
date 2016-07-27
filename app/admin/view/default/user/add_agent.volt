{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Thêm Đại lí</h3>
            </div>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-2 control-label">
                Tên đăng nhập
            </label>
            <div class="col-sm-5">
                {{ form.render('username', {'class': 'form-control', 'placeholder' : 'tên đăng nhập là email hoặc số điện thoại'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'username'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Mật khẩu
            </label>
            <div class="col-sm-5">
                {{ form.render('password', {'class': 'form-control', 'autocomplete': 'off'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'password'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Họ tên
            </label>
            <div class="col-sm-5">
                {{ form.render('name', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Email
            </label>
            <div class="col-sm-5">
                {{ form.render('email', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'email'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Loại
            </label>
            <div class="col-sm-5">
                {{ form.render('membership', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'membership'} %}
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
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-5">
                <button type="submit" class="btn btn-bricky">
                    Thêm
                </button>
                <a href="{{ url({'for': 'user_agent', 'query': '?' ~ http_build_query({'q': q, 'filter': filter})}) }}" class="btn btn-primary">
                    Trở lại
                </a>
            </div>
        </div>
    </form>
{% endblock %}