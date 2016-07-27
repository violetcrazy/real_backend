{% extends 'default.volt' %}
{% block top_css %}
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'css/bootstrap-modal-bs3patch.css?' ~ config.asset.version }}" />
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'css/bootstrap-modal.css?' ~ config.asset.version }}" />
{% endblock %}

{% block top_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/bootstrap-modal.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/bootstrap-modalmanager.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/ui-modals.js?' ~ config.asset.version }}"></script>
{% endblock %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Thêm block</h3>

                <a href="{{ url({'for': 'project_edit'}) }}?id={{ project['id'] }}" target="_blank">
                    {{ project['name'] }}
                </a>
                &gt;
                <a href="{{ url({'for': 'block_list', 'query': '?project_id=' ~ project['id'] }) }}">
                    Danh sách Block
                </a>
            </div>
        </div>
    </div>
    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        <span class="area_mess" id="area_mess"></span>
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tên block <span class="symbol required"></span>
            </label>

            <div class="col-sm-8">
                {{ form.render('name', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Shortname
            </label>

            <div class="col-sm-3">
                {{ form.render('shortname', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'shortname'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Số tầng
            </label>
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-btn">
                        {{ form.render('floor_count', {'class': 'form-control', 'data-name': 'floor', 'data-bind': 'floor_name_list'}) }}
                        <button type="button" data-name="floor" data-bind="floor_name_list" class="btn btn-primary popup-edit-name">
                            Sửa tên tầng
                        </button>
                        <input type="hidden" name="floor_name_list" id="floor_name_list" />
                    </span>
                </div>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'floor_count'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Số sản phẩm mỗi tầng
            </label>
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-btn">
                        {{ form.render('apartment_count', {'class': 'form-control format-number2', 'data-name': 'index', 'data-bind': 'apartment_name_list'}) }}
                        <button type="button" data-name="index" data-bind="apartment_name_list" class="btn btn-primary popup-edit-name">
                            Sửa tên vị trí
                        </button>
                        <input type="hidden" name="apartment_name_list"  id="apartment_name_list" />
                    </span>
                </div>
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'apartment_count'} %}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Giá tổng quan
            </label>

            <div class="col-sm-3">
                {{ form.render('price', {'class': 'form-control format-number'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'price'} %}
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
                Chính sách
            </label>
            <div class="col-sm-8">
                {{ form.render('policy', {'class': 'form-control', 'style': 'height: 300px'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'policy'} %}
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
                Gallery hình ảnh
            </label>
            <div class="col-sm-8">
                <button class="btn btn-primary btn-upload-multiple" data-sendValue="gallery" >Upload</button>
                <div class="clearfix"></div>
                <div class="box-show-gallery preview_gallery preiview_upload_multiple">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">
                Kiểu
            </label>
            <div class="col-sm-8">
                <input id="property_type" class="form-control" type="text" value="" name="property_type">
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
            <div class="col-sm-5">
                {{ form.render('direction', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tiện ích
            </label>
            <div class="col-sm-8">
                <input id="property_utility" name="property_utility" class="form-control" type="text" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Tổng diện tích
            </label>
            <div class="col-sm-3">
                {{ form.render('area', {'class': 'form-control'}) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                Diện tích cây xanh
            </label>

            <div class="col-sm-3">
                {{ form.render('space', {'class': 'form-control'}) }}
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
            <label class="col-sm-3 control-label">
            </label>
            <div class="col-sm-8">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    Thêm
                </button>
            </div>
        </div>
        <input type="hidden" id="type_upload" name="type_upload" value="{{ constant('\ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_DEFAULT') }}"/>
        <input type="hidden" id="load_upload_image_ajax" value="{{ url({"for": "load_upload_image_ajax"}) }}"/>
        <input type="hidden" id="load_delete_image_ajax_url" value="{{ url({"for": "load_delete_image_ajax"}) }}"/>
        <input type="hidden" id="asset_choose_image_url" value="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}"/>
    </form>

    <div class="modal fade" tabindex="-1" role="dialog" id="edit_floor_name_list_index">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <div><b>Chỉnh sửa tên gọi</b></div>
        </div>
        <div class="modal-body">
            <div class="row main-input"></div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="save_edit">Lưu</button>
            <button type="button" class="btn" data-dismiss="modal">Hủy</button>
        </div>
    </div>
{% endblock %}

{% block bottom_js %}
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/dropzone/downloads/css/dropzone.css?' ~ config.asset.version }}" />
    {#<script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/ajax_location.js?' ~ config.asset.version }}"></script>#}
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/project/upload_image.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/dropzone/downloads/dropzone.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var data_bind = false;
            var $modal = $('#edit_floor_name_list_index');

            $('#save_edit').on('click', function() {
                if (data_bind) {
                    var data = {};
                    $modal.find('.item-data').each(function() {
                        $this = $(this).find('input');
                        data[$this.attr('data-index')] = $.trim($this.val());
                    });

                    $('#' + data_bind).val( JSON.stringify(data) );
                }
            });

            $('#floor_count, #apartment_count').on('change', function(event){
                var _data_bind = $(this).data('bind');
                var _type = $(this).data('name');
                var _mess = "";
                console.log(_type);
                $('#' + _data_bind).val("");
                if (_type == 'floor') {
                    _mess = "Số tầng đã thay đổi, tên tầng sẽ trả về mặc định, bấm SỬA TÊN TẦNG nếu muốn";
                }
                if (_type == 'index') {
                    _mess = "Số thứ tự sản phẩm đã thay đổi, tên thứ tự sẽ trả về mặc định, bấm SỬA TÊN VỊ TRÍ nếu muốn";
                }

                $('#area_mess').html('<div class="alert alert-warning">'+ _mess +'</div>');
            });

            $('.popup-edit-name').on('click', function() {
                $modal.modal();
                data_bind = false;

                var val = parseInt($(this).prev().val());
                var type = $(this).data('name');
                var text = {
                    "floor": "Tầng",
                    "index": ""
                };
                data_bind = $(this).data('bind');

                $modal.find('.main-input').html('');
                if (val) {
                    var html = '';
                    for (i = 1 ; i <= val; i++) {
                        var _val = text[type] + ' ' + i;

                        if (i == 1 && type == "floor") {
                            _val = text[type] +' trệt';
                        }

                        html += '<div class="col-md-3 item-data">\
                                <input class="m-t-10" data-index="' + i + '" value="'+ _val +'" type="text" class="form-control" />\
                            </div>';
                    }
                    $modal.find('.main-input').html(html);
                } else {
                    $modal.find('.main-input').html('<div class="col-xs-12"><div class=" alert alert-danger">Vui lòng chọn số lượng, hoặc số lượng nhập vào phải là số</div></div>');
                }
            });

            $('.attribute-select').select2({
                allowClear: true
            });

            $('#attribute_type').tagator({
                autocomplete: {{ data_attribute_type }},
                showAllOptionsOnFocus: true
            });

            $('#attribute_view').tagator({
                autocomplete: {{ data_attribute_view }},
                showAllOptionsOnFocus: true
            });

            $('#property_utility').tagator({
                autocomplete: {{ data_attribute_utility }},
                showAllOptionsOnFocus: true
            });

        });
    </script>
{% endblock %}
