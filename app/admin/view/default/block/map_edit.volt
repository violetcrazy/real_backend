{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Chỉnh sửa bản đồ</h3>

                <a href="{{ url({'for': 'project_edit', 'query': '?id=' ~ result['project_id'] }) }}">
                    {{ result['project_name'] }}
                </a>
                &gt;
                <a href="{{ url({'for': 'block_edit', 'query': '?' ~ http_build_query({'project_id': result['project_id'], 'id': result['id']}) }) }}">
                    {{ result['name'] }}
                </a>
                &gt;
                <a href="{{ url({'for': 'block_list_map_image', 'query': '?block_id=' ~ result['id']}) }}">
                    Quản lý hình ảnh
                </a>
                &gt;
                <span>
                    {{ apartment['name'] }}
                </span>
            </div>
        </div>
    </div>

    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-1 control-label">
            </label>
            <div class="col-sm-11 box-map" style="width: 940px">
                <div class="block-map">
                    <img src="{{ config.asset.frontend_url ~ 'upload/block/' ~ mapImage['image'] }}" alt="" class="map" usemap="#blockmap" />
                    <map id="map-tag" name="blockmap">
                        {% if viewMap is defined %}
                            <area coords="{{ viewMap['coords']}}" shape="{{ viewMap['shape']}}" data-maphilight='{{ viewMap['data-maphilight']}}'>
                        {% endif %}
                    </map>
                </div>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'image_view'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-1 control-label">
            </label>
            <div class="col-sm-11 text-right">
                <div class="pull-left">
                    <a href="" class="btn btn-danger reset-map">
                        <span class="glyphicon glyphicon-repeat"></span> Vẽ lại
                    </a>
                    <input id="image_view" value='{{ map.map }}' class="form-control" type="hidden" name="image_view">
                    <button type="submit" id="form-article-button" class="btn btn-bricky">
                        Cập nhật
                    </button>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{ url({'for': 'block_map_add', 'query': '?' ~ http_build_query({'map_image_id': mapImage['id'], 'block_id': result['id'], 'floor_count': mapImage['floor'] })}) }}">
                        Vẽ sản phẩm khác
                    </a>
                    <a href="{{ url({'for': 'block_map_add', 'query': '?' ~ http_build_query({'map_image_id': request.getQuery('map_image_id'), 'block_id': request.getQuery('block_id'), 'floor_count': request.getQuery('floor_count')} )}) }}" class="btn btn-primary">
                        DS căn hộ đã vẽ
                    </a>
                    <a href="{{ url({'for': 'block_list_map_image', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary">
                        DS tầng trong block
                    </a>
                </div>
            </div>
        </div>
    </form>

    {% if listMapView is defined and listMapView|length %}
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Sản phẩm đã vẽ</h3>
            </div>
        </div>
        <div class="col-sm-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                            #
                        </th>
                        <th>Sản phẩm</th>
                        <th width="5%">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {% for item in listMapView %}
                        <tr>
                            <td>
                                {{ item['apartment_id'] }}
                            </td>
                            <td>
                                <a href="{{ url({'for': 'block_map_edit', 'query': '?' ~ http_build_query({'id':item['id'], 'map_image_id': item['image_map_id'], 'block_id': result['id'], 'floor_count': item['map_image_floor']})}) }}">
                                    {{ item['apartment_name'] }}
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">
                                    <i class="fa fa-times fa fa-white"></i>
                                </a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>
    {% endif %}
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true,
                maximumSelectionSize: 1
            });
        });
        var paint = true;
    </script>
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/map/migrate.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/map/maphilight.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/map/image_view_map.js?' ~ config.asset.version }}"></script>
{% endblock %}