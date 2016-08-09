{% set getMapImagePosition = getMapImagePosition() %}
{% set getMapImageType = getMapImageType() %}

<div id="form_tab3_error_message" class="alert alert-danger" style="display: none;"></div>
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
        <th>Vị trí hiển thị</th>
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
                    <img height="60px;" src="{{ config.cdn.file_url ~ image.image }}">
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
                    <select class="form-control" name="galleries[{{ loop.index }}][position]" id="">
                        {% for key, item in getMapImagePosition %}
                            <option {{ image.position == key ? 'selected' :'' }} value="{{ key }}">{{ item }}</option>
                        {% endfor %}
                    </select>
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
                $('#list-gallery').find('tbody').prepend(templateGallery(value));
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
                    <input type="hidden" name="galleries['+ uniq +'][image]" value="'+ item.path +'">\
                </td>\
                <td>\
                    '+ item.path +'\
                </td>\
                <td class="type">\
                    <select class="form-control" name="galleries['+ uniq +'][type]" id="">\
                        {% for key, item in getMapImageType %}<option value="{{ key }}">{{ item }}</option>{% endfor %}\
                    </select>\
                </td>\
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

</script>

