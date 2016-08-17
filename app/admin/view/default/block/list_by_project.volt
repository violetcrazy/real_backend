{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Danh sách Block ({{ project['name'] }})</h3>

                <a href="{{ url({'for': 'project_edit'}) }}?id={{ project['id'] }}">
                    {{ project['name'] }}
                </a>
                &gt;
                <span>Danh sách Block<span>
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
            <a href="{{ url({'for': 'block_add', 'query': '?' ~ http_build_query({'project_id': project['id']})}) }}" class="btn btn-primary float-right">
                Thêm
            </a>
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th>Block</th>
                        <th width="10%" >Số tầng</th>
                        <th width="10%">Sản phẩm</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    {% if blocks is defined and blocks|length %}
                        {% for item in blocks['result'] %}
                            <tr>
                                <td>
                                    {{ item['id'] }}
                                </td>
                                <td>
                                    <a role="menuitem" href="{{ url({'for': 'block_edit', 'query': '?' ~ http_build_query({'project_id': item['project_id'], 'id': item['id'], 'page': page, 'from': 'list-by-project'})}) }}">
                                        {{ item['name'] }}
                                    </a>
                                </td>
                                <td>{{ item['floor_count'] }}</td>
                                <td>
                                    <a href="{{ url({'for': 'apartment_list_by_block', 'query': '?' ~ http_build_query({'block_id': item['id']})}) }}">
                                        Danh sách
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">
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