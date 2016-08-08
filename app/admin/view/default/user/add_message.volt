{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Thông báo</h3>
            </div>
        </div>
        <div class="col-sm-12">
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                {{ flashSession.output() }}

                {% include 'default/user/_message_form.volt' %}
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-bricky">
                            Gửi
                        </button>
                        {% if user.type is defined and user.type == constant('\ITECH\Data\Lib\Constant::USER_TYPE_AGENT') %}
                            <a href="{{ url({'for': 'userAgentList'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        {% elseif user.type is defined and user.type == constant('\ITECH\Data\Lib\Constant::USER_TYPE_MEMBER') %}
                            <a href="{{ url({'for': 'userMemberList'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        {% endif %}
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}
