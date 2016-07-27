{% extends 'default.volt' %}

{% block content %}
    {% set user_session = session.get('USER') %}
    {% set userStatus = getUserStatus() %}
    {% set userMembership = getUserMembership() %}
    {% set userMembershipAdmin = getUserMembershipAdministrator() %}
    {% set userMembershipAgent = getUserMembershipAgent() %}
    {% set active = 'active' %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh sách thành viên</h3>
            </div>
            <form action="" method="GET" class="sidebar-search-form" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="text" name="q" value="{% if q is defined %}{{ q }}{% endif %}" class="form-control" placeholder="Tìm kiếm" />
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-success">Tìm kiếm</button>
                    </span>
                </div>
            </form>
        </div>

        <div class="col-sm-12">
            <div class="clearfix"></div>
            <hr />
            {{ flashSession.output() }}
        </div>

        <div class="col-sm-12">
            <div class="navbar-tools">
                <div class="btn-group float-left">
                    <a class="btn btn-primary border-tl-tr dropdown-toggle" data-hover="dropdown" href="#">
                        <i class="clip-list-5"></i>
                        Danh sách thành viên
                        <span class="caret"></span>
                    </a>
                </div>
                <div class="float-left m-r-l-5">
                    <a href="{{ url({'for': 'user_add_member', 'query': '?' ~ http_build_query({'q': q})}) }}" class="btn btn-success border-tl-tr">
                        <i class="fa fa-plus"></i>
                        Thêm thành viên
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <table class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Loại</th>
                        <th>Đăng ký</th>
                        <th>Đăng nhập</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                {% if result is defined and result|length %}
                    {% for item in result %}
                        <tr>
                            <td>
                                <a href="{{ url({'for': 'user_edit_member', 'query': '?' ~ http_build_query({'id': item.id, 'q': q})}) }}">
                                    {{ item.username }}
                                </a>
                            </td>
                            <td>{{ userMembership[item.membership] }}</td>
                            <td>{{ date('d-m-Y H:i:s', strtotime(item.created_at)) }}</td>
                            <td>
                                {% if strtotime(item.logined_at) %}
                                    {{ date('d-m-Y H:i:s', strtotime(item.logined_at)) }}
                                {% endif %}
                            </td>
                            <td>
                                <div class="visible-md visible-lg hidden-sm hidden-xs">
                                    <a class="btn btn-xs btn-teal tooltips" data-original-title="Gửi email" data-placement="top" href="{{ url({'for': 'user_add_email', 'query': '?' ~ http_build_query({'uid': item.id})}) }}">
                                        <i class="clip-image"></i>
                                    </a>
                                    <a class="btn btn-xs btn-teal tooltips" data-original-title="Gửi thông báo" data-placement="top" href="{{ url({'for': 'user_add_message', 'query': '?' ~ http_build_query({'uid': item.id})}) }}">
                                        <i class="clip-bubbles-3"></i>
                                    </a>
                                </div>
                            </td>
                            <td>{{ userStatus[item['status']] }}</td>
                            <td class="text-center">
                                <a href="{{ url({'for': 'user_delete', 'query': '?' ~ http_build_query({'id': item['id'], 'q': q})}) }}" onclick="javascript:return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky">
                                    <i class="fa fa-times fa fa-white"></i>
                                </a>
                            </td>
                        </tr>
                    {% endfor %}
                {% endif %}
                </tbody>
            </table>
            <div class="col-sm-12">
                {{ paginationLayout }}
            </div>
        </div>
    </div>
{% endblock %}