{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thống kê hệ thống</h3>
            </div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}
        </div>
    </div>
{% endblock %}