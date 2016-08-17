{% extends 'default.volt' %}

{% block content %}
    {% set apartmentRequestMethod = getApartmentRequestMethod() %}
    {% set apartmentRequestStatus = getApartmentRequestStatus() %}

    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                {% include 'default/element/layout/breadcrumbs.volt' %}
                <h3>Chi tiết yêu cầu</h3>
            </div>
        </div>

        <div class="col-sm-12">
            <form class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Tài khoản
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ user.name }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Điện thoại
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ user.phone }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Email
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ user.email }}" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Sản phẩm
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ apartment.name }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Block
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ apartmentBlock.name }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Dự án
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ project.name }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Mô tả
                    </label>
                    <div class="col-sm-5">
                        <textarea class="form-control" disabled="disabled">{{ apartmentRequest.description }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Phương thức chi trả
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ apartmentRequestMethod[apartmentRequest.pay_method] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        Trạng thái
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" disabled="disabled" value="{{ apartmentRequestStatus[apartmentRequest.status] }}" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3"></label>
                    <div class="col-sm-5">
                        <a href="{{ url({'for': 'apartment_request_approve', 'query': '?' ~ http_build_query({'id': apartmentRequest.id})}) }}" class="btn btn-success">Duyệt</a>
                        <a href="{{ url({'for': 'apartment_request_reject', 'query': '?' ~ http_build_query({'id': apartmentRequest.id})}) }}" class="btn btn-danger">Từ chối</a>
                        <a href="{{ url({'for': 'apartment_request_list'}) }}" class="btn btn-default">Trở lại</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}