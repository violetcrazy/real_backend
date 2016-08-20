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
    {% set userSession = session.get('USER') %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}

            <div class="page-header">
                <h3>{{ result is defined ? 'Chỉnh sửa block' : 'Thêm Block' }}</h3>
            </div>
        </div>
    </div>

    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        <span class="area_mess" id="area_mess"></span>

        {{ flashSession.output() }}

        <div class="tabbable">
            {% set classInActive1 = '' %}
            {% set classInActive2 = '' %}
            {% set classInActive3 = '' %}

            <ul id="myTab" class="nav nav-tabs tab-bricky">
                {%
                if
                    in_array(userSession['membership'], {
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR')
                    })
                %}
                    {% set classActive   = 'active' %}
                    {% set classInActive1 = 'in active' %}

                    <li class="{{ classActive }}">
                        <a href="#panel_tab1" data-toggle="tab">Thông tin</a>
                    </li>
                    <li>
                        <a href="#panel_tab2" data-toggle="tab">Hình ảnh</a>
                    </li>
                    <li>
                        <a href="#panel_tab3" data-toggle="tab">Thuộc tính</a>
                    </li>
                {% endif %}

                {%
                if
                    in_array(userSession['membership'], {
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO')
                    })
                %}
                    {% set classActive    = '' %}
                    {% set classInActive2 = '' %}

                    {% if userSession['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO') %}
                        {% set classActive    = 'active' %}
                        {% set classInActive2 = 'in active' %}
                    {% endif %}

                    <li class="{{ classActive }}">
                        <a href="#panel_tab4" data-toggle="tab">SEO</a>
                    </li>
                {% endif %}

                {%
                if
                    in_array(userSession['membership'], {
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING')
                    })
                %}
                    {% set classActive    = '' %}
                    {% set classInActive3 = '' %}

                    {% if userSession['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING') %}
                        {% set classActive    = 'active' %}
                        {% set classInActive3 = 'in active' %}
                    {% endif %}

                    <li class="{{ classActive }}">
                        <a href="#panel_tab5" data-toggle="tab">Sale</a>
                    </li>
                {% endif %}
            </ul>

            <div class="tab-content">
                <div class="tab-pane {{ classInActive1 }}" id="panel_tab1">
                    {% include 'default/block/element/_form_tab1.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab2">
                    {% include 'default/block/element/_form_tab2.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab3">
                    {% include 'default/block/element/_form_tab3.volt' %}
                </div>
                <div class="tab-pane {{ classInActive2 }}" id="panel_tab4">
                    {% include 'default/block/element/_form_tab4.volt' %}
                </div>
                <div class="tab-pane {{ classInActive3 }}" id="panel_tab5">
                    {% include 'default/block/element/_form_tab5.volt' %}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12 text-right">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    <span class="clip-download"></span>
                    {{ result is defined ? 'Cật nhật' : 'Thêm Block' }}
                </button>

                {% if from == 'list-by-project' %}
                    <a href="{{ url({'for': 'block_list_by_project', 'query': '?' ~ http_build_query({'project_id': projectId})}) }}" class="btn btn-primary">
                        <span class="fa-mail-reply fa"></span>
                        Trở lại
                    </a>
                {% else %}
                    <a href="{{ url({'for': 'block_list'}) }}" class="btn btn-primary">
                        <span class="fa-mail-reply fa"></span>
                        Trở lại
                    </a>
                {% endif %}

                {% if result is defined %}
                    <a target="_blank" class="btn btn-warning" href="{{ url({'for': 'apartment_list_by_block', 'query': '?' ~ http_build_query({'block_id': result.id})}) }}">
                        Danh sách căn hộ
                    </a>
                {% endif %}
            </div>
        </div>
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
    <script type="text/javascript">
        $(document).ready(function () {
            var data_bind = false;
            var $modal = $('#edit_floor_name_list_index');

            $('#save_edit').on('click', function () {
                if (data_bind) {
                    var data = {};

                    $modal.find('.item-data').each(function () {
                        var $this = $(this).find('input');
                        data[$this.attr('data-index')] = $.trim($this.val());
                    });

                    $('#' + data_bind).val(JSON.stringify(data));
                }
            });

            $('#floor_count, #apartment_count').on('change', function (event) {
                var _data_bind = $(this).data('bind');
                var _type      = $(this).data('name');
                var _mess      = "";

                $('#' + _data_bind).val("");

                if (_type == 'floor') {
                    _mess = "Số tầng đã thay đổi, tên tầng sẽ trả về mặc định, bấm SỬA TÊN TẦNG nếu muốn";
                }

                if (_type == 'index') {
                    _mess = "Số thứ tự sản phẩm đã thay đổi, tên thứ tự sẽ trả về mặc định, bấm SỬA TÊN VỊ TRÍ nếu muốn";
                }

                $('#area_mess').html('<div class="alert alert-warning">' + _mess + '</div>');
            });

            $('.popup-edit-name').on('click', function () {
                $modal.modal();
                data_bind = false;

                var val  = parseInt($(this).prev().val());
                var type = $(this).data('name');
                var text = {
                    "floor": "Tầng",
                    "index": ""
                };

                data_bind = $(this).data('bind');
                var data_loop = false;

                if (data_bind) {
                    var _json = $('#' + data_bind).val();
                    if (_json) {
                        data_loop = JSON.parse(_json);
                    }
                }

                $modal.find('.main-input').html('');

                if (data_loop) {
                    var html = '';
                    var i    = 1;

                    $.each(data_loop, function (index, value) {
                        if (i > val) {
                            return false;
                        }

                        i ++;
                        html += '<div class="col-sm-3 item-data">\
                                <input class="m-t-10" data-index="' + index + '" value="' + value + '" type="text" class="form-control" />\
                            </div>';
                    });
                    $modal.find('.main-input').html(html);
                } else if (val) {
                    var html = '';

                    for (i = 1 ; i <= val; i++) {
                        var _val = text[type] + ' ' + i;

                        if (i == 1 && type == "floor") {
                            _val = text[type] + ' trệt';
                        }

                        html += '<div class="col-sm-3 item-data">\
                                <input class="m-t-10" data-index="' + i + '" value="' + _val + '" type="text" class="form-control" />\
                            </div>';
                    }
                    $modal.find('.main-input').html(html);
                } else {
                    $modal.find('.main-input').html('<div class="col-xs-12"><div class=" alert alert-danger">Vui lòng chọn số lượng, hoặc số lượng nhập vào phải là số</div></div>');
                }
            });

            var idBlock = '{{ result is defined ? result.id : '0' }}';

            if (typeof(Storage) !== "undefined") {
                $('.nav-tabs a').click(function (event) {
                    event.preventDefault();
                    var href     = $(this).attr('href');
                    var dataSave = JSON.stringify({id: idBlock, href: href});

                    localStorage.setItem('tab_block', dataSave);
                });

                var active = localStorage.getItem('tab_block');

                if (typeof active != 'undefined' && active != '') {
                    active = JSON.parse(active);

                    if (active.id == idBlock) {
                        $('[href="' + active.href + '"]').trigger('click');
                    }
                }
            } else {
                console.log('Sorry! No Web Storage support.');
            }

            $('.search-select').select2({
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

            $('#attribute_utility').tagator({
                autocomplete: {{ data_attribute_utility }},
                showAllOptionsOnFocus: true
            });
        });
    </script>
{% endblock %}
