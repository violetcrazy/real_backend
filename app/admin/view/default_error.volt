<!DOCTYPE html>
<!--[if IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
    <head>
        <meta charset="utf-8" />
        <title>{% block title %}{% endblock %}</title>
        <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="robots" content="noindex, nofollow" />

        {% include 'default/element/layout/asset.volt' %}
    </head>
    <body class="login example2">
        <div class="main-login col-sm-4 col-sm-offset-4">
            <div class="logo"></div>
            {% block content %}{% endblock %}

            <div class="copyright">
                2014 &copy; JINN
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                Main.init();
            });
        </script>
    </body>
</html>