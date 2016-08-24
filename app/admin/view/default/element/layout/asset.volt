<link type="image/x-icon" rel="shortcut icon" href="{{ config.asset.backend_url ~ 'img/favicon.png?' ~ config.asset.version }}" />
<link type="image/x-icon" rel="icon" href="{{ config.asset.backend_url ~ 'img/favicon.png?' ~ config.asset.version }}" />

<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/bootstrap/css/bootstrap.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/font-awesome/css/font-awesome.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'fonts/style.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'css/main.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'css/main-responsive.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/iCheck/skins/all.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/perfect-scrollbar/src/perfect-scrollbar.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'css/theme_black_and_white.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/select2/select2.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/tag_auto/fm.tagator.jquery.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/datepicker/css/datepicker.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/bootstrap-fileupload/bootstrap-fileupload.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/toast/jquery.toast.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" media="print" href="{{ config.asset.backend_url ~ 'css/print.min.css?' ~ config.asset.version }}" />
<link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url}}plugins/fancybox/jquery.fancybox.css?{{ config.asset.version }}" />
<!--[if IE 7]>
    <link type="text/css" rel="stylesheet" href="{{ config.asset.backend_url ~ 'plugins/font-awesome/css/font-awesome-ie7.min.css?' ~ config.asset.version }}" />
<![endif]-->

<script type="text/javascript">
	var url_uload_media       = '{{ config.cdn.file_upload }}';
	var url_upload_multiple   = '{{ config.cdn.file_upload }}';
    var url_thumbnail_default = '{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}';

    var file_upload = '{{ config.cdn.file_upload }}';
    var file_url    = '{{ config.cdn.file_url }}';
</script>

<!--[if lt IE 9]>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/respond.min.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/excanvas.min.js?' ~ config.asset.version }}"></script>
<![endif]-->

<script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/jquery.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/jquery_number.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/jquery-ui/jquery-ui-1.10.2.custom.min.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/bootstrap/js/bootstrap.min.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/blockUI/jquery.blockUI.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/iCheck/jquery.icheck.min.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/perfect-scrollbar/src/jquery.mousewheel.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/perfect-scrollbar/src/perfect-scrollbar.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/less/less-1.5.0.min.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/jquery-cookie/jquery.cookie.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/select2/select2.min.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/tag_auto/fm.tagator.jquery.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/datepicker/js/bootstrap-datepicker.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/main.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/bootstrap-fileupload/bootstrap-fileupload.min.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/fancybox/jquery.fancybox.js?' ~ config.asset.version }}"></script>
<script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/toast/jquery.toast.min.js?' ~ config.asset.version }}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.format-number').number(true, 0, '.' , ',');
        $('.format-number2').number(true, 0, '' , '');
    });

    function saveTab($, key)
    {
        if (typeof(Storage) !== "undefined") {
            $('.nav-tabs a').click(function () {
                var href = $(this).attr('href');
                localStorage.setItem(key, href);
            });

            var active = localStorage.getItem(key);
            $('[href="' + active + '"]').trigger('click');
        } else {
            console.log('Sorry! No Web Storage support.');
        }
    }
</script>
