{% extends 'default.volt' %}

{% block content %}
    {% set statusSelect = getCategoryStatus() %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh mục</h3>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'category_add_fengshui'}) }}" class="btn btn-primary float-right">Thêm tuổi</a>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-12">
            {{ flashSession.output() }}
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                        </th>
                        <th>Năm sinh/Tháng</th>
                        <th width="15%">Tuổi/Tháng</th>
                        <th width="10%">Bài viết</th>
                        <th width="10%">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    {% if categories is defined and categories|length %}
                        {% for item in categories %}
                            <tr>
                                <td>
                                    {{ item.id }}
                                </td>
                                <td>
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'category_edit_fengshui', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                        {{ item.name }}
                                    </a>
                                </td>
                                <td>
                                    {{ item.middle_name }}
                                </td>
                                <td>
                                    {{ item.article_count }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ url({'for': 'category_delete', 'query': '?' ~ http_build_query({'id': item.id})}) }}" onclick="javascript:return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky">
                                        <i class="fa fa-times fa fa-white"></i>
                                    </a>
                                </td>
                            </tr>
                            {% if subCategory[item.id] is defined %}
                                {{ subCategory[item.id] }}
                            {% endif %}
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