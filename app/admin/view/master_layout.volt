<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ADmin</title>

        <link rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/bootstrap/css/bootstrap.min.css?' ~ config.asset.version }}">

        <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/jquery.js?' ~ config.asset.version }}"></script>
        <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/bootstrap/js/bootstrap.min.js?' ~ config.asset.version }}"></script>
    </head>
    <body>
        <hr />
        <div class="container-fluid">
            {% block content %}{% endblock %}
        </div>
    </body>
</html>