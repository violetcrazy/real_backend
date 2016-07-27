{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thêm hình ảnh hiển thị</h3>
                
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
                    Thêm hình ảnh
                </span>
            </div>
        </div>
    </div>

    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-1 control-label">
                Hình ảnh
            </label>
            <div class="col-sm-11 block-upload-img-view">
                <div class="thumbnail-img">
                    <img src="{{ config.asset.backend_url ~ 'img/choose-940x400.gif?' ~ config.asset.version }}" alt="" />
                    <input type="file" class="hidden" accept="image/*" value="" />
                    <input type="hidden" class="input_image" name="image_view" value="" />
                    <span class="fa delete-img" style="display: none;"></span>
                </div>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'image_view'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-1 control-label">
                Kiểu
            </label>
            <div class="col-sm-4">
                {{ form.render('type', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-1 control-label">
                Tầng
            </label>
            <div class="col-sm-4">
                <select id="floor_count" class="form-control" name="floor">
                    {% set floor_name_list = result['floor_name_list']|json_decode(true) %}

                    {% if (floor_name_list|length) %}
                        {% for index, value in floor_name_list %}
                            <option value="{{ index }}">
                                {{ value }}
                            </option>
                        {% endfor %}
                    {% else %}
                        {% for value in 1..result['floor_count'] %}
                            <option value="{{ value }}">
                                {{ value }}
                            </option>
                        {% endfor %}
                    {% endif %}
                </select>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'block_id'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-1 control-label">
            </label>
            <div class="col-sm-11 text-right">
                <button type="submit" id="form-article-button" class="btn btn-bricky">Thêm</button>
            </div>
        </div>

        <input type="hidden" id="type_upload" name="type_upload" value="{{ constant('\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_MAP_IMAGE') }}" />
        <input type="hidden" id="load_upload_image_ajax" value="{{ url({"for": "load_upload_image_ajax"}) }}" />
        <input type="hidden" id="load_delete_image_ajax_url" value="{{ url({"for": "load_delete_image_ajax"}) }}" />
        <input type="hidden" id="asset_choose_image_url" value="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" />
    </form>
{% endblock %}

{% block bottom_js %}
    {#<script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/ajax_location.js?' ~ config.asset.version }}"></script>#}
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/block/upload_image.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true,
                maximumSelectionSize: 1
            });
        });
    </script>
{% endblock %}
