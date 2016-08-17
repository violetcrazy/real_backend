{% extends 'default.volt' %}

{% block content %}

    {% set statusSelect = getGroupStatus() %}
    {% set typeSelect = getGroupType() %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh mục</h3>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'category_add_group'}) }}" class="btn btn-primary float-right">Thêm nhóm</a>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                        </th>
                        <th>Tên</th>
                        <th width="15%" >Cập nhật</th>
                        <th width="10%" >Kiểu</th>
                        <th width="10%" >Trạng thái</th>
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
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'category_edit_group', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                        {{ item.name }}
                                    </a>
                                </td>
                                <td>
                                    {{ date('d-m-Y', strtotime(item.updated_at)) }}
                                </td>
                                <td>
                                    {{ typeSelect[item.type] }}
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