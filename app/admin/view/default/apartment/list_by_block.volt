{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Danh sách sản phẩm ({{ result['name'] }})</h3>
                
                <a href="{{ url({'for': 'project_edit', 'query': '?id=' ~ result['project_id'] }) }}">
                    {{ result['project_name'] }}
                </a>
                &gt;
                <a href="{{ url({'for': 'block_edit', 'query': '?' ~ http_build_query({'project_id': result['project_id'], 'id': result['id']}) }) }}">
                    {{ result['name'] }}
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
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'apartment_add', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary float-right">
                Thêm
            </a>
            <div class="clearfix"></div>
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
                    {% if apartments is defined and apartments|length %}
                        {% for item in apartments['result'] %}
                            <tr>
                                <td>
                                    {{ item['id'] }}
                                </td>
                                <td>
                                    <a role="menuitem" href="{{ url({'for': 'apartment_edit', 'query': '?' ~ http_build_query({'block_id': item['block_id'], 'id': item['id'], 'page': page, 'from': 'list-by-block'})}) }}">
                                        {{ item['name'] }}
                                    </a>
                                </td>
                                <td>{{ item['floor_count'] }}</td>
                                <td>{{ item['block_name'] }}</td>
                                <td class="text-center">
                                    <a href="{{ url({'for': 'apartment_delete', 'query': '?' ~ http_build_query({'id': item['id'], 'from': 'list-by-block'})}) }}" onclick="javascript:return confirm('Đồng ý xoá.?');" class="btn btn-xs btn-bricky">
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