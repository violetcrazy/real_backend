{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %} 
            <div class="page-header">
                <h3>Cấu hình Email</h3>
            </div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Host
                    </label>
                    <div class="col-sm-9">
                        <input id="host" class="form-control" type="text" value="{% if config['host'] is defined %}{{ config['host'] }}{% endif %}" name="host">
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'host'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Port
                    </label>
                    <div class="col-sm-9">
                        <input id="port" class="form-control" type="text" value="{% if config['port'] is defined %}{{ config['port'] }}{% endif %}" name="port">
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'port'} %}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        User name
                    </label>
                    <div class="col-sm-9">
                        <input id="meta_title" class="form-control" type="text" value="{% if config['username'] is defined %}{{ config['username'] }}{% endif %}" name="username">
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'username'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Password
                    </label>
                    <div class="col-sm-9">
                        <input id="password" class="form-control" type="password" value="{% if config['password'] is defined %}{{ config['password'] }}{% endif %}" name="password">
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'password'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-bricky">
                            Cập nhật
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}