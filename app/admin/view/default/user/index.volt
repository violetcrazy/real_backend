{% extends 'default.volt' %}

{% block content %}
    {% set user_status = getUserStatus() %}

    <div class="row">
        <div class="col-sm-12">
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
            <div class="clearfix"></div>
            <hr />
        </div>
    </div>

    {{ flashSession.output() }}

    <table class="table table-striped table-bordered table-hover table-full-width">
        <thead>
            <tr>
                <th>Username</th>
                <th>Loại</th>
                <th>Đăng ký</th>
                <th>Cập nhật</th>
                <th>Đăng nhập</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {% for item in users.items %}
                <tr>
                    <td>
                        <a href="{{ url({'for': 'user_edit', 'query': '?' ~ http_build_query({'id': item.id, 'from': 'list'})}) }}">
                            {{ item.phone }}
                        </a>
                        <br />
                        {{ item.name }}
                    </td>
                    <td>{{ item.type }}</td>
                    <td>{{ date('d-m-Y H:i:s', strtotime(item.created_at)) }}</td>
                    <td>{{ date('d-m-Y H:i:s', strtotime(item.updated_at)) }}</td>
                    <td>{{ date('d-m-Y H:i:s', strtotime(item.logined_at)) }}</td>
                    <td>{{ user_status[item.status] }}</td>
                    <td class="text-center">
                        <a href="{{ url({'for': 'user_delete', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'from': 'list'})}) }}" class="btn btn-xs btn-bricky">
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
                                <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'page': result.before})}) }}">
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
                                    <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'page': i})}) }}">
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
                                    <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'page': i})}) }}">
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
                                <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'page': result.next})}) }}">
                                    <span>Sau</span>
                                </a>
                            </li>
                        {% endif %}
                    </ul>
                </div>
            </div>
        </div>
    {% endif %}
{% endblock %}