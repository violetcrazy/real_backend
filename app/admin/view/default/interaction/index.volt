{% extends 'default.volt' %}

{% block content %}
    
    {% set messageTypeSelect = getMessageType() %}
    {% set messageStatusSelect = getMessageStatus() %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Danh sách thông báo chung</h3>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'interaction_add'}) }}" class="btn btn-primary float-right">Thông báo</a>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                        </th>
                        <th>Tiêu đề</th>
                        <th width="30%" >Nội dung</th>
                        <th width="10%" >Gửi tới</th>
                        <th width="5%" ></th>
                    </tr>
                </thead>
                <tbody>
                    {% if result is defined and result|length %}
                        {% for item in result %}
                            <tr>
                                <td>
                                    {{ item.id }}
                                </td>
                                <td>
                                    <a role="menuitem" tabindex="-1" href="{{ url({'for': 'interaction_edit', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                        {{ item.name }}
                                    </a>
                                </td>
                                <td>
                                    {{ item.description }}
                                </td>
                                <td>
                                    {{ messageTypeSelect[item.type] }}
                                </td>
                                <td>
                                    {{ messageStatusSelect[item.status] }}
                                </td>
                            </tr>
                        {% endfor %}
                    {% endif %}
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
            {{ paginationLayout }}
        </div>
    </div>
{% endblock %}