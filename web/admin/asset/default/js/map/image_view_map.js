    $document = $(document);
    var addnew = true,
        times = 0,
        resultMap = {};

    $(window).load(function(){
        var emW = $('.block-map img').width();
        var emH = $('.block-map img').height();
        $('.block-map').width(emW).height(emH).css("margin","auto");
    });

    $document.ready(function(){

        $('.map').maphilight();

        $('.block-map').on('click',function(e){

            if ( paint ) {

                var elm = $(this);
                var xPos = e.pageX - $(elm).offset().left;
                var yPos = e.pageY - $(elm).offset().top;
                if (addnew == true) {
                    $('#map-tag').append('<area data-maphilight=\'{"strokeColor":"ff0000","strokeWidth":1,"fillColor":"ff0000","fillOpacity":0.6}\' shape="poly" coords="">');
                    addnew = false;
                    resultMap = {
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
                var val = $('#map-tag').find('area').last().attr('coords');
                if (val && val != undefined) {
                    val += "," + xPos + "," + yPos
                } else {
                    val += xPos + "," + yPos
                }

                times++;
                localStorage.setItem('undo_' + times, val);
                resultMap.coords = val;
                $('#image_view').val(JSON.stringify(resultMap));

                $('#map-tag').find('area').last().attr('coords', val);

                $('.map').maphilight();
                $('#map-tag').find('area.active').removeClass('active');
            }
        });

        $('.reset-map').click(function(e){
            e.preventDefault();
            localStorage.clear();
            $('#map-tag').find('area').remove();
            $('#image_view').val('');
            $('.map').maphilight();
            times = 0;
            addnew = true;
            resultMap = {};
        });

    })
    .on('click','area',
        function(){
            $this = $(this);
            var id_block = $this.data('id');
            //var name = $this.attr('title');
            //$('.mess-info').html('<b class="text-danger">'+ name +'</b>');
            //return false;
        }
    )
    .on('mouseover','area',
        function(){
            var id_block = $(this).data('id');
            $('#'+id_block).addClass('active');
        }
    )
    .on('mouseout','area',
        function(){
            var id_block = $(this).data('id');
            $('#'+id_block).removeClass('active');
        }
    );

    $document.on('click','area',function(){
        $this = $(this);
        var id_block = $this.data('id');
    });

    $document.keydown(function(e){
        if (e.keyCode == 90 && e.ctrlKey){

            times --;
            var val = localStorage.getItem('undo_'+times);
            localStorage.removeItem('undo_'+times);

            resultMap.coords = val;
            $('#map-tag').find('area').last().attr('coords',val);

            $('#image_view').val(JSON.stringify(resultMap));
            $('.map').maphilight();
        }
    });