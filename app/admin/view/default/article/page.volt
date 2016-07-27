{% extends 'default.volt' %}

{% block content %}
    {% set statusSelect = getArticleStatus() %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh sách trang tĩnh</h3>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'article_add_page'}) }}" class="btn btn-primary float-right">Thêm trang tĩnh</a>
            <div class="clearfix"></div>
        </div>
            
        <div class="col-sm-12">
            {{ flashSession.output() }}
            
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                        </th>
                        <th>Tên</th>
                        <th width="15%">Slug</th>
                        <th width="10%">Trạng thái</th>
                        <th width="10%"></th>
                    </tr>
                </thead>
                <tbody>
                    {% if articles is defined and articles|length %}
                        {% for item in articles %}
                            <tr>
                                <td>
                                    {{ item.id }}
                                </td>
                                <td>
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'article_edit_page', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                        {{ item.name }}
                                    </a>
                                </td>
                                <td>
                                    {{ item.slug }}
                                </td>
                                <td>
                                    {{ statusSelect[item.status] }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ url({'for': 'article_delete_page', 'query': '?' ~ http_build_query({'id': item.id })}) }}" onclick="javascript:return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">
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