{% extends 'master_layout.volt' %}

{% block content %}
    <div class="wrap-filenmanager">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs header-area" role="tablist">
            <li role="presentation">
                <a href="#list" aria-controls="list" role="tab" data-toggle="tab" id="view-list">Thư viện ảnh</a></li>
            <li class="active" role="presentation">
                <a href="#upload-tool" aria-controls="upload-tool" role="tab" data-toggle="tab" id="tab-upload">Tải ảnh lên <span class="counter_upload"></span></a></li>
            <li class="" role="presentation">
                <a href="#category-list" aria-controls="category-list" role="tab" data-toggle="tab" id="tab-category">Chuyên mục</a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content body-area">

            <div role="tabpanel" class="tab-pane" id="list">
                <div class="entry-list media-list dropzone">
                    <div class="block-preview">
                        <div class="title">Xem trước</div>
                        <div class="entry-preview">
                        </div>
                    </div>
                    <form action="{{ config.application.api_url }}media-list" id="form-filter-media" class="form-filter-media">
                        <div class="row">
                            <div class="col-xs-6">
                                <select name="category_id" id="category_filter" class="form-control select_category_id">
                                    <option value="-1">Tất cả</option>
                                    {%  if (listMediaTerm['status'] == constant("\ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS") and listMediaTerm['result']|length) %}
                                        {% for item in listMediaTerm['result']  %}
                                            <option  value="{{ item['category_id'] }}">{{ item['name'] }}</option>
                                        {% endfor %}
                                    {% endif %}
                                </select>
                            </div>
                            <div class="col-xs-6">
                            </div>
                            <button class="btn btn-warning">Lọc</button>
                        </div>
                    </form>
                    <div class="wrap-list">
                        <div class="entry-content">
                            {% if listMedia['status'] == constant("\ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS") %}
                                {% for item in listMedia['result']  %}
                                    <div class="form-group dz-preview dz-file-preview" data-id="{{ item['id'] }}">
                                        <div class="item dz-details">
                                            <div class="thumbnail-img"><span class="dz-upload" data-dz-uploadprogress></span>
                                                <img  data-dz-thumbnail src="{{ config.cdn.dir_upload }}thumbnail/{{ item['relative_path'] }}/{{ item['name'] }}" alt="">
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                {% endfor %}
                            {% endif %}
                        </div>
                        <div class="clearfix"></div>
                        <hr />
                        <div><a href="" data-page="1" class="btn btn-default btn-loadmore-media">Tải thêm</a></div>
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane active" id="upload-tool">
                <div class="dropzone media-list">
                    <div class="select-folder-upload">
                        <select name="category_id" id="category_id_upload" class="form-control select_category_id">
                            <option value="">Tất cả</option>
                            {%  if (listMediaTerm['status'] == constant("\ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS") and listMediaTerm['result']|length) %}
                                {% for item in listMediaTerm['result']  %}
                                    <option {{ category_id == item['category_id'] ? 'selected': '' }}  value="{{ item['category_id'] }}">{{ item['name'] }}</option>
                                {% endfor %}
                            {% endif %}
                        </select>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <div class="block-preview">
                        <div class="title">Xem trước</div>
                        <div class="entry-preview">
                        </div>
                    </div>
                    <div class="wrap-list" id="image-view">
                        <div class="text-center click-area">
                            <br>
                            Kéo thả các hình ảnh vào đây hoặc <br>
                            <a class="btn btn-default btn-upload">Tải lên hình ảnh</a>

                        </div>
                        <div class="block-img-upload"></div>
                        <div class="preview-area"></div>
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="category-list">
                <div class="category-list">
                    <div class="dropzone category-media-list">
                        <div class="row wrap-list-cate">
                            <div class="col-xs-6">
                                <form action="" class="form-add-cate panel-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" placeholder="Tên thư mục">
                                <span class="input-group-btn">
                                    <button class="btn btn-danger" type="submit">Thêm</button>
                                </span>
                                    </div><!-- /input-group -->
                                </form>
                            </div>
                            <div class="col-xs-6">
                                <ul class="list-folder">
                                    <li id="mess-ajax"></li>
                                    {%  if (listMediaTerm['status'] == constant("\ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS") and listMediaTerm['result']|length) %}
                                        {% for item in listMediaTerm['result']  %}
                                            <li>
                                                <a>
                                                    {{ item['name'] }}
                                                </a>
                                                <a data-id="{{ item['category_id'] }}" href="" class="delete-category">x</a>
                                            </li>
                                        {% endfor %}
                                    {% endif %}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            var
                    media_list           = '{{ config.application.api_url }}media-list',
                    media_detail         = '{{ config.application.api_url }}media-detail',
                    folder_list          = '{{ config.application.api_url }}folder-list',
                    upload_url           = '{{ url({'for': 'upload_home'}) }}',
                    add_folder           = '{{ url({'for': 'add_folder'}) }}',
                    delete_folder        = '{{ url({'for': 'folder_delete'}) }}',
                    change_folder_upload = '{{ url({'for': 'change_folder_upload'}) }}',
                    add_media            = '{{ url({'for': 'add_media'}) }}';


            $firstLoadListMedia = 0;
            var listCategory = '<option value="">Tất cả</option>'
                            {%  if (listMediaTerm['status'] == constant("\ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS") and listMediaTerm['result']|length) %}
                            {% for item in listMediaTerm['result']  %}
                            + '<option value="{{ item['category_id'] }}">{{ item['name'] }}</option>'
                    {% endfor %}
                    {% endif %};

            $(document)
                    .on('click', '#sendToParent', function(event){
                        event.preventDefault();
                        $this = $(this);
                        var json = $this.data('json');
                        var result = {
                            element: '{{ sendToElement }}',
                            result: json
                        };
                        window.parent.{{ callback ? callback : 'getFile' }}(result);
                    })
                    .on('click','.dz-preview', function (event) {
                        event.preventDefault();
                        $this = $(this);
                        var id = $this.data('id');
                        if (!$this.hasClass('active')) {
                            $('.dz-preview').removeClass('active');
                            $this.addClass('active');
                            $.ajax({
                                url: media_detail,
                                data: {id: id},
                                dataType: 'json',
                                success: function (data) {
                                    if (data.status == 200) {
                                        var html = BuildPreview(data.result);
                                        $this
                                                .closest('.dropzone')
                                                .find('.entry-preview')
                                                .html(html)
                                                .find('select').val(data.result.category_id);
                                    }
                                }
                            });
                        }
                    })
            ;

            function BuildPreview(data){
                var width = data.attribute.width ? data.attribute.width : '--';
                var height = data.attribute.height ? data.attribute.height : '--' ;
                var link = data.attribute.link ? data.attribute.link : '';
                var size = data.size ? parseInt(data.size / 1024) : '';
                var out = '\
                <div class="thumbnail">\
                    <img src="'+ data.thumbnail +'" alt="">\
                </div>\
                <div class="summary-short">\
                    Dài: <b>'+ width +'px</b> <br>\
                    Cao: <b>'+ height +'px</b> <br>\
                    Size: <b>'+ size +' KB</b> <br>\
                    Ngày '+ data.created_at +' <br>\
                    URL: <a target="_blank" href="'+ data.url +'">Xem</a>\
                </div>\
                <div class="clearfix"></div>\
                <form action="" class="form-edit-media">\
                    <input name="id" type="hidden" readonly value="'+ data.id +'" class="form-control">\
                    <label for=""> Tên </label>\
                    <input name="name" type="text" readonly placeholder="Tên hình ảnh" value="'+ data.name +'" class="form-control">\
                    <label for=""> Chuyên mục</label>\
                    <select class="form-control" name="category_id">' + listCategory + '</select>\
                    <label for=""> Tiêu đề </label>\
                    <input name="title" type="text" placeholder="Tiêu đề" value="'+ data.attribute.title +'" class="form-control">\
                    <label for=""> Liên kết </label>\
                    <input name="link" type="text" placeholder="Liên kết" value="'+ link +'" class="form-control">\
                    <label for=""> Mô tả </label>\
                    <textarea name="description" placeholder="Mô tả" class="form-control" rows="3">'+ data.attribute.description +'</textarea>\
                    <div class=" action-bottom">\
                        <div class="row">\
                            <div class="col-xs-6">\
                                <button class="btn btn-block btn-success">Lưu</button>\
                            </div>\
                            <div class="col-xs-6">\
                                <button type="button" id="sendToParent" data-json=\''+ JSON.stringify(data) +'\'" class="btn btn-block btn-danger">Dùng hình này</button>\
                            </div>\
                        </div>\
                    </div>\
                </form>';
                return out;
            }

        </script>
    </div>

    <link rel="stylesheet" href="/asset/upload_tool/css/style.css">
    <script type="text/javascript" src="asset/upload_tool/js/dropzone.js"></script>
    <script type="text/javascript" src="asset/upload_tool/js/upload_ajax_multiple.js"></script>

{% endblock %}
