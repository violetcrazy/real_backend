{% extends 'default.volt' %}

{% block content %}
    {% set user_session = session.get('USER') %}
    {% set user_status = getUserStatus() %}
    {% set userMembership = getUserMembership() %}
    {% set userMembershipAdmin = getUserMembershipAdministrator() %}
    {% set userMembershipAgent = getUserMembershipAgent() %}
    {% set active = 'active' %}

    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>{{ header_title }}</h3>
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
                    <ul class="dropdown-menu notifications" role="menu">
                        <li role="presentation" class="{% if filter == 'today' %} {{ active }} {% endif %}">
                            <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'today'})}) }}">
                                <span class="desc" style="opacity: 1; text-decoration: none;">
                                    Mới hôm nay
                                </span>
                                <span class="label label-success float-right">{{ count_today }}</span>
                            </a>
                        </li>
                        <li role="presentation" class="{% if filter == 'uncertified' %} {{ active }} {% endif %}">
                            <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'uncertified'})}) }}">
                                Chưa xác thực
                                <span class="label label-success float-right">{{ count_uncertified }}</span>
                            </a>
                        </li>
                        <li role="presentation" class="{% if filter == 'uncall' %} {{ active }} {% endif %}">
                            <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'uncall'})}) }}">
                                Chưa liên lạc
                                <span class="label label-success float-right">{{ count_uncall }}</span>
                            </a>
                        </li>
                        <li role="presentation" class="{% if filter == 'basic' %} {{ active }} {% endif %}">
                            <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'basic'})}) }}">
                                Basic
                                <span class="label label-success float-right">{{ count_basic }}</span>
                            </a>
                        </li>
                        <li role="presentation" class="{% if filter == 'vip' %} {{ active }} {% endif %}">
                            <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'vip'})}) }}">
                                Vip
                                <span class="label label-success float-right">{{ count_vip }}</span>
                            </a>
                        </li>
                        <li role="presentation" class="{% if filter == 'partner' %} {{ active }} {% endif %}">
                            <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'partner'})}) }}">
                                Partner
                                <span class="label label-success float-right">{{ count_partner }}</span>
                            </a>
                        </li>

                        {% if user_session['membership'] == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN') %}
                            <li role="presentation" class="{% if filter == 'admin' %}{{ active }}{% endif %}">
                                <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'admin'})}) }}">
                                    Quản trị viên
                                    <span class="label label-success float-right">{{ count_admin }}</span>
                                </a>
                            </li>
                        {% endif %}

                        {% if user_session['membership'] == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_CUSTOMER_CARE') and user_session['is_leader'] == constant('\MBN\Data\Lib\Constant::ADMIN_PERMISSION_IS_LEADER_YES') %}
                            <li role="presentation" class="{% if filter == 'admin' %}{{ active }}{% endif %}">
                                <a role="menuitem" tabindex="-1" href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': 'admin'})}) }}">
                                    Quản trị viên
                                    <span class="label label-success float-right">{{ count_admin }}</span>
                                </a>
                            </li>
                        {% endif %}
                    </ul>
                </div>
                <div class="float-left m-r-l-5">
                    <a href="{{ url({'for': 'user_add', 'query': '?' ~ http_build_query({'q': q, 'filter': filter})}) }}" class="btn btn-success border-tl-tr">
                        <i class="fa fa-plus"></i>
                        Thêm thành viên
                    </a>
                </div>

                {% if user_session['type'] == constant('\MBN\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR') and user_session['membership'] == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN') %}
                    <div class="float-left m-r-l-5">
                        <a href="{{ url({'for': 'user_add_admin', 'query': '?' ~ http_build_query({'q': q, 'filter': 'admin'})}) }}" class="btn btn-bricky border-tl-tr">
                            <i class="fa fa-plus"></i>
                            Thêm quản trị viên
                        </a>
                    </div>
                {% endif %}
            </div>
        </div>
        <div class="col-sm-12">
        <table class="table table-striped table-bordered table-hover table-full-width">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Loại</th>
                    <th>Đăng ký</th>
                    <th>Cập nhật</th>
                    <th>Đăng nhập</th>
                    <th>CSKH</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {% for item in users.items %}
                    <tr>
                        <td>
                            {% if item.type == constant('\MBN\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR') %}
                                <a href="{{ url({'for': 'user_edit_admin', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'filter': filter})}) }}">
                                    {{ item.username }}
                                </a>
                            {% else %}
                                <a href="{{ url({'for': 'user_edit', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'filter': filter})}) }}">
                                    {{ item.username }}
                                </a>
                            {% endif %}
                            <br />
                            {{ item.name }}
                        </td>

                        {% if item.type == constant('\MBN\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR') %}
                            <td>
                                {{ userMembershipAdmin[item.membership] }}

                                {% if item.is_leader is defined and item.is_leader == constant('\MBN\Data\Lib\Constant::ADMIN_PERMISSION_IS_LEADER_YES') %}
                                        (Trưởng nhóm)
                                {% endif %}
                            </td>
                        {% else %}
                            <td>{{ userMembership[item.membership] }}</td>
                        {% endif %}

                        <td>{{ date('d-m-Y H:i:s', strtotime(item.created_at)) }}</td>
                        <td>{{ date('d-m-Y H:i:s', strtotime(item.updated_at)) }}</td>
                        <td>
                            {% if strtotime(item.logined_at) %}
                                {{ date('d-m-Y H:i:s', strtotime(item.logined_at)) }}
                            {% endif %}
                        </td>
                        <td>
                            {{ item.customer_care }}
                        </td>
                        <td>{{ user_status[item.status] }}</td>
                        <td class="text-center">
                            <a href="{{ url({'for': 'user_delete', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'filter': filter})}) }}" onclick="javascript:return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky">
                                <i class="fa fa-times fa fa-white"></i>
                            </a>
                        </td>
                    </tr>
                {% endfor %}
            </tbody>
        </table>

        {% set result = users %}
        {% if result.total_pages > 1 %}
            <div class="row text-right">
                <div class="col-md-12">
                    <div class="dataTables_paginate paging_bootstrap">
                        <ul class="pagination">
                            {% if result.before == result.current %}
                                <li class="prev">
                                    <a>
                                        <span>Trước</span>
                                    </a>
                                </li>
                            {% else %}
                                <li class="prev">
                                    <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': filter, 'page': result.before})}) }}">
                                        <span>Trước</span>
                                    </a>
                                </li>
                            {% endif %}

                            {% if result.current == result.last %}
                                {% set start = result.current - 4 %}
                            {% else %}
                                {% set start = result.current - 3 %}
                            {% endif %}

                            {% for i in start..result.current - 1 %}
                                {% if i > 0 %}
                                    <li>
                                        <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': filter, 'page': i})}) }}">
                                            {{ i }}
                                        </a>
                                    </li>
                                {% endif %}
                            {% endfor %}

                            <li class="active">
                                <a>
                                    {{ result.current }}
                                </a>
                            </li>

                            {% if result.current == 1 %}
                                {% set end = result.current + 4 %}
                            {% else %}
                                {% set end = result.current + 3 %}
                            {% endif %}

                            {% for i in result.current + 1..end %}
                                {% if i <= result.last %}
                                    <li>
                                        <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': filter, 'page': i})}) }}">
                                            {{ i }}
                                        </a>
                                    </li>
                                {% endif %}
                            {% endfor %}

                            {% if result.next == result.current %}
                                <li class="next">
                                    <a>
                                        <span>Sau</span>
                                    </a>
                                </li>
                            {% else %}
                                <li class="next">
                                    <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': filter, 'page': result.next})}) }}">
                                        <span>Sau</span>
                                    </a>
                                </li>
                            {% endif %}
                        </ul>
                    </div>
                </div>
            </div>
        {% endif %}
        </div>
    </div>
{% endblock %}