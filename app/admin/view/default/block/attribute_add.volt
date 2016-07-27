{% extends 'default.volt' %}

{% block top_css %}
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'css/bootstrap-modal-bs3patch.css?' ~ config.asset.version }}" />
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'css/bootstrap-modal.css?' ~ config.asset.version }}" />
{% endblock %}

{% block top_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/bootstrap-modal.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/bootstrap-modalmanager.js?' ~ config.asset.version }}"></script>
{% endblock %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Thêm thuộc tính</h3>
            </div>
        </div>
    </div>

    <form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-2 control-label">
                Tiêu đề <span class="symbol required"></span>
            </label>
            <div class="col-sm-8">
                {{ form.render('name', {'class': 'form-control'}) }}
                {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                Icon
            </label>
            <div class="col-sm-1">
                <div class="thumbnail-img">
                    <span id="icon-choosed">
                        <img src="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" id="image_one_url"/>
                        <input type="hidden" id="image_one_name" name="image_one" value="" />
                        <input type="hidden" id="image_two_name" name="image_two" value="" />
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-2">
                <a data-toggle="modal" id="modal_ajax_demo_btn" class="demo btn btn-primary">
                    Chọn icon
                </a>

                <div id="ajax-modal" class="modal fade" tabindex="-1" style="display: none;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <div><b>Chọn Icon</b></div>
                    </div>
                    <div class="modal-body">
                        {% for item in iconsList %}
                            <span class="item-icon-select">
                                <img src="{{ item['image_url'] ~ '?' ~ config.asset.version }}" />
                                <input type="hidden" class="image_url" value="{{ item['image_name'] }}" />
                            </span>
                        {% endfor %}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="select_icon">Chọn</button>
                        <button type="button" class="btn" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                Ngôn ngữ
            </label>
            <div class="col-sm-3">
                {{ form.render('language', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                Kiểu
            </label>
            <div class="col-sm-3">
                {{ form.render('type', {'class': 'form-control'}) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
            </label>
            <div class="col-sm-3">
                <button type="submit" id="form-article-button" class="btn btn-bricky">
                    Thêm
                </button>
            </div>
        </div>
    </form>
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript">
        $(document).ready(function() {
            var $modal = $('#ajax-modal');

            $('#modal_ajax_demo_btn').on('click', function() {
                $modal.modal();
            });

            $('body').on('click', '.item-icon-select', function(){
                $('.item-icon-select').removeClass('active');
                $(this).addClass('active');
            });

            $('body').on('click', '#select_icon', function(e) {
                e.preventDefault();

                var src = $('.modal-body').find('.item-icon-select.active').find('img').attr('src');
                var name = $('.modal-body').find('.item-icon-select.active').find('input').val();
                $('.item-icon-select').removeClass('active');

                $modal.modal('hide');

                // Xử lý gắn vào đâu đó thì làm ở đây
                $('#image_one_url').attr('src', src);
                $('#image_one_name').val(name);
                $('#image_two_name').val(name);
            });
        });
    </script>
{% endblock %}