{% extends 'default.volt' %}

{% block content %}
    {% set footer1Vi = response['footer1_vi'] is defined and response['footer1_vi']['value'] != '' ? response['footer1_vi']['value'] : '' %}
    {% set footer2Vi = response['footer2_vi'] is defined and response['footer2_vi']['value'] != '' ? response['footer2_vi']['value'] : '' %}
    {% set footer3Vi = response['footer3_vi'] is defined and response['footer3_vi']['value'] != '' ? response['footer3_vi']['value'] : '' %}
    {% set footer1En = response['footer1_en'] is defined and response['footer1_en']['value'] != '' ? response['footer1_en']['value'] : '' %}
    {% set footer2En = response['footer2_en'] is defined and response['footer2_en']['value'] != '' ? response['footer2_en']['value'] : '' %}
    {% set footer3En = response['footer3_en'] is defined and response['footer3_en']['value'] != '' ? response['footer3_en']['value'] : '' %}

    {% set facebook = response['facebook'] is defined and response['facebook']['value'] != '' ? response['facebook']['value'] : '' %}
    {% set google_plus = response['google_plus'] is defined and response['google_plus']['value'] != '' ? response['google_plus']['value'] : '' %}
    {% set twitter = response['twitter'] is defined and response['twitter']['value'] != '' ? response['twitter']['value'] : '' %}

    {% set requestTimeout = response['request_timeout'] is defined and response['request_timeout']['value'] != '' ? response['request_timeout']['value'] : '' %}
    {% set rangePrice = response['range_price'] is defined and response['range_price']['value'] != '' ? response['range_price']['value'] : '' %}
    {% set priceScore = response['price_score'] is defined and response['price_score']['value'] != '' ? response['price_score']['value'] : '' %}
    {% set price_search = response['price_search'] is defined and response['price_search']['value'] != '' ? response['price_search']['value'] : '' %}

    {% set price_search_min = response['price_search_min'] is defined and response['price_search_min']['value'] != '' ? response['price_search_min']['value'] : '' %}
    {% set price_search_max = response['price_search_max'] is defined and response['price_search_max']['value'] != '' ? response['price_search_max']['value'] : '' %}

    {% set trend = response['trend'] is defined and response['trend']['value'] != '' ? response['trend']['value'] : '' %}

    {% set mess_reject_vi = response['mess_reject_vi'] is defined and response['mess_reject_vi']['value'] != '' ? response['mess_reject_vi']['value'] : '' %}
    {% set mess_reject_en = response['mess_reject_en'] is defined and response['mess_reject_en']['value'] != '' ? response['mess_reject_en']['value'] : '' %}

    {% set mess_signin_success_vi = response['mess_signin_success_vi'] is defined and response['mess_signin_success_vi']['value'] != '' ? response['mess_signin_success_vi']['value'] : '' %}
    {% set mess_signin_success_en = response['mess_signin_success_en'] is defined and response['mess_signin_success_en']['value'] != '' ? response['mess_signin_success_en']['value'] : '' %}

    {% set noimage = response['no_image'] is defined and response['no_image']['value'] != '' ? response['no_image']['value'] : '' %}

    {% set sliderHome = response['slider-home'] is defined and response['slider-home']['value'] != '' ? response['slider-home']['value'] : '' %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Tùy chỉnh hệ thống</h3>
            </div>
        </div>

        <div class="col-sm-12">
            <form action="" method="post" class="form-wrap">
                {{ flashSession.output() }}

                <div class="tabbable tabs-left">
                    <ul id="myTab" class="nav nav-tabs tab-bricky">
                        <li class="active">
                            <a href="#panel_home" data-toggle="tab">Trang chủ</a>
                        </li>
                        <li class="">
                            <a href="#panel_tab1" data-toggle="tab">Footer</a>
                        </li>
                        <li class="">
                            <a href="#panel_tab2" data-toggle="tab">Mạng xã hội</a>
                        </li>
                        <li class="">
                            <a href="#panel_tab3" data-toggle="tab">Thông số hệ thống</a>
                        </li>
                        <li class="">
                            <a href="#panel_tab4" data-toggle="tab">Thông báo, Mẫu văn bản</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {% include 'default/system/tabs/_tab_panel_tab1.volt' %}
                        {% include 'default/system/tabs/_tab_panel_tab2.volt' %}
                        {% include 'default/system/tabs/_tab_panel_tab3.volt' %}
                        {% include 'default/system/tabs/_tab_panel_tab4.volt' %}
                        {% include 'default/system/tabs/_tab_panel_home.volt' %}
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-bricky">Cập nhật</button>
                </div>
                <br />
            </form>
        </div>
    </div>
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/tinymce/tinymce.min.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            if(typeof(Storage) !== "undefined") {
                $('.nav-tabs a').click(function(){
                    var  href = $(this).attr('href');
                    localStorage.setItem('tab_option', href);
                });

                var active = localStorage.getItem('tab_option');
                $('[href="'+ active +'"]').trigger('click');
            } else {
                console.log('Sorry! No Web Storage support.');
            }

            tinymce.init({selector: '.editor'});

            $('.build-option-json-single').on('click', '.btn-add', function(event) {
                event.preventDefault();

                $blockOption = $(this).closest('.build-option-json-single');
                var _class = $blockOption.attr('data-class');
                var _bind = $blockOption.attr('data-bind');

                $this = $(this);
                var html = '<div class="row-item">\
                                <input type="text" class="form-control ' + _class + '" placeholder="'+ _bind +'" />\
                                <span class="delete-row clip-close-4"></span>\
                                <div class="clearfix"></div>\
                            </div>';

                $this.closest('.build-option-json-single').find('.endlist').before(html);
                $this.closest('.build-option-json-single').find('.endlist').prev('.row-item').find('input').focus();
                $('.format-number').number(true, 0, '.', ',');

            }).each(function(){
                $blockOption = $(this);
                var _class = $blockOption.attr('data-class');
                var _bind = $blockOption.attr('data-bind');
                var _old_value = $.trim($blockOption.find('.main-data').val());
                $this = $(this);

                if (typeof _old_value != 'undefined' && _old_value != '') {
                    var json = JSON.parse(_old_value);
                    var html = '';
                    $.each(json, function(index, value){
                        html += '<div class="row-item">\
                                    <input type="text" value="'+ value +'" class="form-control ' + _class + '" placeholder="'+ _bind +'" />\
                                    <span class="delete-row clip-close-4"></span>\
                                    <div class="clearfix"></div>\
                                </div>';
                    });

                    $this.closest('.build-option-json-single').find('.endlist').before(html);
                    $('.format-number').number(true, 0, '.', ',');
                }
            });

            $('.build-option-json').on('click', '.btn-add', function(event) {
                event.preventDefault();

                $blockOption = $(this).closest('.build-option-json');
                var _leftKey = $blockOption.attr('data-key-left');
                var _rightKey = $blockOption.attr('data-key-right');
                var _class = $blockOption.attr('data-class');

                $this = $(this);
                var html = '<div class=" row-price row-item">\
                        <div class="col-sm-6">\
                            <input type="text" data-name="' + _leftKey + '"  class="form-control ' + _class + '" placeholder="' + _leftKey + '" />\
                        </div>\
                        <div class="col-sm-6">\
                            <input type="text" data-name="' + _rightKey + '"  class="form-control ' + _class + '" placeholder="' + _rightKey + '" />\
                        </div>\
                        <span class="delete-row clip-close-4"></span>\
                        <div class="clearfix"></div>\
                    </div>';

                $this.closest('.build-option-json').find('.endlist').before(html);
                $('.format-number').number(true, 0, '.', ',');
            }).each(function() {
                $blockOption = $(this);

                var _leftKey = $blockOption.attr('data-key-left');
                var _rightKey = $blockOption.attr('data-key-right');
                var _class = $blockOption.attr('data-class');
                var old_value = $.trim($blockOption.find('.main-data').val());

                if (typeof old_value != 'undefined' && old_value != '') {
                    var json = JSON.parse(old_value);

                    $.each(json, function(index, value) {
                        var html = '<div class=" row-price row-item">\
                                <div class="col-sm-6">\
                                    <input type="text" data-name="' + _leftKey + '"  class="form-control ' + _class + '" value="' + value[_leftKey] + '" placeholder="' + _leftKey + '" />\
                                </div>\
                                <div class="col-sm-6">\
                                    <input type="text" data-name="' + _rightKey + '"  class="form-control ' + _class + '" value="' + value[_rightKey] + '" placeholder="' + _rightKey + '" />\
                                </div>\
                                <span class="delete-row clip-close-4"></span>\
                                <div class="clearfix"></div>\
                            </div>';

                        $blockOption.find('.endlist').before(html);
                        $('.format-number').number(true, 0, '.', ',');
                    });
                }
            });

            var checkCalc = false;
            $('form').submit(function(event) {
                if (!checkCalc) {
                    event.preventDefault();
                    $form = $(this);

                    $('.build-option-json').each(function() {
                        $blockOption = $(this);
                        var value = {};
                        var index = 1;

                        $blockOption.find('.row-item').each(function() {
                            $rowOption = $(this);
                            value[index] = {};

                            $rowOption.find('input').each(function() {
                                var $cell = $(this);
                                var _name = $cell.attr('data-name');
                                value[index][_name] = $cell.val();
                            });
                            index++;
                        });

                        $blockOption.find('.main-data').val(JSON.stringify(value));
                    });

                    $('.build-option-json-single').each(function(){
                        $blockOption = $(this);
                        var value = {};
                        var index = 1;

                        $blockOption.find('.row-item').each(function() {
                            $rowOption = $(this);
                            value[index] = {};

                            $rowOption.find('input').each(function() {
                                var $cell = $(this);
                                value[index] = $cell.val();
                            });
                            index++;
                        });

                        $blockOption.find('.main-data').val(JSON.stringify(value));
                    });


                    checkCalc = true;
                    $form.trigger('submit');
                }
            });

        }).on('click', '.delete-row', function() {
            var conf = window.confirm('Bạn chắc chắn muốn xóa dòng này?');
            if (conf) {
                $(this).closest('.row-item').slideUp('fast', function() {
                    $(this).remove();
                });
            }
        });
    </script>
{% endblock %}
