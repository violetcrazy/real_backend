{% extends 'default.volt' %}

{% block content %}
    {% set statusSelect = getCeriterialStatus() %}
    {% set ceriterialIsHome = getCeriterialIsHome() %}

    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Box hiển thị sản phẩm cho thuê</h3>
            </div>
        </div>

        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'ceriterial_add', 'query': '?' ~ http_build_query({'for': 'rent'})}) }}" class="btn btn-primary float-right">Thêm</a>
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th>Tiêu đề</th>
                        <th>Hiển thị trang chủ</th>
                        <th width="15%">Cập nhật</th>
                        <th width="15%">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    {% if ceriterials is defined and ceriterials|length %}
                        {% for item in ceriterials %}
                            <tr>
                                <td>{{ item.id }}</td>
                                <td>
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'ceriterial_edit', 'query': '?' ~ http_build_query({'id': item.id, 'for': 'rent'})}) }}">
                                        {{ item.name }}
                                    </a>
                                </td>
                                <td>{{ ceriterialIsHome[item.is_home] }}</td>
                                <td>{{ item.updated_at }}</td>
                                <td>{{ statusSelect[item.status] }}</td>
                            </tr>
                        {% endfor %}
                    {% endif %}
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
            {{ paginationLayout }}
        </div>
    </div>
{% endblock %}