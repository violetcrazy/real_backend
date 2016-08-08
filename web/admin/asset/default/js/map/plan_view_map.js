    $document = $(document);
    var addnew = true,
        times = 0,
        resultMap = {};
    $document.ready(function(){
        var emW = $('.block-map-plan-view img').width();
        var emH = $('.block-map-plan-view img').height();
        $('.block-map-plan-view').width(emW).height(emH).css("margin","auto");
        $('.map-plan-view').maphilight();

        $('.block-map-plan-view').on('click',function(e){
            var elm = $(this);
            var xPos = e.pageX - $(elm).offset().left;
            var yPos = e.pageY - $(elm).offset().top;
            if (addnew == true){
                $('#map-tag-plan-view').append('<area data-maphilight=\'{"strokeColor":"blue","strokeWidth":1,"fillColor":"4caf50","fillOpacity":0.6}\' shape="poly" coords="">');
                addnew = false;
                resultMap = {
                    "data-maphilight": {
                            "strokeColor":"blue",
                            "strokeWidth":1,
                            "fillColor":"4caf50",
                            "fillOpacity":0.6
                        },
                    "shape": "poly",
                    "coords": ""
                };
            }
            var val = $('#map-tag-plan-view').find('area').last().attr('coords');
            if(val &&  val != undefined){
                val += ","+xPos+","+yPos
            } else{
                val += xPos+","+yPos
            }

            times ++;
            localStorage.setItem( 'undo_'+times , val);

            // print data to input
            resultMap.coords = val;
            $('#plan_view').val(JSON.stringify(resultMap));

            $('#map-tag-plan-view').find('area').last().attr('coords',val);

            $('.map-plan-view').maphilight();
            $('#map-tag').find('area.active').removeClass('active');

        });

        $('.reset-map-plan-view').click(function(e){
            e.preventDefault();
            localStorage.clear();
            $('#map-tag-plan-view').find('area').remove();
            $('#plan_view').val('');
            $('.map-plan-view').maphilight();
            times = 0;
            addnew = true;
            resultMap = {};
        });

    });

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
            $('#map-tag-plan-view').find('area').last().attr('coords',val);

            $('#plan_view').val(JSON.stringify(resultMap));
            $('.map-plan-view').maphilight();
        }
    });