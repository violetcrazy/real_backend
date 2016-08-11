{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thêm Mục banner</h3>
            </div>
        </div>
        <div class="col-sm-12">
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                {{ flashSession.output() }}

                {% include 'default/banner/_form.volt' %}
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="text-right col-sm-10">
                        <button type="submit" class="btn btn-bricky">
                            Thêm
                        </button>
                        <a href="{{ url({'for': 'category'}) }}" class="btn btn-primary">
                            Trở lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}