{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                {% if f != 'smart-search' %}
                    <h3>Chỉnh sửa box hiển thị sản phẩm</h3>
                {% else %}
                    <h3>Chỉnh sửa smart search</h3>
                {% endif %}
            </div>
        </div>

        <div class="col-sm-12">
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                {{ flashSession.output() }}

                {% include 'default/ceriterial/_form.volt' %}
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-bricky">
                            Cập nhật
                        </button>

                        {% if f == 'buy' %}
                            <a href="{{ url({'for': 'ceriterial_buy_list'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        {% elseif f == 'rent' %}
                            <a href="{{ url({'for': 'ceriterial_rent_list'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        {% elseif f == 'smart-search' %}
                            <a href="{{ url({'for': 'ceriterial_smart_search_list'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        {% else %}
                            <a href="{{ url({'for': 'ceriterial'}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                        {% endif %}
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}