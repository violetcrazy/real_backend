{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh sách thuộc tính</h3>
            </div>
        </div>

        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'load_attribute_add_ajax'}) }}?module_attr={{ constant('\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT') }}" class="btn btn-primary float-right btn-add-attr">Thêm thuộc tính</a>
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-jquery table-hover" id="table-list-attr">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Tên tiếng anh</th>
                    <th>Nhóm</th>
                    <th width="5%">Xóa</th>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <th></th>
                    <th><input class="form-control" type="text" placeholder="Nhập tên thuộc tính"></th>
                    <th><input class="form-control" type="text" placeholder="Nhập tên thuộc tính"></th>
                    <th>
                        <select name="" id="" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="Kiểu căn hộ">Kiểu căn hộ</option>
                            <option value="Môi trường sống">Môi trường sống</option>
                            <option value="Dịch vụ - Tiện ích">Dịch vụ - Tiện ích</option>
                        </select>
                    </th>
                    <th></th>
                </tr>
                </tfoot>

                <tbody>

                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>

    </div>

    <link rel="stylesheet" href="{{ config.application.base_url}}asset/plugins/datatables/css/jquery.dataTables.css">
    <script type="text/javascript" src="{{ config.application.base_url}}asset/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script>
        var url = "{{ url({ 'for': 'load_attribute_list_ajax', 'module_attr': constant('\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT') }) }}";
    </script>
    <script type="text/javascript" src="{{ config.application.base_url}}asset/default/js/attr_list.js"></script>
{% endblock %}