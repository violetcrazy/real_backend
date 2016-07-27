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
                <h3>Chỉnh sửa dự án</h3>
            </div>
        </div>
    </div>

    {{ flashSession.output() }}

    <div class="tabbable">
        <ul id="myTab" class="nav nav-tabs tab-bricky">
            <li class="active">
                <a href="#panel_tab1" data-toggle="tab">Thông tin</a>
            </li>
            <li>
                <a href="#panel_tab2" data-toggle="tab">Mô tả</a>
            </li>
            <li>
                <a href="#panel_tab3" data-toggle="tab">Hình ảnh</a>
            </li>
            <li>
                <a href="#panel_tab4" data-toggle="tab">Thuộc tính</a>
            </li>
            <li>
                <a href="#panel_tab5" data-toggle="tab">SEO</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane in active" id="panel_tab1">
                {% include 'default/project/_form_tab1.volt' %}
            </div>
            <div class="tab-pane" id="panel_tab2">
                {% include 'default/project/_form_tab2.volt' %}
            </div>
            <div class="tab-pane" id="panel_tab3">
                {% include 'default/project/_form_tab3.volt' %}
            </div>
            <div class="tab-pane" id="panel_tab4">
                {% include 'default/project/_form_tab4.volt' %}
            </div>
            <div class="tab-pane" id="panel_tab5">
                {% include 'default/project/_form_tab5.volt' %}
            </div>
        </div>
        <br>

        <div class="form-group">
            <div class="col-sm-12 text-right">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    <span class="clip-download"></span>
                    {{ project is defined ? 'Cật nhật' : 'Thêm Dự án' }}
                </button>

                <a href="{{ url({'for': 'project_list', 'query': '?' ~ http_build_query({'page': page})}) }}" class="btn btn-primary">
                    <span class="fa-mail-reply fa"></span>
                    Trở lại
                </a>

                <a target="_blank" href="{{ url({'for': 'block_list', 'query': '?project_id=' ~  project.id }) }}" class="btn btn-warning">
                    Danh sách Block
                </a>
            </div>
        </div>
    </div>
    <input type="hidden" name="id" value="{% if id is defined %}{{ id }}{% endif %}" />

</form>
<script type="text/javascript">
    var process = false;

    $(document).ready(function() {
        $(document).on('submit', '#form_tab', function(e) {
            e.preventDefault();

            if (!process) {
                process = true;
                $.ajax({
                    url: '{{ url({'for': 'project_post_ajax'}) ~ '?tab=3&page=' ~ page }}',
                    type: 'POST',
                    data: $('#form_tab').serialize(),
                    success: function(response) {
                        if (typeof response != 'undefined') {
                            if (response.status == 200) {
                                if (typeof response.result.redirect_url != 'undefined') {
                                    window.location.href = response.result.redirect_url;
                                }
                            } else {
                                $('#form_tab3_error_message').text(response.message).show();

                                if (typeof response.result != 'undefined') {
                                    $.each(response.result, function(key, value) {
                                        $('#form_tab #error_' + key).text(value);
                                    });
                                }
                            }
                        } else {
                            alert('Lỗi, không thể cập nhật.');
                        }
                    }
                }).done(function() {
                    process = false;
                });
            }
        });

        var idProject = '{{ project is defined ? project.id : '0' }}';
        if(typeof(Storage) !== "undefined") {
            $('.nav-tabs a').click(function(event){
                event.preventDefault();
                var  href = $(this).attr('href');
                var dataSave = JSON.stringify({id: idProject, href: href});
                localStorage.setItem('tab_project', dataSave);
            });

            var active = localStorage.getItem('tab_project');
            if (typeof active != 'undefined' && active != '') {
                active = JSON.parse(active);

                if (active.id == idProject) {
                    $('[href="'+ active.href +'"]').trigger('click');
                }
            }

        } else {
            console.log('Sorry! No Web Storage support.');
        }
    });
</script>
{% endblock %}