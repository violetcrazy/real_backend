{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thêm thành viên</h3>
            </div>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-2 control-label">
                Số điện thoại
            </label>
            <div class="col-sm-5">
                {{ form.render('display', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'display'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
            </label>
            <div class="col-sm-5">
                {{ form.render('is_verified', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'is_verified'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Mật khẩu
            </label>
            <div class="col-sm-5">
                {{ form.render('password', {'class': 'form-control', 'autocomplete': 'off', 'value': random_password}) }}
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
                Giới tính
            </label>
            <div class="col-sm-5">
                {{ form.render('gender', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'gender'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Ngày sinh
            </label>
            <div class="col-sm-5">
                {{ form.render('birthday', {'class': 'form-control date-picker', 'data-date-format': 'dd-mm-yyyy'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'birthday'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                CMND/Passport
            </label>
            <div class="col-sm-5">
                {{ form.render('identity_number', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'identity_number'} %}
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
                <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': filter})}) }}" class="btn btn-primary">
                    Trở lại
                </a>
            </div>
        </div>
    </form>
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript">
        $(document).ready(function () {
            $('.date-picker').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
        });
    </script>
{% endblock %}