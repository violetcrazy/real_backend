{% extends 'default.volt' %}

{% block content %}
    {% set projectStatus = getProjectStatus() %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <a href="{{ url({'for': 'block_add'}) }}" class="btn btn-primary pull-right">Thêm block/khu</a>
                <h3>Danh sách Block/Khu</h3>
            </div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}
            <div class="wrap-table-scroll" style="overflow: auto">
                <table class="table table-striped table-hover table-jquery display nowrap" id="table-list-block" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Block/Khu</th>
                        <th>Dự án</th>
                        <th>Số tầng</th>
                        <th>Maplink</th>
                        <th>Trạng thái</th>
                        <th>Tổng</th>
                        <th>Đã bán</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th><input type="text" placeholder="Nhập tên Block/Khu"></th>
                        <th><input type="text" placeholder="Nhập tên dự án"></th>
                        <th><input type="text" placeholder="Nhập tầng"></th>
                        <th><label for="map_link" class="custom-mark mark-condition-"><input name="filter_condition" class="filter-column" type="checkbox" id="map_link" value="Ok"><span class="mark-input"></span> Đã vẽ </label></th>
                        <th></th>
                        <th></th>
                        <th></th>
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

    <link rel="stylesheet" href="{{ config.application.base_url}}asset/plugins/datatables/css/jquery.dataTables.css">
    <script type="text/javascript" src="{{ config.application.base_url}}asset/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            var blockDataTable = $('#table-list-block').DataTable({
                "lengthMenu": [ [5,10, 25, 50, -1], [5,10, 25, 50, "Tất cả"] ],
                "aoColumns":[
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": true},
                    {"bSortable": true},
                    {"bSortable": false}
                ],
                "aaSorting": [[0,'desc']],
                "pageLength": 25,
                "drawCallback": function ( settings ) {
                    var api = this.api();
                    var rows = api.rows( {page:'current'} ).nodes();
                    var last=null;

                    api.column(2, {page:'current'} ).data().each( function ( group, i ) {
                        if ( last !== group ) {
                            $(rows).eq( i ).before(
                                    '<tr class="group"><td colspan="10"> <b>Dự án:</b> '+group+'</td></tr>'
                            );
                            last = group;
                        }
                    } );
                },
                "ajax": "{{ url({ 'for': 'block_list_block' }) }}?project_id={{ request.getQuery('project_id') }}",
                "language": {
                    "decimal": "",
                    "emptyTable": "Không có Block/Khu",
                    "infoEmpty":  "",
                    "infoFiltered":   "(Tìm trong _MAX_ Block/Khu)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "loadingRecords": "Đang tải...",
                    "processing": "Đang xử lý...",
                    "search": "Tìm kiếm:",
                    "lengthMenu": "Hiển thị _MENU_ Block/Khu trên 1 trang",
                    "zeroRecords": "Không tìm thấy Block/Khu nào",
                    "info": "Hiển thị _PAGE_/_PAGES_",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "›",
                        "previous": "‹"
                    },
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }
            });

            blockDataTable.columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that.search( this.value ).draw();
                    }
                } );

                $('input[type="checkbox"]', this.footer()).on('change', function () {

                    blockDataTable.columns([4,5,6,7]).search('').draw();
                    if ($(this).is(':checked')){
                        that.search(this.value).draw();
                        $('input[type="checkbox"]').not($(this)).prop('checked', false)
                    }
                });
            });
        })
    </script>
{% endblock %}
