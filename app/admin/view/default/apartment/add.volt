{% extends 'default.volt' %}

{% block content %}
    {% include 'default/element/macro/_upload.volt' %}

    {% set apartment_name_list = blocks['apartment_name_list']|json_decode(true) %}
    {% set floor_name_list = blocks['floor_name_list']|json_decode(true) %}

    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thêm sản phẩm</h3>

                <a target="_blank" href="{{ url({'for': 'block_edit'}) }}?id={{ blocks['id'] }}">Block:
                    {{ blocks['name'] }}
                </a>
            </div>
        </div>
    </div>
    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <script type="text/javascript">var shortNameBlock = '{{ blocks['shortname'] }}';</script>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tầng
            </label>
            <div class="col-sm-3">
                <select id="floor_count" class="form-control" name="floor">
                    {% if (floor_name_list|length) %}
                        {% for index, value in floor_name_list %}
                            <option value="{{ index }}">{{ value }}</option>
                        {% endfor %}
                    {% else %}
                        {% for value in 1..blocks['floor'] %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endfor %}
                    {% endif %}

                </select>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'floor_count'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Thứ tự sản phẩm
            </label>
            <div class="col-sm-3">
                <select id="ordering" class="form-control" name="ordering">
                    {% if (apartment_name_list|length) %}
                        {% for index, value in apartment_name_list %}
                            <option value="{{ index }}">{{ value }}</option>
                        {% endfor %}
                    {% else %}
                        {% for value in 1..blocks['apartment_count'] %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endfor %}
                    {% endif %}
                </select>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'ordering'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Số phòng ngủ
            </label>
            <div class="col-sm-3">
                {{ form.render('bedroom_count', {'class': 'form-control format-number'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Số phòng tắm
            </label>
            <div class="col-sm-3">
                {{ form.render('bathroom_count', {'class': 'form-control format-number'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tên sản phẩm <span class="symbol required"></span>
            </label>
            <div class="col-sm-5">
                <div class="input-group">
                    {{ form.render('name', {'class': 'form-control','readonly': 'readonly'}) }}
                    <span class="input-group-btn">
                        <button class="btn btn-edit btn-warning edit-name" type="button" id="edit-name">Sửa</button>
                    </span>
                </div>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Ảnh đại diện
            </label>
            <div class="col-sm-5">
                {{ templateUpload('default_image') }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Người quản lý
            </label>
            <div class="col-sm-3">
                {{ form.render('user_id', {'class': 'form-control', 'disabled': 'disabled'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'user_id'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tình trạng
            </label>
            <div class="col-sm-3">
                {{ form.render('condition', {'class': 'form-control', 'disabled': 'disabled'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'condition'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Loại
            </label>
            <div class="col-sm-3">
                {{ form.render('type', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'type'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Giá bán
            </label>
            <div class="col-sm-3">
                {{ form.render('price', {'class': 'form-control format-number'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Giá khuyến mãi
            </label>
            <div class="col-sm-3">
                {{ form.render('price_sale_off', {'class': 'form-control format-number'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price_sale_off'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Ảnh vị trí
            </label>
            <div class="col-sm-5">
                {{ templateUpload('position_image') }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Mô tả vị trí
            </label>
            <div class="col-sm-8 block-upload-img">
                {{ form.render('position', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Ảnh toàn cảnh
            </label>
            <div class="col-sm-5">
                {{ templateUpload('panorama_image') }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Gallery hình ảnh
            </label>
            <div class="col-sm-8">
                <button class="btn btn-primary btn-upload-multiple" data-sendValue="gallery">Upload</button>
                <div class="clearfix"></div>
                <div class="box-show-gallery preview_gallery preiview_upload_multiple"> </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Nội dung
            </label>
            <div class="col-sm-8">
                {{ form.render('description', {'class': 'form-control', 'style': 'height: 300px'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'description'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Kiểu sản phẩm
            </label>
            <div class="col-sm-8">
                <input id="property_type" class="form-control" type="text" value="" name="property_type" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Hệ thống an ninh
            </label>
            <div class="col-sm-8">
                <input id="security_control_system" name="security_control_system" class="form-control" type="text" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Hệ thống điều khiển tiết kiệm điện
            </label>
            <div class="col-sm-8">
                <input id="energy_control_system" name="energy_control_system" class="form-control" type="text" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Hệ thống giải trí âm nhạc
            </label>
            <div class="col-sm-8">
                <input id="entertaining_control_system" name="entertaining_control_system" class="form-control" type="text" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Hệ thống kiểm soát môi trường
            </label>
            <div class="col-sm-8">
                <input id="environment_control_system" name="environment_control_system" class="form-control" type="text" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                View
            </label>
            <div class="col-sm-8">
                <input id="property_view" class="form-control" type="text" value="" name="property_view">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Hướng nhìn
            </label>
            <div class="col-sm-3">
                {{ form.render('direction', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tiện ích
            </label>
            <div class="col-sm-8">
                <input id="property_utility" name="property_utility" class="form-control" type="text" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Diện tích sản phẩm
            </label>
            <div class="col-sm-3">
                {{ form.render('total_area', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Diện tích vườn
            </label>
            <div class="col-sm-3">
                {{ form.render('green_area', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Hoa hồng
            </label>
            <div class="col-sm-3">
                {{ form.render('rose', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Người lớn
            </label>
            <div class="col-sm-3">
                {{ form.render('adults_count', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Trẻ em
            </label>
            <div class="col-sm-3">
                {{ form.render('children_count', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Trạng thái
            </label>
            <div class="col-sm-3">
                {{ form.render('status', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-8">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    Thêm
                </button>
            </div>
        </div>
        <input type="hidden" id="type_upload" name="type_upload" value="{{ constant('\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_DEFAULT') }}"/>
        <input type="hidden" id="load_upload_image_ajax" value="{{ url({"for": "load_upload_image_ajax"}) }}"/>
        <input type="hidden" id="load_delete_image_ajax_url" value="{{ url({"for": "load_delete_image_ajax"}) }}"/>
        <input type="hidden" id="asset_choose_image_url" value="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}"/>
    </form>
{% endblock %}

{% block bottom_js %}
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/dropzone/downloads/css/dropzone.css?' ~ config.asset.version }}">
    {#<script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/ajax_location.js?' ~ config.asset.version }}"></script>#}
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/project/upload_image.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/dropzone/downloads/dropzone.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        function autoNamingApartment($) {
            var floor = $.trim($('#floor_count').val());
            var apartment = $.trim($('#ordering').val());

            if (floor < 10) {
                floor = '0' + floor;
            }
            if (apartment < 10) {
                apartment = '0' + apartment;
            }

            var name = shortNameBlock + '-' + floor + '-' + apartment;
            $('#name').val(name);
        }


        $(document).ready(function () {

            autoNamingApartment($);

            $('#ordering, #floor_count').on('change', function () {
                autoNamingApartment($);
            });

            $('#edit-name').on('click', function (event) {
                event.preventDefault();
                var attr = $('#name').attr('readonly');
                if (attr == "readonly") {
                    $('#name').attr('readonly', false);
                } else {
                    $('#name').attr('readonly', 'readonly');
                }
            });

            $('.search-select').select2({
                allowClear: true
            });

            $('#property_type').tagator({
                autocomplete: {{ data_attribute_type }}
            });

            $('#property_type_eng').tagator({
                autocomplete: {{ data_attribute_type_eng }}
            });

            $('#property_view').tagator({
                autocomplete: {{ data_attribute_view }}
            });

            $('#property_view_eng').tagator({
                autocomplete: {{ data_attribute_view_eng }}
            });

            $('#property_utility').tagator({
                autocomplete: {{ data_attribute_utility }}
            });

            $('#property_utility_eng').tagator({
                autocomplete: {{ data_attribute_utility_eng }}
            });

            $('#room_type').tagator({
                autocomplete: {{ data_attribute_room_type }}
            });

            $('#best_for').tagator({
                autocomplete: {{ data_attribute_best_for }}
            });

            $('#best_for_eng').tagator({
                autocomplete: {{ data_attribute_best_for_eng }}
            });

            $('#suitable_for').tagator({
                autocomplete: {{ data_attribute_suitable_for }}
            });

            $('#suitable_for_eng').tagator({
                autocomplete: {{ data_attribute_suitable_for_eng }}
            });

            $('.dz-clickable').dropzone({
                url: '{{ url({"for": "load_upload_image_ajax", "query": "?" ~ http_build_query({"type_upload": constant("\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_GALLERY")})}) }}',
                dictRemoveFile: '',
                addRemoveLinks: true,
                FileName: false,
                previewsContainer: ".box-show-gallery",
                init: function () {
                    this.on('success', function (file, response) {
                        if (typeof response != 'undefined' && file.processing == true) {
                            if (response.status != 200) {
                                alert(response.message);
                                return false;
                            }

                            $('.dz-success').append('<input type="hidden" class="gallery" name="gallery[]" value="' + response.blocks.image + '" />');
                        } else {
                            alert('An unknown error occurred, please try again.');
                        }
                    });

                    this.on("removedfile", function () {
                        var image = $('.gallery').find('.input_image').val();
                        var folder = $('#folder_upload').val();
                        $.ajax({
                            'url': $('#load_delete_image_ajax_url').val(),
                            'type': 'GET',
                            'data': {'image': image, 'folder': folder},
                            'success': function (response) {
                                if (typeof response != 'undefined') {
                                    if (response.status != 200) {
                                        alert(response.message);
                                        return false;
                                    }

                                    $('.gallery_new').remove();
                                } else {
                                    alert('An unknown error occurred, please try again.');
                                }
                            }
                        });
                    });
                }
            });
            Dropzone.autoDiscover = false;
        });
    </script>
{% endblock %}
