{% extends 'default.volt' %}

{% block content %}
    {% set statusSelect = getArticleStatus() %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh sách bài viết phong thuỷ</h3>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'article_add_fengshui'}) }}" class="btn btn-primary float-right">Thêm bài viết phong thủy</a>
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
                        <th width="35%">Tuổi</th>
                        <th width="10%">Trạng thái</th>
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
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'article_edit_fengshui', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                        {{ item.name }}
                                    </a>
                                </td>
                                <td>
                                    {{ item.slug }}
                                </td>
                                <td>
                                    {% if categoryLayout[item.id] is defined and categoryLayout[item.id]|length %}
                                        {{ implode(', ', categoryLayout[item.id]) }}
                                    {% endif %}
                                </td>
                                <td>
                                    {{ statusSelect[item.status] }}
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