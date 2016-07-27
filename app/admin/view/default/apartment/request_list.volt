{% extends 'default.volt' %}

{% block content %}
    {% set apartmentRequestStatus = getApartmentRequestStatus() %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Danh sách yêu cầu</h3>
            </div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}

            <select id="selectStatus" class="form-control" style="width: 150px;">
                {% for key, value in apartmentRequestStatus %}
                    {% if filter is defined and filter == key %}
                        <option value="{{ key }}" selected="selected">{{ value }}</option>
                    {% else %}
                        <option value="{{ key }}">{{ value }}</option>
                    {% endif %}
                {% endfor %}
            </select>
            <div class="clearfix"></div>
            <br />

            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Họ tên</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Sản phẩm</th>
                        <th>Block</th>
                        <th>Dự án</th>
                        <th width="5%"></th>
                    </tr>
                </thead>

                {% if requests.items is defined and requests.items|length %}
                    <tbody>
                        {% for item in requests.items %}
                            <tr>
                                <td>{{ item['user_name'] }}</td>
                                <td>{{ item['user_phone'] }}</td>
                                <td>{{ item['user_email'] }}</td>
                                <td>
                                    <a href="{{ url({'for': 'apartment_edit', 'query': '?' ~ http_build_query({'id': item['apartment_id'], 'block_id': item['block_id']})}) }}">
                                        {{ item['apartment_name'] }}
                                    </a>
                                </td>
                                <td>{{ item['block_name'] }}</td>
                                <td>{{ item['project_name'] }}</td>
                                <td class="text-center" nowrap="nowrap">
                                    <a href="{{ url({'for': 'apartment_request_edit', 'query': '?' ~ http_build_query({'id': item['apartment_request_id']})}) }}">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        {% endfor %}
                    </tbody>
                {% endif %}
            </table>
        </div>

        <div class="col-sm-12">
            {{ paginationLayout }}
        </div>
    </div>
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#selectStatus').change(function() {
                var filter = $(this).val();
                window.location.href = '{{ url({'for': 'apartment_request_list'}) }}' + '?filter=' + filter;
            });
        });
    </script>
{% endblock %}