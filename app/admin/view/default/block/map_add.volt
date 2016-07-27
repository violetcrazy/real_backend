{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thêm bản đồ</h3>

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
                    Tầng {{ request.getQuery('floor_count') }}
                </span>
            </div>
        </div>
    </div>

    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-1 control-label"></label>
            <div class="col-sm-11 box-map">
                <div class="block-map {{ apartments is defined and apartments|length ? 'can-paint' : '' }}" style="width: 940px">
                    <img src="{{ config.asset.frontend_url ~ 'upload/block/' ~ mapImage['image'] }}" alt="" class="map" usemap="#blockmap" />
                    {% if listMapView is defined and listMapView|length %}
                        <map id="map-tag" name="blockmap">
                        {% for item in listMapView %}
                            <area title="{{ item['apartment_name'] }}" coords="{{ item['view_map']['coords']}}" shape="{{ item['view_map']['shape']}}" data-maphilight='{{ item['view_map']['data-maphilight']}}'>
                        {% endfor %}
                        </map>
                    {% else %}
                        <map id="map-tag" name="blockmap" />
                    {% endif %}
                </div>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'image_view'} %}
            </div>
        </div>
        {% if apartments is defined and apartments|length %}
            <div class="form-group">
                <label class="col-sm-1 control-label">
                </label>
                <div class="col-sm-4">
                    <a href="" class="btn btn-danger btn-sm reset-map m-t-10"><span class="glyphicon glyphicon-repeat"></span> Vẽ lại</a>
                    <input id="image_view" value='' class="form-control" type="hidden" name="image_view">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-1 control-label">
                    Sản phẩm
                </label>
                <div class="col-sm-4">
                    <select id="apartment_id" class="form-control" name="apartment_id">
                        {% for item in apartments %}
                            <option value="{{ item['id'] }}">{{ item['name'] }}</option>
                        {% endfor %}
                    </select>
                    {% include 'default/element/layout/form_message' with {'form': form, 'element': 'apartment_id'} %}
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-1 control-label">
                </label>
                <div class="col-sm-11 text-right">
                    <button type="submit" id="form-article-button" class="btn btn-bricky">
                        Thêm
                    </button>
                    <a href="{{ url({'for': 'block_list_map_image', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary">
                        Trở lại
                    </a>
                </div>
            </div>

            <script>var paint = true;</script>
        {% else %}
            {% if listMapView is defined and listMapView|length %}
                <div class="form-group">
                    <label class="col-sm-1 control-label">
                    </label>
                    <div class="col-sm-11 text-right">
                        <a href="{{ url({'for': 'apartment_add', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary">
                            Tạo thêm sản phẩm
                        </a>
                        <a href="{{ url({'for': 'block_list_map_image', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary">
                            Trở lại
                        </a>
                    </div>
                </div>
            {% else %}
                <div class="form-group">
                    <label class="col-sm-1 control-label">
                    </label>
                    <div class="col-sm-11 text-right">
                        <a href="{{ url({'for': 'apartment_add', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary">
                            Tạo sản phẩm
                        </a>
                        <a href="{{ url({'for': 'block_list_map_image', 'query': '?' ~ http_build_query({'block_id': result['id']})}) }}" class="btn btn-primary">
                            Trở lại
                        </a>
                    </div>
                </div>
            {% endif %}
        {% endif %}
    </form>

    {% if listMapView is defined and listMapView|length %}
        <div class="col-sm-12 col-0">
            <div class="page-header">
                <h3>Sản phẩm đã vẽ</h3>
            </div>
        </div>
        <div class="col-sm-12 col-0">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                            #
                        </th>
                        <th>Sản phẩm</th>
                        <th width="5%"></th>
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
    </script>
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/map/migrate.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/map/maphilight.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/map/image_view_map.js?' ~ config.asset.version }}"></script>
{% endblock %}