{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Bộ sưu tập nội thất</h3>
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
            <a href="{{ url({'for': 'apartment_add_gallery', 'query': '?' ~ http_build_query({'apartment_id': apartment['id']})}) }}" class="btn btn-primary float-right">
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
                        <th>Tên</th>
                        <th>Giá</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    {% if gallery is defined and gallery|length %}
                        {% for item in gallery['result'] %}
                            <tr>
                                <td>
                                    {{ item['id'] }}
                                </td>
                                <td>
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'apartment_edit_gallery', 'query': '?' ~ http_build_query({'id': item['id'], 'apartment_id': apartment['id']})}) }}">
                                        {{ item['name'] }}
                                    </a>
                                </td>
                                <td>{{ item['price'] }}</td>
                                <td class="text-center">
                                    <a href="{{ url({'for': 'apartment_delete_gallery', 'query': '?' ~ http_build_query({'id': item['id'], 'apartment_id': apartment['id']})}) }}" onclick="javascript:return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">
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