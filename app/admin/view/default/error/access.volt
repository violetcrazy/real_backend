{% extends 'default_error.volt' %}

{% block title %}Error{% endblock %}

{% block content %}
    <div class="box-login">
        <h3>Error</h3>
        <div class="errorHandler">
            {{ flashSession.output() }}
        </div>
    </div>
{% endblock %}