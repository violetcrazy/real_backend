{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Sửa bài viết phong thủy</h3>
            </div>
        </div>

        <div class="col-sm-12">
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                {{ flashSession.output() }}

                <div class="col-sm-9">
                    {% include 'default/article/_fengshui_form.volt' %}
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-bricky">
                                Cập nhật
                            </button>
                            <a href="{{ url({'for': 'article_list_fengshui'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}