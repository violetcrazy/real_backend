{% set getMapImagePosition = getMapImagePosition() %}
{% set getMapImageType = getMapImageType() %}

<div id="form_tab3_error_message" class="alert alert-danger" style="display: none;"></div>
<div class="form-group">
    <div class="col-sm-12 text-right">
        <button class="btn btn-primary btn-upload-multiple" data-sendValue="gallery" data-callback="uploadDone">Thêm hình ảnh</button>
    </div>
    <div class="clearfix"></div>
</div>

<table class="table table-striped table-bordered table-hover" id="list-gallery">
    <thead>
    <tr>
        <th>Hình ảnh</th>
        <th>Tên file</th>
        <th>Vị trí hiển thị</th>
        <th>Tầng</th>
        <th>Kiểu hiển thị</th>
        <th></th>
    </tr>
    </thead>

    <input type="hidden" value="false" name="galleries[]">
    <tbody>
    {% if mapImage is defined and mapImage|length > 0 %}
        {% for image in mapImage %}
            <tr>
                <td>
                    <img height="60px;" src="{{ config.cdn.dir_upload ~ image.image }}">
                    <input type="hidden" name="galleries[{{ loop.index }}][image]" value="{{ image.image }}">
                    <input type="hidden" name="galleries[{{ loop.index }}][id]" value="{{ image.id }}">
                </td>
                <td>
                    {{ getNameImage(image.image) }}
                </td>
                <td class="type">
                    <select class="form-control" name="galleries[{{ loop.index }}][type]" id="">
                        {% for key, item in getMapImageType %}
                            <option {{ image.type == key ? 'selected' :'' }} value="{{ key }}">{{ item }}</option>
                        {% endfor %}
                    </select>
                </td>
                <td>
                    {% if (image.type == constant('\ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_FLOOR')) %}
                        <select class="form-control" name="galleries[{{ loop.index }}][floor]" id="">
                            <option value="">Chọn tầng</option>
                            {% for item in 1..result.floor_count %}
                                <option {{ image.floor == item ? 'selected' :'' }} value="{{ item }}">{{ item }}</option>
                            {% endfor %}
                        </select>
                    {% endif %}
                </td>
                <td>
                    <select class="form-control" name="galleries[{{ loop.index }}][position]" id="">
                        {% for key, item in getMapImagePosition %}
                            <option {{ image.position == key ? 'selected' :'' }} value="{{ key }}">{{ item }}</option>
                        {% endfor %}
                    </select>
                </td>
                <td class="center">
                    {% if (image.position == constant('\ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_MAP') and image.floor > 0) %}
                        {% set paramsEdit = http_build_query({'map_image_id': image.id}) %}
                        <a href="{{ url({'for': 'map_image_index', 'query' : '?' ~ paramsEdit}) }}" class="btn btn-xs btn-success tooltips" data-placement="top" data-original-title="Vẽ map link"><i class="fa fa-pencil fa fa-white"></i></a>

                        <a href="{{ url({'for': 'block_map_image_clone', 'query': '?' ~ http_build_query({'map_image_id': image.id, 'block_id': image.item_id, 'floor_count': image.floor })}) }}" class="btn btn-xs btn-teal tooltips" data-original-title="Sao chép" data-placement="top">
                            <i class="clip-copy-2"></i>
                        </a>
                    {% endif %}

                    <a class="btn btn-xs btn-bricky tooltips delete-row" data-placement="top" data-original-title="Remove"><i class="fa fa-times fa fa-white"></i></a>
                </td>
            </tr>
        {% endfor %}
    {% endif %}
    </tbody>
</table>
<div class="clearfix"></div>
<script>
    $(document).ready(function(){
        $('table').on('click', '.delete-row', function (event) {
            event.preventDefault();
            deleteRow($(this));
        });
    }).on('change', 'td.type select', function(){
        var _vl = $(this).val();
        $.each($('td.type select'), function (index, el) {
            if ($(el).val() == _vl && _vl == 1) {
                $(el).val('2');
            }
        });
        $(this).val(_vl);
    });

    function uploadDone(data){
        if (data.result) {
            $.each(data.result, function(index, value){
                $('#list-gallery').find('tbody').prepend(templateTr(value, index));
            });
        }
        $.fancybox.close();
    }

    function templateTr(item, index) {
        var uniq = 'id' + index + (new Date()).getTime();

        var html = '\
            <tr>\
                <td>\
                    <img height="60px;" src="'+ item.thumbnail +'">\
                    <input type="hidden" name="galleries['+ uniq +'][image]" value="'+ item.relative_path + '/' + item.name +'">\
                </td>\
                <td>\
                    '+ item.name +'\
                </td>\
                <td class="type">\
                    <select class="form-control" name="galleries['+ uniq +'][type]" id="">\
                        {% for key, item in getMapImageType %}<option value="{{ key }}">{{ item }}</option>{% endfor %}\
                    </select>\
                </td>\
                <td></td>\
                <td>\
                    <select class="form-control" name="galleries['+ uniq +'][position]" id="">\
                        {% for key, item in getMapImagePosition %}<option value="{{ key }}">{{ item }}</option>{% endfor %}\
                    </select>\
                </td>\
                <td class="center">\
                    <a href="#" class="btn btn-xs btn-bricky tooltips delete-row" data-placement="top" data-original-title="Remove"><i class="fa fa-times fa fa-white"></i></a>\
                </td>\
            </tr>';
        return html;
    }

    function deleteRow(el) {
        el.closest('tr').fadeOut('fast', function () {
            $(this).remove();
        })
    }

</script>

