{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Danh sách sản phẩm ({{ project.name }})</h3>

                <a href="{{ url({'for': 'project_edit', 'query': '?id=' ~ project.id }) }}">
                    {{ project.name }}
                </a>
                &gt;
                <span>Danh sách sản phẩm</span>
            </div>

            {#
            <form action="" method="GET" class="sidebar-search-form" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="text" name="q" value="{% if q is defined %}{{ q }}{% endif %}" class="form-control" placeholder="Tìm kiếm" />
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-bricky">Tìm kiếm</button>
                    </span>
                </div>
            </form>
            <div class="clearfix"></div>
            <hr />
            #}
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th>Sản phẩm</th>
                        <th>Tầng</th>
                        <th>Block</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    {% if apartments.items is defined and apartments.items|length %}
                        {% for item in apartments.items %}
                            <tr>
                                <td>
                                    {{ item['id'] }}
                                </td>
                                <td>
                                    <a role="menuitem" href="{{ url({'for': 'apartment_edit', 'query': '?' ~ http_build_query({'project_id': project.id, 'block_id': item['block_id'], 'id': item['id'], 'page': page, 'from': 'list-by-project'})}) }}">
                                        {{ item['name'] }}
                                    </a>
                                </td>
                                <td>{{ item['floor_count'] }}</td>
                                <td>{{ item['block_name'] }}</td>
                                <td class="text-center">
                                    <a href="{{ url({'for': 'apartment_delete', 'query': '?' ~ http_build_query({'id': item['id'], 'project_id': project.id, 'from': 'list-by-project'})}) }}" onclick="javascript:return confirm('Đồng ý xoá.?');" class="btn btn-xs btn-bricky">
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

        <div class="col-sm-12">
            {{ paginationLayout }}
        </div>
    </div>
{% endblock %}