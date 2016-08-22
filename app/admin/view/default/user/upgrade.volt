{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Đăng ký VIP</h3>
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
                <input type="text" class="form-control" value="{{ user.display }}" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Họ tên
            </label>
            <div class="col-sm-5">
                <input type="text" class="form-control" value="{{ user.name }}" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Thời hạn VIP
            </label>
            <div class="col-sm-5">
                {{ form.render('membership_expired_at', {'class': 'form-control date-picker', 'data-date-format': 'dd-mm-yyyy'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'membership_expired_at'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Chăm sóc KH
            </label>
            <div class="col-sm-5">
                {{ form.render('referral_by', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'referral_by'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-5">
                <button type="submit" class="btn btn-bricky">Đăng ký</button>
                <a href="{{ url({'for': 'user_edit', 'query': '?' ~ http_build_query({'id': user.id, 'filter': filter})}) }}" class="btn btn-primary">Trở lại</a>
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