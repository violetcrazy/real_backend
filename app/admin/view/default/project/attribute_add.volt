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

    {% include 'default/project/_form_attribute.volt' %}
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