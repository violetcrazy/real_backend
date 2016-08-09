<script type="text/javascript">
    function getFile(option)
    {
        console.log(option.meta.inputreceive);
        $('#' + option.meta.inputreceive).val(option.data[0].path);
        $('#view_' + option.meta.inputreceive).attr('src', option.data[0].link);
        $.fancybox.close();
    }

    function getFileMultiple(option)
    {
        if (option.data) {
            for (index in option.data) {
                $('.preview_'+option.meta.inputreceive).prepend(templateHTMLPreviewUpload(option.data[index], option.meta.inputreceive));
            }
        }

        $.fancybox.close();
    }
    function templateHTMLPreviewUpload(data, name)
    {
        return '<div class="thumbnail thumbnail-upload-tools inlineB item">\
                <div class="clear-image-multiple  clip-close" data-clear="' + name + '"></div>\
                <img height="100px;" src="' + data.link + '" alt="" id="view_' + name + '" />\
                <input name="' + name + '[]" value="' + data.path + '" type="hidden" />\
            </div>';
    }

    $(document).ready(function() {
        $('.btn-upload-tools').on('click', function(event) {
            event.preventDefault();
            var elGetData = $(this).attr('data-sendValue');

            var url = url_uload_media + '?callback=getFile&input-receive=' + elGetData;
            $.fancybox({
                'width': '90%',
                'height': '90%',
                'autoScale': true,
                'transitionIn': 'fade',
                'transitionOut': 'fade',
                'type': 'iframe',
                'href': url
            });
        });

        $('.btn-upload-multiple').on('click', function(event) {
            event.preventDefault();
            var elGetData = $(this).attr('data-sendValue');
            var callBack = $(this).attr('data-callback');
            if (typeof callBack == 'undefined' || callBack == '') {
                callBack = 'getFileMultiple';
            }

            var url = url_upload_multiple + '?callback='+ callBack +'&input-receive=' + elGetData;
            $.fancybox({
                'width': '90%',
                'height': '90%',
                'autoScale': true,
                'transitionIn': 'fade',
                'transitionOut': 'fade',
                'type': 'iframe',
                'href': url
            });
        });

        $('.clear-image').on('click', function(event) {
            event.preventDefault();
            $this = $(this);
            var field = $this.data('clear');
            $('#' + field).val('');
            $('#view_' + field).attr('src', url_thumbnail_default);
        });
    }).on('click', '.clear-image-multiple', function(event) {
        event.preventDefault();
        $this = $(this);
        $this.closest('.thumbnail-upload-tools').fadeOut('fast', function() {
            $(this).remove();
        });
    });
</script>

{%- macro templateUpload(id, url = false, value = false)  %}
    <div class="thumbnail thumbnail-upload-tools">
        <div class="clear-image clip-close" data-clear="{{ id }}"></div>

        {% if value == false %}
            <img height="100px;" src="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" alt="" id="view_{{ id }}" />
        {% else %}
            <img height="100px;" src="{{ url }}" alt="" id="view_{{ id }}" />
        {% endif %}
    </div>

    <div class="input-group">
        <input readonly type="text" class="form-control" name="{{ id }}" id="{{ id }}" value="{{ value }}" />
        <span class="input-group-btn">
            <button class="btn btn-primary btn-upload-tools" data-sendValue="{{ id }}" type="button">Upload</button>
        </span>
    </div>
{%- endmacro  %}