<!DOCTYPE html>
<!--[if IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
    <head>
        <meta charset="utf-8" />
        <title>Administrator</title>
        <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="robots" content="noindex, nofollow" />

        {% include 'default/element/layout/asset.volt' %}

        {% block top_css %}{% endblock %}
        {% block top_js %}{% endblock %}
        <script>
            var configJS = {
                'load_attribute_add_ajax' : '{{ url({"for": "load_attribute_add_ajax"}) }}',
                'load_attribute_edit_ajax': '{{ url({"for": "load_attribute_edit_ajax"}) }}'
            };

            var t     = {};
            var check = false;
            var tryOk = true;

            setInterval(function () {
                $.get('/', function () {
                    if (check) {
                        t.reset();
                        check = false;
                    }

                    if (!tryOk) {
                        $.toast({
                            text: "Kết nối thành công!",
                            bgColor: '#3c763d',
                            textColor: '#eee',
                            allowToastClose: close,
                            hideAfter: 5000,
                            stack: 2,
                            textAlign: 'left',
                            position: 'top-center'
                        });
                        tryOk = true
                    }

                }).fail(function() {
                    t = $.toast({
                        text : "Kết nối Internet của bạn đang có vấn đề, Kiểm tra lại kết nối. Hệ thống đang cố kết nối lại",
                        bgColor : '#FFFB91',
                        textColor : '#000',
                        allowToastClose : close,
                        stack: 0,
                        position : 'top-center',
                        hideAfter: false
                    });
                    tryOk = false;
                    check = true;
                });
            }, 3000);
        </script>
    </head>
    <body>
        {% include 'default/element/macro/_upload.volt' %}
        {% include 'default/element/layout/header.volt' %}

        <div class="main-container" style="margin-top: 0 !important;">
            <div class="navbar-content">
                {% include 'default/element/layout/sidebar.volt' %}
            </div>
            <div class="main-content">
                <div class="container">
                    {% block content %}{% endblock %}
                </div>
            </div>
        </div>

        <div class="footer clearfix">
            <div class="footer-inner">2015 &copy; JINN</div>
            <div class="footer-items">
                <span class="go-top">
                    <i class="clip-chevron-up"></i>
                </span>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                Main.init();
            });
        </script>

        {% block bottom_js %}{% endblock %}
    </body>
</html>
