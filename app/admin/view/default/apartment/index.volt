{% extends 'default.volt' %}

{% block content %}
    {% set projectStatus = getProjectStatus() %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <a href="{{ url({'for': 'apartment_add'}) }}" class="btn btn-primary pull-right">
                    <i class="fa fa-plus"></i>
                    Thêm sản phẩm
                </a>

                <h3>Danh sách sản phẩm</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="col-sm-12">
            <form action="" method="GET" class="sidebar-search-form">
                <select id="project-filter" name="" class="form-control" id="">
                    <option value="">1.000 sản phẩm mới nhất</option>
                    {% if projects is defined %}
                        {% for project in projects %}
                            <option value="{{ project.id }}">{{ project.name }}</option>
                        {% endfor %}
                    {% endif %}
                </select>
            </form>
            <br />
        </div>
        <div class="clearfix"></div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <div class="wrap-table-scroll" style="overflow: auto">
                <table class="table table-striped table-hover table-jquery display nowrap" id="table-list-apartment" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Block</th>
                        <th>Dự án</th>
                        <th>Còn trống</th>
                        <th>Đang xử lý</th>
                        <th>Đã bán</th>
                        <th>Khác</th>
                        <th>Trạng thái</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th><input class="form-control" type="text" placeholder="Nhập tên sản phẩm"></th>
                        <th><input class="form-control" type="text" placeholder="Nhập tên Block/Khu"></th>
                        <th><input class="form-control" type="text" placeholder="Nhập tên dự án"></th>
                        <th>
                            <label for="condition_1" class="custom-mark mark-condition-"><input name="filter_condition" class="filter-column" type="checkbox" id="condition_1" value="Còn trống"> <span class="mark-input"></span> Còn trống</label>
                        </th>
                        <th>
                            <label for="condition_2" class="custom-mark mark-condition-"><input name="filter_condition" class="filter-column" type="checkbox" id="condition_2" value="Đang xử lý"> <span class="mark-input"></span> Đang xử lý</label>
                        </th>
                        <th>
                            <label for="condition_3" class="custom-mark mark-condition-"><input name="filter_condition" class="filter-column" type="checkbox" id="condition_3" value="Đã bán"> <span class="mark-input"></span> Đã bán</label>
                        </th>
                        <th>
                            <label for="condition_4" class="custom-mark mark-condition-"><input name="filter_condition" class="filter-column" type="checkbox" id="condition_4" value="Other"> <span class="mark-input"></span> Khác</label>
                        </th>
                        <th>
                            <select name="" id="" class="form-control">
                                <option value="">Chọn trạng thái</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Removed">Removed</option>
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
    </div>

    <link type="text/css" rel="stylesheet" href="{{ config.application.base_url }}asset/plugins/datatables/css/jquery.dataTables.css?{{ config.asset.version }}" />
    <script type="text/javascript" src="{{ config.application.base_url }}asset/plugins/datatables/js/jquery.dataTables.min.js?{{ config.asset.version }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var t = $.toast({
                text            : "Đang tải dữ liệu. Vui lòng đợi",
                bgColor         : '#2EA093',
                textColor       : '#fff',
                allowToastClose : close,
                stack           : 0,
                position        : 'top-center',
                hideAfter       : false
            });

            var apartmentDataTable = $('#table-list-apartment').DataTable({
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tất cả"]],
                "pagingType": "full_numbers",
                "aoColumns" : [
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                ],
                "aaSorting" : [[0,'desc']],
                "pageLength": 25,
                "ajax"      : "{{ url({'for': 'apartment_list_apartment'}) }}",
                "initComplete": function (settings, json) {
                   t.reset();
                },
                "language": {
                    "decimal": "",
                    "emptyTable": "Không có Sản phẩm",
                    "infoEmpty": "",
                    "infoFiltered": "(Tìm trong _MAX_ Sản phẩm)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "loadingRecords": "Đang tải...",
                    "processing": "Đang xử lý...",
                    "search": "Tìm kiếm:",
                    "lengthMenu": "Hiển thị _MENU_ Sản phẩm trên 1 trang",
                    "zeroRecords": "Không tìm thấy Sản phẩm nào",
                    "info": "Hiển thị _PAGE_/_PAGES_",
                    "paginate": {
                        "first"   : "« Đầu tiên",
                        "last"    : "Cuối cùng »",
                        "next"    : "›",
                        "previous": "‹"
                    },
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }
            });

            $('#project-filter').on('change', function (event) {
                t = $.toast({
                    text            : "Đang tải dữ liệu. Vui lòng đợi",
                    bgColor         : '#2EA093',
                    textColor       : '#fff',
                    allowToastClose : close,
                    stack           : 0,
                    position        : 'top-center',
                    hideAfter       : false
                });

                var url = "{{ url({ 'for': 'apartment_list_apartment' }) }}?project_id=" + $(this).val();
                apartmentDataTable.ajax.url(url).load(function (data) {
                    t.reset();
                    if (data.data[0][0] == '') {
                        $.toast({
                            text            : "Chưa có sản phẩm",
                            bgColor         : '#FFFB91',
                            loader          : false,
                            textColor       : '#000',
                            allowToastClose : close,
                            stack           : 0,
                            position        : 'top-center',
                            hideAfter       : 3000
                        });
                        $('.table-jquery').find('tbody').find('tr').html('<td class="text-center" colspan="10">Chưa có sản phẩm cho dự án này</td>');
                        $('.table-jquery').find('tbody').find('tr.group').remove();
                    }
                });
            });

            apartmentDataTable.columns().every(function () {
                var that = this;
                $('input[type="text"]', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });

                $('input[type="checkbox"]', this.footer()).on('change', function () {
                    apartmentDataTable.columns([4, 5, 6, 7]).search('').draw();
                    if ($(this).is(':checked')) {
                        that.search(this.value).draw();
                        $('input[type="checkbox"]').not($(this)).prop('checked', false);
                    }
                });

                $('select', this.footer()).on('change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });

            });

            {% if (request.getQuery('project_id')) %}
                $('#project-filter').val('{{ request.getQuery('project_id') }}');
                {% if (request.getQuery('block_id')) %}
                    var url = "{{ url({ 'for': 'apartment_list_apartment' }) }}?project_id={{ request.getQuery('project_id') }}&block_id={{ request.getQuery('block_id') }}";
                {% else %}
                    var url = "{{ url({ 'for': 'apartment_list_apartment' }) }}?project_id={{ request.getQuery('project_id') }}";
                {% endif %}
                apartmentDataTable.ajax.url(url).load(function (data) {
                    t.reset();
                    if (data.data[0][0] == '') {
                        $.toast({
                            text            : "Chưa có sản phẩm",
                            bgColor         : '#FFFB91',
                            loader          : false,
                            textColor       : '#000',
                            allowToastClose : close,
                            stack           : 0,
                            position        : 'top-center',
                            hideAfter       : 3000
                        });
                        $('.table-jquery').find('tbody').find('tr').html('<td class="text-center" colspan="10">Chưa có sản phẩm cho dự án này</td>');
                        $('.table-jquery').find('tbody').find('tr.group').remove();
                    }
                });
            {% endif %}
        });
    </script>
{% endblock %}
