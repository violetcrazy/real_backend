<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên
    </label>
    <div class="col-sm-5">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Trạng thái
    </label>
    <div class="col-sm-5">
        {{ form.render('status', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
    </div>
</div>

<input type="hidden" id="asset_choose_image_url" value="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" />



<div class="form-group">
    <div class="col-sm-12 text-right">
        <button class="btn btn-primary add-gallery" data-callback="createGallery">Thêm hình ảnh</button>
    </div>
    <div class="clearfix"></div>
</div>

<table class="table table-striped table-bordered table-hover" id="list-gallery">
    <thead>
    <tr>
        <th>Hình ảnh</th>
        <th>Tên file</th>
        <th>Mô tả</th>
        <th>Liên kết</th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    {% if imageList is defined %}
        {% for image in imageList %}
            <tr>
                <td>
                    <img height="60px;" src="{{ image.src }}">
                    <input type="hidden" name="image[{{ loop.index }}][src]" value="{{ image.src }}">
                    <input type="hidden" name="image[{{ loop.index }}][name]" value="{{ image.name }}">
                </td>
                <td>
                    {{ image.name }}
                </td>
                <td class="type">
                    <textarea name="image[{{ loop.index }}][description]" id="" rows="3" class="form-control">{{ image.description }}</textarea>
                </td>
                <td>
                    <input type="text" name="image[{{ loop.index }}][link]" class="form-control" value="{{ image.link }}">
                </td>
                <td class="center">
                    <a class="btn btn-xs btn-bricky tooltips delete-row" data-placement="top" data-original-title="Remove"><i class="fa fa-times fa fa-white"></i></a>
                </td>
            </tr>
        {% endfor %}
    {% endif %}
    </tbody>
</table>
<div class="clearfix"></div>


<script>
    $(document).ready(function() {
    }).on('change', 'td.type select', function(){
        var _vl = $(this).val();
        $.each($('td.type select'), function (index, el) {
            if ($(el).val() == _vl && _vl == 1) {
                $(el).val('2');
            }
        });
        $(this).val(_vl);
    });

    //Callback on upload
    function createGallery(result) {
        $(document).ready(function(){
            $.each(result.data, function(index, value){
                $('#list-gallery').find('tbody').prepend(templateGallery(value, index));
            });
            $.fancybox.close();
        });
    }
    function templateGallery(item, index) {
        var uniq = 'id' + index + (new Date()).getTime();

        var html = '\
            <tr>\
                <td>\
                    <img height="60px;" src="'+ item.link +'">\
                    <input type="hidden" name="image['+ uniq +'][name]" value="'+ item.path +'">\
                    <input type="hidden" name="image['+ uniq +'][src]" value="'+ item.link +'">\
                </td>\
                <td>\
                    '+ item.path +'\
                </td>\
                <td class="type">\
                    <textarea name="image['+ uniq +'][description]" id="" rows="3" class="form-control"></textarea>\
                </td>\
                <td>\
                    <input name="image['+ uniq +'][link]" type="text" class="form-control">\
                </td>\
                <td class="center">\
                    <a href="#" class="btn btn-xs btn-bricky tooltips delete-row" data-placement="top" data-original-title="Remove"><i class="fa fa-times fa fa-white"></i></a>\
                </td>\
            </tr>';
        return html;
    }

</script>
{% block bottom_js %}
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/banner/upload_image.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true
            });
        });
    </script>
{% endblock %}