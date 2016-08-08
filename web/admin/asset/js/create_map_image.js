function MapImage()
{
    this.src = '';
    this.urlAjax = {};
    this.imageWidth = '';
    this.imageHeight = '';
    this.$ = '';
    this.wrap = '';
    this.wrapImage = '';
    this.imageMap = '';
    this.addNewBtn = '';
    this.tableResult = '';
    this.select = '';

    this.addnew = true;
    this.times = 0;
    this.resultMap = {};
    this.form = '';
    this.createImage = function(){

        var sizeStyle = 'style="width: '+ this.imageWidth +'px; height: '+ this.imageHeight +'px; margin: auto; position: relative"';
        var htmlInt = this.$('.block-init').html();
        this.$('.block-init').html('');
        var html = '' +
            '<div class="wrap-map" '+ sizeStyle +'>' +
                '<img usemap="#boxmap" id="map-image" src="'+ this.src +'" '+ sizeStyle +' />' +
                '<map id="map-tag" name="boxmap">' +
                    htmlInt +
                '</map>' +
                '<div class="block-label"></div>' +
            '</div>';

        this.wrap.find('.entry').html(html);
        this.wrapImage = this.wrap.find('.wrap-map');

        this.imageMap = this.wrap.find('img#map-image');

        this.initMap();
    };

    this.getImageSize = function(url, el, $){
        var parent = this;
        this.$ = $;
        this.wrap = this.$(el);
        this.src = url;

        var img = new Image;
        img.src=this.src;
        img.onload=function(){
            parent.imageWidth = this.width;
            parent.imageHeight = this.height;
            parent.createImage();
        };
    };

    this.initMap = function () {
        var parent = this;

        parent.refresh();
        parent.wrapImage.on('click', function (target) {
            var elm = $(this);
            var xPos = target.pageX - $(elm).offset().left;
            var yPos = target.pageY - $(elm).offset().top;

            if (parent.addnew == true) {
                parent.wrapImage.find('map').append('<area data-item_id="" data-maphilight=\'{"strokeColor":"a94442","strokeWidth":1,"fillColor":"ff0000","fillOpacity":0.6}\' shape="poly" coords="">');
                parent.addnew = false;
                parent.resultMap = {
                    "data-maphilight": {
                        "strokeColor": "blue",
                        "strokeWidth": 1,
                        "fillColor": "4caf50",
                        "fillOpacity": 0.6
                    },
                    "shape": "poly",
                    "coords": ""
                };
            }
            var val = parent.wrapImage.find('map').find('area').last().attr('coords');
            if (val && val != "undefined") {
                val += "," + xPos + "," + yPos
            } else {
                val = xPos + "," + yPos
            }

            parent.times++;

            localStorage.setItem('undo_' + parent.times, val);

            parent.resultMap.coords = val;
            parent.wrapImage.find('map').find('area').last().attr('coords', val);
            parent.form.find('[name="point"]').val(JSON.stringify(parent.resultMap));
            parent.wrapImage.find('map').find('area.active').removeClass('active');
            parent.imageMap.maphilight();
        });

        parent.addNewBtn.on('click', function (event) {
            event.preventDefault();
            parent.addnew = true;

            var dataForm = parent.form.serialize();
            parent.$.ajax({
                url: parent.urlAjax.add,
                data: dataForm,
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.status == 200) {
                        $.toast({
                            text: "Tạo thành công Maplink",
                            showHideTransition: 'slide',
                            bgColor: '#3c763d',
                            textColor: '#eee',
                            allowToastClose: close,
                            hideAfter: 5000,
                            stack: 2,
                            textAlign: 'left',
                            position: 'top-right'
                        });
                        var html = '' +
                            '<tr>' +
                                '<td class="text-danger">' + data.result.id + '</td>' +
                                '<td>' +
                                    '<form action="' + parent.urlAjax.update + '">' +
                                        '<input type="hidden" name="id" value="' + data.result.id + '">' +
                                        '<select onchange="formSubmit(this);"  name="item_id">' + parent.select + '</select>' +
                                    '</form>' +
                                '</td>' +
                                '<td>' +
                                    '<a href="" onclick="return deleteMapImage('+ data.result.id +', this);" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa">' +
                                        '<i class="fa fa-times fa fa-white"></i>' +
                                    '</a>' +
                                '</td>' +
                            '</tr>';
                        parent.wrapImage.find('map').find('area').last().attr({'id': 'point_' + data.result.id, 'data-item_id': data.result.item_id});
                        parent.form.trigger('reset');
                        parent.tableResult.find('tbody').prepend(html);
                        parent.refresh();
                    } else {
                        var mess = '';
                        $.each(data.result, function (index, vl) {
                            mess += '- ' + vl + '<br>';
                        });
                        $.toast({
                            text: "<p><b>Cập nhật không thành công</b></p> <br>" + mess,
                            showHideTransition: 'slide',
                            bgColor: '#a94442',
                            textColor: '#eee',
                            allowToastClose: close,
                            hideAfter: 5000,
                            stack: 2,
                            textAlign: 'left',
                            position: 'top-right'
                        });
                    }
                }
            })
        });

        parent.$(document).keydown(function(e){
            if (e.keyCode == 90 && e.ctrlKey){

                if (localStorage.getItem('undo_'+parent.times) != null) {
                    if ( localStorage.getItem('undo_'+parent.times).split(',').length == 2 ) {
                        parent.wrapImage.find('map').find('area').last().remove();
                        parent.addnew = true;
                    }
                }

                localStorage.removeItem('undo_'+parent.times);
                if (parent.times > 0) {
                    parent.times --;
                }
                var val = localStorage.getItem('undo_'+parent.times);

                parent.wrapImage.find('map').find('area').last().attr('coords', val);
                parent.resultMap.coords = val;
                parent.form.find('[name="point"]').val(JSON.stringify(parent.resultMap));
                parent.wrapImage.find('map').find('area.active').removeClass('active');
                parent.imageMap.maphilight();
            }
        });
    };

    this.deleteMap = function (id) {
        this.wrapImage.find('map').find('area#point_' + id).remove();
        this.refresh();
    };

    this.changeItemId = function (id, itemId) {
        this.wrapImage.find('map').find('area#point_' + id).attr({'data-item_id': itemId});
        this.refresh();
    };

    this.refresh = function () {
        var parent = this;
        parent.wrapImage.find('map');
        parent.$('.block-label').html('');
        parent.wrapImage.find('map').find('area').each(function () {
            parent.$el = parent.$(this);
            var coords = $.trim(parent.$el.attr('coords')).split(','),
                maxX = parseInt(coords[0]),
                minX = parseInt(coords[0]),
                maxY = parseInt(coords[1]),
                minY = parseInt(coords[1]);

            for (i = 0; i < coords.length; i ++) {
                var vl = parseInt(coords[i]);
                if (i % 2 == 0){
                    // X
                    maxX = (vl > maxX) ? vl : maxX;
                    minX = (vl < minX) ? vl : minX;
                } else {
                    // Y
                    maxY = (vl > maxY) ? vl : maxY;
                    minY = (vl < minY) ? vl : minY;
                }
            }
            var centerX = minX + ((maxX - minX) / 2);
            var centerY = minY + ((maxY - minY) / 2);
            parent.$('.block-label').append('<span style="text-shadow: 0 0 1px rgba(0,0,0,0.7); position: absolute; color: #fff; font-size: 18px; left: '+ centerX +'px; top: '+ centerY +'px" id="for_'+ parent.$el.attr('id') +'">'+ parent.$el.attr('id').replace('point_', '') +'</span>');
        });
        this.imageMap.maphilight();
    };

    return this;
}

var mapImage;
$(document).ready(function () {
    mapImage = new MapImage();

    mapImage.form = $(form);
    mapImage.addNewBtn = $('.add-map');
    mapImage.urlAjax = url;
    mapImage.tableResult = $(table);
    mapImage.select = '';
    var selectOption = JSON.parse(select);
    $.each(selectOption, function(index, el){
        if (index == '') {
            mapImage.select += '<option selected value="'+ index +'">'+ el +'</option>';
        } else {
            mapImage.select += '<option value="'+ index +'">'+ el +'</option>';
        }

    });

    mapImage.getImageSize(linkMapImage, '#' + wrapImage, $);

    $('select').each(function () {
        if ($(this).data('selected') > 0) {
            $(this).val($(this).data('selected'));
        }
    });
});

function formSubmit(el) {
    $(document).ready(function () {
        $el = $(el);
        var _url = $el.closest('form').attr('action');
        var data = $el.closest('form').serialize();

        $.ajax({
            url: _url,
            data: data,
            dataType: 'json',
            type: 'post',
            success: function(data){
                if (data.status == 200) {
                    $.toast({
                        text : "Cập nhật thành công Maplink",
                        bgColor : '#3c763d',
                        textColor : '#eee',
                        allowToastClose : close,
                        position : 'top-right'
                    });
                    mapImage.changeItemId(data.result.id, data.result.item_id);
                } else {
                    $(el).val('');
                    $.toast({
                        text : "Cập nhật KHÔNG thành công Maplink <br> <b>  - "+ data.message +"</b>",
                        bgColor : '#C83A2A',
                        textColor : '#eee',
                        allowToastClose : close,
                        position : 'top-right'
                    });
                }
            }
        })
    });
}

function deleteMapImage(id, el) {
    $(document).ready(function () {
        $.ajax({
            url: url.delete,
            data: {id: id},
            dataType: 'json',
            type: 'post',
            success: function(data){
                if (data.status == 200) {
                    $.toast({
                        text: "Xóa thành công Maplink",
                        bgColor: '#3c763d',
                        textColor: '#eee',
                        allowToastClose: close,
                        position: 'top-right'
                    });
                    console.log(data);

                    $(el).closest('tr').fadeOut('fast', function () {
                        $(this).remove();
                    });

                    mapImage.deleteMap(id);
                } else {
                    var mess = '';
                    $.each(data.result, function (index, vl) {
                        mess += '- ' + vl + '<br>';
                    });
                    $.toast({
                        text: "<p><b>Xóa không thành công</b></p> <br>" + mess,
                        showHideTransition: 'slide',
                        bgColor: '#a94442',
                        textColor: '#eee',
                        allowToastClose: close,
                        hideAfter: 5000,
                        stack: 2,
                        textAlign: 'left',
                        position: 'top-right'
                    });
                }
            }
        });
    });

    return false;
}