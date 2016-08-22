{% extends 'default.volt' %}

{% block top_css %}
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/dropzone/downloads/css/dropzone.css?' ~ config.asset.version }}" />
{% endblock %}

{% block top_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'assets/plugins/ckeditor/ckeditor.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'assets/plugins/ckeditor/adapters/jquery.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/dropzone/downloads/dropzone.js?' ~ config.asset.version }}"></script>
{% endblock %}

{% block content %}
    <form action="" method="POST" id="form_tab" class="form-horizontal" enctype="multipart/form-data">
        <div class="row">
            <div class="col-sm-12">
                {% include 'default/element/layout/breadcrumbs.volt' %}

                <div class="page-header">
                    <h3>Thêm dự án</h3>
                </div>
            </div>
        </div>

        {{ flashSession.output() }}

        <div class="tabbable">
            <ul id="myTab" class="nav nav-tabs tab-bricky">
                <li class="active">
                    <a href="#panel_tab1" data-toggle="tab">Thông tin</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane in active" id="panel_tab1">
                    {% include 'default/project/_form_tab1.volt' %}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12 text-right">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    <span class="clip-download"></span>
                    {{ project is defined ? 'Cật nhật' : 'Thêm Dự án' }}
                </button>

                <a href="{{ url({'for': 'project_list'}) }}" class="btn btn-primary">
                    <span class="fa-mail-reply fa"></span>
                    Trở lại
                </a>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        var process = false;

        $(document).ready(function () {
            $(document).on('submit', '#form_tab', function (e) {
                e.preventDefault();

                if (!process) {
                    process = true;

                    $.ajax({
                        url : '{{ url({'for': 'project_post_ajax'}) }}',
                        type: 'POST',
                        data: $('#form_tab').serialize(),
                        success: function (response) {
                            if (typeof response != 'undefined') {
                                if (response.status == 200) {
                                    if (typeof response.result.redirect_url != 'undefined') {
                                        window.location.href = response.result.redirect_url;
                                    }
                                } else {
                                    $('#form_tab3_error_message').text(response.message).show();

                                    if (typeof response.result != 'undefined') {
                                        $.each(response.result, function (key, value) {
                                            $('#form_tab #error_' + key).text(value);
                                        });
                                    }
                                }
                            } else {
                                alert('Lỗi, không thể Tạo mới.');
                            }
                        }
                    }).done(function () {
                        process = false;
                    });
                }
            });
        });
    </script>
{% endblock %}
