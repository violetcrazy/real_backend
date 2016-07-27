{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Sửa bài viết</h3>
            </div>
        </div>

        <div class="col-sm-12">
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                {{ flashSession.output() }}

                <div class="col-sm-9 col-0">
                    {% include 'default/article/_form.volt' %}

                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-bricky">
                                Cập nhật
                            </button>
                            <a href="{{ url({'for': 'article'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-0">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-external-link-square"></i>
                            Danh mục
                        </div>
                        <div class="panel-body">
                            {{ categoryLayout }}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}