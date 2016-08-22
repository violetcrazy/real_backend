{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Cấu hình SEO</h3>
            </div>
        </div>

        <div class="col-sm-12">
            {{ flashSession.output() }}
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Meta title
                    </label>
                    <div class="col-sm-9">
                        <input id="meta_title" class="form-control" type="text" value="{% if config['meta_title'] is defined %}{{ config['meta_title'] }}{% endif %}" name="meta_title" />
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_title'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Meta description
                    </label>
                    <div class="col-sm-9">
                        <textarea id="meta_description" class="form-control" name="meta_description">{% if config['meta_description'] is defined %}{{ config['meta_description'] }}{% endif %}</textarea>
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_description'} %}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Meta keywords
                    </label>
                    <div class="col-sm-9">
                        <textarea id="meta_keywords" class="form-control" name="meta_keywords">{% if config['meta_keywords'] is defined %}{{ config['meta_keywords'] }}{% endif %}</textarea>
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_keywords'} %}
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