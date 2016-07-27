{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh sách nhà nội thất</h3>
            </div>
            <div class="col-sm-12 m-b-20">
                <a href="{{ url({'for': 'apartment_furniture_add'}) }}" class="btn btn-primary float-right">Thêm nhà nội thất</a>
                <div class="clearfix"></div>
            </div>
            <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-jquery table-hover" id="table-list-attr">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Tên tiếng anh</th>
                    <th>Điện thoại</th>
                    <th width="5%">Xóa</th>
                </tr>
                </thead>

                <tbody>
                    {% if furniture is defined %}
                        {% for item in furniture.items %}
                            <tr>
                                <td>
                                    {{ item.id }}
                                </td>
                                <td>
                                    <a role="menuitem" href="{{ url({'for': 'apartment_furniture_edit', 'query': '?' ~ http_build_query({'id': item.id, 'page': page})}) }}">
                                        {{ item.name }}
                                    </a>
                                </td>
                                <td>
                                    <a role="menuitem" href="{{ url({'for': 'apartment_furniture_edit', 'query': '?' ~ http_build_query({'id': item.id, 'page': page})}) }}">
                                        {{ item.name_eng }}
                                    </a>
                                </td>
                                <td>{{ item.phone }}</td>
                                <td class="text-center">
                                    <a href="{{ url({'for': 'apartment_furniture_delete', 'query': '?' ~ http_build_query({'id': item.id, 'page': page})}) }}" onclick="javascript:return confirm('Đồng ý xoá.?');" class="btn btn-xs btn-bricky">
                                        <i class="fa fa-times fa fa-white"></i>
                                    </a>
                                </td>
                            </tr>
                        {% endfor %}
                    {% endif %}
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>
        </div>
    </div>
{% endblock %}