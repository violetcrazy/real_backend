$(document).ready(function() {
    var process = false;

    $('.block-upload-img .thumbnail-img img').click(function() {
        $(this).next('input').trigger('click');
    });

    $('.block-upload-img .thumbnail-img input').change(function() {
        if (!process) {
            process = true;

            var file_data = $(this).prop('files')[0];
            var type_upload = $('#type_upload').val();
            var form_data = new FormData();

            form_data.append('file', file_data);
            form_data.append('type_upload', type_upload);

            $this = $(this);
            $this.prev('img').attr('src', '');
            $this.closest('.thumbnail-img').addClass('loadding-by-ajax');

            $.ajax({
                'url': $('#load_upload_image_ajax').val(),
                'type': 'POST',
                'dataType': 'json',
                'cache': false,
                'contentType': false,
                'processData': false,
                'data': form_data,
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        console.log(response);

                        $this.closest('.thumbnail-img').removeClass('loadding-by-ajax');
                        $this.closest('.thumbnail-img').find('.fa').fadeIn();
                        $this.next('input').val(response.result.image);
                        $this.prev('img').attr('src', response.result.image);
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    });

    $('.block-upload-img-view .thumbnail-img img').click(function() {
        $(this).next('input').trigger('click');
    });

    $('.block-upload-img-view .thumbnail-img input').change(function() {
        if (!process) {
            process = true;

            var file_data = $(this).prop('files')[0];
            var type_upload = $('#type_upload').val();
            var form_data = new FormData();

            form_data.append('file', file_data);
            form_data.append('type_upload', type_upload);

            $this = $(this);
            $this.prev('img').attr('src', '');
            $this.closest('.thumbnail-img').addClass('loadding-by-ajax');

            $.ajax({
                'url': $('#load_upload_image_ajax').val(),
                'type': 'POST',
                'dataType': 'json',
                'cache': false,
                'contentType': false,
                'processData': false,
                'data': form_data,
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').removeClass('loadding-by-ajax');
                        $this.closest('.thumbnail-img').find('.fa').fadeIn();
                        $this.next('input').val(response.result.image);
                        $this.prev('img').attr('src', response.result.default_thumbnail_url);
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    });

    $('.block-upload-plan-view .thumbnail-img img').click(function() {
        $(this).next('input').trigger('click');
    });

    $('.block-upload-plan-view .thumbnail-img input').change(function() {
        if (!process) {
            process = true;

            var file_data = $(this).prop('files')[0];
            var folder = $('#folder_upload').val();
            var w = $('#upload-image-view-size-x').val();
            var h = $('#upload-image-view-size-y').val();
            var form_data = new FormData();

            form_data.append('file', file_data);
            form_data.append('folder', folder);
            form_data.append('w', w);
            form_data.append('h', h);

            $this = $(this);
            $this.prev('img').attr('src', '');
            $this.closest('.thumbnail-img').addClass('loadding-by-ajax');

            $.ajax({
                'url': $('#load_upload_image_ajax').val(),
                'type': 'POST',
                'dataType': 'json',
                'cache': false,
                'contentType': false,
                'processData': false,
                'data': form_data,
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').removeClass('loadding-by-ajax');
                        $this.closest('.thumbnail-img').find('.fa').fadeIn();
                        $this.next('input').val(response.result.image);
                        $this.prev('img').attr('src', response.result.default_thumbnail_url);
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    });

    $('.block-upload-img').on('click', '.delete-img', function() {
        if (!process) {
            process = true;

            var image = $(this).closest('.thumbnail-img').find('.input_image').val();
            var folder = $('#folder_upload').val();
            $this = $(this);

            $.ajax({
                'url': $('#load_delete_image_ajax_url').val(),
                'type': 'GET',
                'data': {'image': image, 'folder': folder},
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').find('.fa').fadeOut();
                        $this.closest('.thumbnail-img').find('input[type="file"]').val('');
                        $this.closest('.thumbnail-img').find('.input_image').val('');
                        $this.closest('.thumbnail-img').find('img').attr('src', $('#asset_choose_image_url').val());
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    });

    $('.box-show-gallery').on('click', '.delete-img', function() {
        if (!process) {
            process = true;

            var image = $(this).closest('.thumbnail-img').find('.input_image').val();
            var folder = $('#folder_upload').val();
            $this = $(this);

            $.ajax({
                'url': $('#load_delete_image_ajax_url').val(),
                'type': 'GET',
                'data': {'image': image, 'folder': folder},
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').remove();
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    });

    $('.block-upload-img-view').on('click', '.delete-img', function() {
        if (!process) {
            process = true;

            var image = $(this).closest('.thumbnail-img').find('.input_image').val();
            var folder = $('#folder_upload').val();
            $this = $(this);

            $.ajax({
                'url': $('#load_delete_image_ajax_url').val(),
                'type': 'GET',
                'data': {'image': image, 'folder': folder},
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').find('.fa').fadeOut();
                        $this.closest('.thumbnail-img').find('input[type="file"]').val('');
                        $this.closest('.thumbnail-img').find('.input_image').val('');
                        $this.closest('.thumbnail-img').find('img').attr('src', $('#asset_choose_image_url').val());
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function() {
                process = false;
            });
        }
    });

    $('.block-upload-plan-view').on('click', '.delete-img', function() {
        if (!process) {
            process = true;

            var image = $(this).closest('.thumbnail-img').find('.input_image').val();
            var folder = $('#folder_upload').val();
            $this = $(this);

            $.ajax({
                'url': $('#load_delete_image_ajax_url').val(),
                'type': 'GET',
                'data': {'image': image, 'folder': folder},
                'success': function(response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').find('.fa').fadeOut();
                        $this.closest('.thumbnail-img').find('input[type="file"]').val('');
                        $this.closest('.thumbnail-img').find('.input_image').val('');
                        $this.closest('.thumbnail-img').find('img').attr('src', $('#asset_choose_image_url').val());
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