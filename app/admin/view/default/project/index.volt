{% extends 'default.volt' %}

{% block content %}
    {% set projectStatus = getProjectStatus() %}
    {% set userSession   = session.get('USER') %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                {% if userSession['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN') %}
                    <a href="{{ url({'for': 'project_add'}) }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i>
                        Thêm dự án
                    </a>
                {% endif %}

                <h3>Danh sách dự án</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-hover table-jquery display nowrap" id="table-list-project" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dự án</th>
                        <th nowrap="nowrap">Số Block/Khu</th>
                        <th nowrap="nowrap">Số sản phẩm</th>
                        <th nowrap="nowrap">Còn trống</th>
                        <th nowrap="nowrap">Đang giao dịch</th>
                        <th nowrap="nowrap">Đã bán</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th><input class="form-control" type="text" placeholder="Nhập tên dự án"></th>
                        <th><input class="form-control" type="text" placeholder="Nhập số Block/Khu"></th>
                        <th><input class="form-control" type="text" placeholder="Nhập số Sản phẩm"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                    {% if projects is defined %}
                        {% for project in projects %}
                            <tr>
                                <td>
                                    {{ project.id }}
                                </td>
                                <td>
                                    {% set projectPage = page is defined ? page : 1 %}
                                    <a role="menuitem" href="{{ url({ 'for': 'project_edit', 'query': '?' ~ http_build_query({'id': project.id}) }) }}">
                                        {{ project.name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ url({ 'for': 'block_list', 'query': '?project_id=' ~ project.id }) }}">
                                        {{ project.block_count }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ url({ 'for': 'apartment_list_by_project', 'query': '?' ~ http_build_query({ 'project_id': project.id })}) }}">
                                        {{ project.apartment_count }}
                                    </a>
                                </td>
                                <td>{{ project.available_count }}</td>
                                <td>{{ project.processing_count }}</td>
                                <td>{{ project.sold_count }}</td>
                                <td class="text-center">
                                    {% if userSession['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN') %}
                                        <a href="{{ url({'for': 'project_delete', 'query': '?' ~ http_build_query({'project_id': project.id })}) }}" onclick="return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">
                                            <i class="fa fa-times fa fa-white"></i>
                                        </a>
                                    {% endif %}
                                </td>
                            </tr>
                        {% endfor %}
                    {% endif %}
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
        </div>
    </div>

    <link rel="stylesheet" href="{{ config.application.base_url}}asset/plugins/datatables/css/jquery.dataTables.css">
    <script type="text/javascript" src="{{ config.application.base_url}}asset/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var tableProject = $('#table-list-project').DataTable({
                "aoColumns": [
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                ],
                "aaSorting": [[0,'desc']],
                "pageLength": 25,
                "language": {
                    "decimal": "",
                    "emptyTable": "Không có dự án",
                    "infoEmpty":  "Không có dự án",
                    "infoFiltered":   "(Tìm trong _MAX_ dự án)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "loadingRecords": "Đang tải...",
                    "processing": "Đang xử lý...",
                    "search": "Tìm kiếm:",
                    "lengthMenu": "Hiển thị _MENU_ dự án trên 1 trang",
                    "zeroRecords": "Không tìm thấy dự án nào",
                    "info": "Hiển thị _PAGE_/_PAGES_",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "›",
                        "previous": "‹"
                    },
                    "aria": {
                        "sortAscending":  ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }
            });

            tableProject.columns().every(function () {
                var that = this;
                $('input[type="text"]', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        })
    </script>
{% endblock %}
