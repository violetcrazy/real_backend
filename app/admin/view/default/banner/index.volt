{% extends 'default.volt' %}

{% block content %}
    {% set statusSelect = getBannerStatus() %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh sách banner</h3>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'banner_add'}) }}" class="btn btn-primary float-right">Thêm</a>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                        </th>
                        <th>Tên</th>
                        <th width="15%">Mô tả</th>
                        <th width="10%">Click</th>
                        <th width="10%">Hình ảnh</th>
                        <th width="10%">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    {% if result is defined and result|length %}
                        {% for item in result %}
                            <tr>
                                <td>
                                    {{ item.id }}
                                </td>
                                <td>
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'banner_edit', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                        {{ item.name }}
                                    </a>
                                    <br />
                                    {{ item.url }}
                                </td>
                                <td>
                                    {{ item.description }}
                                </td>
                                <td>
                                    {{ item.click }}
                                </td>
                                <td>
                                    {% if item.image is defined and item.image != '' %}
                                        <img width="120px" src="{{ config.asset.frontend_url ~ 'upload/banner/' ~ item.image }}" alt="{{ item.name }}" />
                                    {% endif %}
                                </td>
                                <td>
                                    {{ statusSelect[item.status] }}
                                </td>
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