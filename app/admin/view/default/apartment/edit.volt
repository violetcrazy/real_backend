{% extends 'default.volt' %}

{% block content %}
    {% set apartment_name_list = [] %}
    {% set floor_name_list = [] %}

    {% if blocks.id is defined %}
        {% set apartment_name_list = blocks.apartment_name_list|json_decode(true) %}
        {% set floor_name_list = blocks.floor_name_list|json_decode(true) %}
    {% endif %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}

            <div class="page-header">
                <h3>{{ apartment is defined ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm' }}</h3>
            </div>
        </div>
    </div>

    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <script type="text/javascript">
            var shortNameBlock = '{{ blocks.shortname is defined ? blocks.shortname : ''}}';
        </script>

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
                        <a href="#panel_tab2" data-toggle="tab">Mô tả</a>
                    </li>
                    <li>
                        <a href="#panel_tab3" data-toggle="tab">Hình ảnh</a>
                    </li>
                    <li>
                        <a href="#panel_tab4" data-toggle="tab">Thuộc tính</a>
                    </li>
                    <!-- <li>
                        <a href="#panel_tab6" data-toggle="tab">Nội thất</a>
                    </li> -->
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
                        <a href="#panel_tab5" data-toggle="tab">SEO</a>
                    </li>
                {% endif %}

                {%
                if
                    in_array(userSession['membership'], {
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN'),
                        constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SALE')
                    })
                %}
                    {% set classActive    = '' %}
                    {% set classInActive3 = '' %}

                    {% if userSession['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SALE') %}
                        {% set classActive    = 'active' %}
                        {% set classInActive3 = 'in active' %}
                    {% endif %}

                    <li class="{{ classActive }}">
                        <a href="#panel_tab7" data-toggle="tab">Sale</a>
                    </li>
                {% endif %}
            </ul>

            <div class="tab-content">
                <div class="tab-pane {{ classInActive1 }}" id="panel_tab1">
                    {% include 'default/apartment/element/_form_tab1.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab2">
                    {% include 'default/apartment/element/_form_tab2.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab3">
                    {% include 'default/apartment/element/_form_tab3.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab4">
                    {% include 'default/apartment/element/_form_tab4.volt' %}
                </div>
                <div class="tab-pane {{ classInActive2 }}" id="panel_tab5">
                    {% include 'default/apartment/element/_form_tab5.volt' %}
                </div>
                {#
                <div class="tab-pane" id="panel_tab6">
                    {% include 'default/apartment/element/_form_tab6.volt' %}
                </div>
                #}
                <div class="tab-pane {{ classInActive3 }}" id="panel_tab7">
                    {% include 'default/apartment/element/_form_tab7.volt' %}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 text-right">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    <span class="clip-download"></span>
                    {{ apartment is defined ? 'Cật nhật' : 'Thêm sản phẩm' }}
                </button>
                {% if from == 'list-by-block' %}
                    <a href="{{ url({'for': 'apartment_list_by_block', 'query': '?' ~ http_build_query({'block_id': blocks.id})}) }}" class="btn btn-primary">
                        <span class="fa-mail-reply fa"></span>
                        Trở lại
                    </a>
                {% elseif from == 'list-by-project' %}
                    <a href="{{ url({'for': 'apartment_list_by_project', 'query': '?' ~ http_build_query({'project_id': projectId})}) }}" class="btn btn-primary">
                        <span class="fa-mail-reply fa"></span>
                        Trở lại
                    </a>
                {% else %}
                    <a href="{{ url({'for': 'apartment_list'}) }}" class="btn btn-primary">
                        <span class="fa-mail-reply fa"></span>
                        Trở lại
                    </a>
                {% endif %}
            </div>
        </div>
    </form>
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript">
        $(document).ready(function () {
            $('#ordering, #floor_count').on('change', function () {
                var floor     = $.trim($('#floor_count').val());
                var apartment = $.trim($('#ordering').val());

                if (floor < 10) {
                    floor = '0' + floor;
                }

                if (apartment < 10) {
                    apartment = '0' + apartment;
                }

                var name = shortNameBlock + '-' + floor + '-' + apartment;
                $('#name').val(name);
                $('#name_eng').val(name);
            });

            if ($('#name').val() == '' || typeof $('#name').val() == 'undefined') {
                var floor     = $.trim($('#floor_count').val());
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

            $('.edit-name').on('click', function (event) {
                event.preventDefault();

                var attr = $(this).closest('.input-group').find('input[type="text"]').attr('readonly');

                if (attr == "readonly") {
                    $(this).closest('.input-group').find('input[type="text"]').attr('readonly', false);
                } else {
                    $(this).closest('.input-group').find('input[type="text"]').attr('readonly', 'readonly');
                }
            });

            var idApartment = '{{ apartment is defined ? apartment.id : '0' }}';

            if(typeof(Storage) !== "undefined") {
                $('.nav-tabs a').click(function (event) {
                    event.preventDefault();
                    var href     = $(this).attr('href');
                    var dataSave = JSON.stringify({id: idApartment, href: href});
                    localStorage.setItem('tab_apartment', dataSave);
                });

                var active = localStorage.getItem('tab_apartment');
                if (typeof active != 'undefined' && active != '') {
                    active = JSON.parse(active);

                    if (active.id == idApartment) {
                        $('[href="' + active.href + '"]').trigger('click');
                    }
                }
            } else {
                console.log('Sorry! No Web Storage support.');
            }

            $('.search-select').select2({
                allowClear: true
            });

            $('#property_type').tagator({
                autocomplete: [{{ propertyTypeVie }}],
                showAllOptionsOnFocus: true
            });


            $('#property_view').tagator({
                autocomplete: [{{ propertyViewVie }}],
                showAllOptionsOnFocus: true
            });

            $('#property_utility').tagator({
                autocomplete: [{{ propertyUtilityVie }}],
                showAllOptionsOnFocus: true
            });
        });
    </script>
{% endblock %}
