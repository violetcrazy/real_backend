{% extends 'default.volt' %}

{% block content %}
    {% set viewMap = getMapView() %}

    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Hình ảnh hiển thị</h3>

                <a href="{{ url({'for': 'project_edit', 'query': '?id=' ~ result['project_id'] }) }}">
                    {{ result['project_name'] }}
                </a>
                &gt;
                <a href="{{ url({'for': 'block_edit', 'query': '?' ~ http_build_query({'project_id': result['project_id'], 'id': result['id']}) }) }}">
                    {{ result['name'] }}
                </a>
                &gt;
                <span>
                    Quản lý hình ảnh
                </span>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <div>
                <p><b>Tổng cộng: </b> {{ result['floor_count'] }} Tầng</p>
            </div>
            <a href="{{ url({'for': 'block_map_image_add', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary float-right">
                Thêm
            </a>
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        {#<th width="10%">Xem bản đồ</th>#}
                        <th width="10%">Kiểu hiển thị</th>
                        <th width="10%">Tầng</th>
                        <th width="15%"></th>
                    </tr>
                </thead>
                <tbody>
                    {% if mapImages is defined and mapImages|length %}
                        {% for item in mapImages['result'] %}
                            <tr>
                                <td>
                                    {{ item['id'] }}
                                </td>
                                <td>
                                    <img src="{{ config.asset.frontend_url ~ 'upload/block/thumbnail/' ~ item['image'] }}" width="200px" alt="" />
                                </td>
                                {#
                                <td>
                                    <a class="btn btn-xs btn-green tooltips" data-original-title="Xem bản đồ" data-placement="top" href="#">
                                        <i class="fa fa-share"></i>
                                    </a>
                                </td>
                                #}
                                <td>
                                    {% if viewMap[item['type']] is defined %}
                                        {{ viewMap[item['type']] }}
                                    {% endif %}
                                </td>
                                <td>
                                    {{ item['floor'] }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ url({'for': 'block_map_add', 'query': '?' ~ http_build_query({'map_image_id': item['id'], 'block_id': item['item_id'], 'floor_count': item['floor']})}) }}" class="btn btn-xs btn-teal tooltips" data-original-title="Vẽ bản đồ" data-placement="top">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    {% if item['type'] == constant('\ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_MAP_VIEW') %}
                                        <a href="{{ url({'for': 'block_map_image_clone', 'query': '?' ~ http_build_query({'map_image_id': item['id'], 'block_id': item['item_id'], 'floor_count': item['floor']})}) }}" class="btn btn-xs btn-teal tooltips" data-original-title="Sao chép" data-placement="top">
                                            <i class="clip-copy-2"></i>
                                        </a>
                                    {% endif %}

                                    <a href="{{ url({'for': 'block_map_image_edit', 'query': '?' ~ http_build_query({'id': item['id'], 'block_id': item['item_id'], 'floor_count': item['floor']})}) }}" class="btn btn-xs btn-teal tooltips" data-original-title="Sửa hình ảnh" data-placement="top">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="{{ url({'for': 'block_map_image_delete', 'query': '?' ~ http_build_query({'map_image_id': item['id'], 'block_id': item['item_id']})}) }}" onclick="javascript:return confirm('Đồng ý xóa?');" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">
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