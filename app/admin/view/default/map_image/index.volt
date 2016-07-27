{% extends 'default.volt' %}

{% block content %}

    {% set jsonSelect = [] %}
    {% set jsonSelect[''] = 'Chọn liên kết' %}
    {% set optionSelect = '<option value="">Chọn liên kết</option>' %}
    {% for item in subObject %}
        {% set jsonSelect[item.id] = item.name %}
        {% set optionSelect = optionSelect ~ '<option value="'~ item.id ~'">'~ item.name ~'</option>' %}
    {% endfor %}
    {% include 'default/element/layout/breadcrumbs.volt' %}
    <h3>Vẽ map link</h3>
    <hr>
    <div class="wrap-map-image" style="position: relative" id="wrap-map-image">
        <div class="entry"></div>
    </div>
    <form action="" class="form-save-point hidden" id="form-save-point">
        <input class="from" type="text" name="point">
        <input type="text" name="item_id">
        <input type="text" name="map_image_id" value="{{ mapImage.id }}">
    </form>
    <br>
    <div class="text-right">
        <a href="" class="btn btn-danger add-map">Vẽ xong</a>
    </div>
    <hr>
    <h3>Chọn Map link</h3>
    <div class="table-list">
        <table id="main-result" class="table table-hover">
            <thead>
                <th>ID</th>
                <th>Liên kết MapLink</th>
                <th></th>
            </thead>

            <tbody id="body-table">
            {% if mapPoint is defined and mapPoint|length > 0 %}
                {% for point in mapPoint %}
                    <tr>
                        {% set _point = point.point|json_decode %}
                        <td>{{ point.id }}</td>
                        <td>
                            <form action="{{ url({'for': 'map_image_update'}) }}">
                                <input type="hidden" name="id" value='{{ point.id }}'>
                                <select onchange="formSubmit(this);" name="item_id" data-selected="{{ point.item_id }}" id="">
                                    {{ optionSelect }}
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="" onclick="return deleteMapImage({{ point.id }}, this);"  class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">
                                <i class="fa fa-times fa fa-white"></i>
                            </a>
                        </td>
                    </tr>
                {% endfor %}
            {% endif %}
            </tbody>
        </table>
    </div>
    <div class="block-init">
        {% if mapPoint is defined and mapPoint|length > 0 %}
            {% for point in mapPoint %}
                {% set _point = point.point|json_decode(true) %}
                <area data-item_id="{{ point.item_id }}" id="point_{{ point.id }}" data-maphilight='{{ _point['data-maphilight']|json_encode }}' shape="{{ _point['shape'] }}" coords="{{ _point['coords'] }}">
            {% endfor %}
        {% endif %}
    </div>

    <script>
        var linkMapImage = '{{ config.cdn.dir_upload ~ mapImage.image }}';
        var wrapImage = 'wrap-map-image';
        var form = '#form-save-point';
        var table = '#main-result';
        var select = '{{ jsonSelect|json_encode }}';
        var url = {
            'add' : '{{ url({'for': 'map_image_add'}) }}',
            'update': '{{ url({'for': 'map_image_update'}) }}',
            'delete' : '{{ url({'for': 'map_image_delete'}) }}'
        }
    </script>
    {{ javascript_include( config.application.base_url ~ 'asset/default/js/map/maphilight.js?' ~ config.asset.version) }}
    {{ javascript_include( config.application.base_url ~ "asset/js/create_map_image.js") }}
{% endblock %}
