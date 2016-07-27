$(document).ready(function() {
    var process = false;

    var province_id = $('#province_id').val(),
        district_id = $('#district_id').attr('data-id'),
        action = $('#province_id').attr('data-action'),
        link = $('#province_id').attr('data-url');

    if (province_id != '' && action == 'true') {
        if (!process) {
            process = true;
            $.ajax({
                'url': link,
                'type': 'GET',
                'data': {'province_id': province_id, 'district_id': '' + district_id + ''},
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        $('#district_id').html(response);
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    }

    $(document).on('change', '#province_id', function() {
        if (!process) {
            process = true;

            var province_id = $(this).val();
            var link = $(this).attr('data-url');
            $.ajax({
                "url": link,
                "type": "GET",
                "data": {"province_id": province_id},
                "success": function(response) {
                    if (typeof response != "undefined") {
                        $('#district_id').html(response);
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    });
});