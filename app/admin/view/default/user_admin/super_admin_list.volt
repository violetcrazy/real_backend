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
                {% if user_session['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN') %}
                    <a href="{{ url({'for': 'user_add_admin', 'query': '?' ~ http_build_query({'q': q, 'filter': 'super_admin_list'})}) }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i>
                        Thêm Super Admin
                    </a>
                {% endif %}

                <h3>Danh sách Super Admin</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="col-sm-12">
            <form action="" method="GET" class="sidebar-search-form" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="text" name="q" value="{% if q is defined %}{{ q }}{% endif %}" class="form-control" placeholder="Tìm kiếm" />
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-success">Tìm kiếm</button>
                    </span>
                </div>
            </form>
            <br />
        </div>
        <div class="clearfix"></div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Họ tên</th>
                        <th>Loại</th>
                        <th>Đăng ký</th>
                        <th>Đăng nhập</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {% for item in result %}
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>
                                <a href="{{ url({'for': 'user_edit_admin', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'filter': 'super_admin_list'})}) }}">
                                    {{ item.username }}
                                </a>
                            </td>
                            <td>{{ item.name }}</td>
                            <td>{{ userMembershipAdmin[item.membership] }}</td>
                            <td>{{ date('d-m-Y H:i:s', strtotime(item.created_at)) }}</td>
                            <td>
                                {% if strtotime(item.logined_at) %}
                                    {{ date('d-m-Y H:i:s', strtotime(item.logined_at)) }}
                                {% endif %}
                            </td>
                            <td>{{ userStatus[item['status']] }}</td>
                            <td class="text-center">
                                {% if user_session['id'] != item['id'] %}
                                    <a href="{{ url({'for': 'user_delete_admin', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'filter': 'super_admin_list'})}) }}" onclick="return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky">
                                        <i class="fa fa-times fa fa-white"></i>
                                    </a>
                                {% endif %}
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
{% endblock %}
