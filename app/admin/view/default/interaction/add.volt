{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thông báo</h3>
            </div>
        </div>
        <div class="col-sm-12">
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                {{ flashSession.output() }}

                {% include 'default/interaction/_form.volt' %}
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-bricky">
                            Thêm
                        </button>
                        <a href="{{ url({'for': 'interaction'}) }}" class="btn btn-primary">
                            Trở lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}