{% extends 'default.volt' %}

{% block content %}
    {% set user_session = session.get('USER') %}
    {% set userStatus = getUserStatus() %}
    {% set userMembership = getUserMembership() %}
    {% set userMembershipAdmin = getUserMembershipAdministrator() %}
    {% set userMembershipAgent = getUserMembershipAgent() %}
    {% set active = 'active' %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}

            <div class="page-header">
                {% if user_session['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN') %}
                    <a href="{{ url({'for': 'user_add_admin', 'query': '?' ~ http_build_query({'q': q, 'filter': 'all_admin_list'})}) }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i>
                        Thêm Quản trị viên
                    </a>
                {% endif %}

                <h3>Danh sách Quản trị viên</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        {#
            <div class="col-sm-12">
                <form action="" method="GET" class="sidebar-search-form" enctype="multipart/form-data">
                    <div class="input-group">
                        <input type="text" name="q" value="{% if q is defined %}{{ q }}{% endif %}" class="form-control" placeholder="Tìm kiếm" />
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-success">Tìm kiếm</button>
                        </span>
                    </div>
                </form>
                <br />
            </div>
        #}
        <div class="clearfix"></div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <table class="table table-striped table-bordered table-jquery table-hover table-full-width" id="table-user">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Họ tên</th>
                        <th>Loại</th>
                        <th>Đăng ký</th>
                        <th>Đăng nhập</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th><input class="form-control" type="text" placeholder="Nhập tên QTV"></th>
                        <th><input class="form-control" type="text" placeholder="Nhập loại QTV"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                    {% for item in result %}
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>
                                <a href="{{ url({'for': 'user_edit_admin', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'filter': 'all_admin_list'})}) }}">
                                    {{ item.username }}
                                </a>
                            </td>
                            <td>{{ item.name }}</td>
                            <td>{{ userMembershipAdmin[item.membership] }}</td>
                            <td>{{ date('d-m-Y H:i:s', strtotime(item.created_at)) }}</td>
                            <td>
                                {% if strtotime(item.logined_at) %}
                                    {{ date('d-m-Y H:i:s', strtotime(item.logined_at)) }}
                                {% endif %}
                            </td>
                            <td>{{ userStatus[item['status']] }}</td>
                            <td class="text-center">
                                {% if user_session['id'] != item['id'] %}
                                    <a href="{{ url({'for': 'user_delete_admin', 'query': '?' ~ http_build_query({'id': item.id, 'q': q, 'filter': 'all_admin_list'})}) }}" onclick="return confirm('Đồng ý xoá?');" class="btn btn-xs btn-bricky">
                                        <i class="fa fa-times fa fa-white"></i>
                                    </a>
                                {% endif %}
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    <link type="text/css" rel="stylesheet" href="{{ config.application.base_url }}asset/plugins/datatables/css/jquery.dataTables.css?{{ config.asset.version }}" />
    <script type="text/javascript" src="{{ config.application.base_url }}asset/plugins/datatables/js/jquery.dataTables.min.js?{{ config.asset.version }}"></script>

    <script type="text/javascript">
    $(document).ready(function(){
        var TableUser = $('#table-user').DataTable({
            "aoColumns" : [
                {"bSortable": true},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
            ],
        });

        TableUser.columns().every(function () {
            var that = this;
            $('input[type="text"]', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });

            $('select', this.footer()).on('change', function () {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });

        });
    })
    </script>
{% endblock %}
