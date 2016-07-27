{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
        </div>
    </div>
    <div class="panel-body">
        {{ flashSession.output() }}
    </div>

    <div class="panel-body">
        <div class="tabbable">
            <ul id="myTab" class="nav nav-tabs tab-bricky">
                <li class="active">
                    <a href="#panel_tab1" data-toggle="tab">Dự án</a>
                </li>
                <li class="">
                    <a href="#panel_tab2" data-toggle="tab">Block</a>
                </li>
                <li class="">
                    <a href="#panel_tab3" data-toggle="tab">Nhập sản phẩm mới</a>
                </li>
                <li class="">
                    <a href="#panel_tab5" data-toggle="tab">Cập nhật sản phẩm</a>
                </li>
                <li class="">
                    <a href="#panel_tab4" data-toggle="tab">Xuất sản phẩm</a>
                </li>
                <li class="">
                    <a href="#panel_tab6" data-toggle="tab">Tra cứu thông tin</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane in active" id="panel_tab1">
                    {% include 'default/system/element/_import_project.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab2">
                    {% include 'default/system/element/_import_block.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab3">
                    {% include 'default/system/element/_import_apartment.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab4">
                    {% include 'default/system/element/_export_apartment.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab5">
                    {% include 'default/system/element/_import_apartment_update.volt' %}
                </div>
                <div class="tab-pane" id="panel_tab6">
                    <div class="">
                        <p><b>ID tương ứng các Hướng</b></p>
                        1 => Đông, <br>
                        2 => Đông Nam, <br>
                        3 => Nam, <br>
                        4 => Tây Nam, <br>
                        5 => Tây, <br>
                        6 => Tây Bắc, <br>
                        7 => Bắc, <br>
                        8 => Đông Bắc, <br>
                        9 => Tây Bắc - Tây Nam, <br>
                        10 => Đông Bắc - Tây Bắc, <br>
                        11 => Đông Bắc - Đông Nam, <br>
                        12 => Đông Nam - Tây Nam, <br>
                        13 => Đông Tây, <br>
                        14 => Đông Nam - Tây Bắc, <br>
                        15 => Đông Bắc - Tây Nam' <br>
                    </div>
                    <hr>

                    <div class="">
                        <p><b>ID tỉnh thành tương ứng</b></p>
                        {% if location is defined %}
                            {% for key, item in location['result'] %}
                                <b class="text-danger">{{ item['id'] }} => {{ item['name'] }}</b> ,<br>
                                {% for skey, sitem in item['district'] %}
                                    &#160&#160&#160&#160 {{ sitem['id'] }} => {{ sitem['name'] }} ,<br>
                                {% endfor %}
                            {% endfor %}
                        {% endif %}
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('form').trigger('reset');
            saveTab($,'tab_data_import');
        });
    </script>
{% endblock %}

{% block bottom_js %}{% endblock %}