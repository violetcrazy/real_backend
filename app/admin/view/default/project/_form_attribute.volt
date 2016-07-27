<form id="form-article" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
    {{ flashSession.output() }}

    <div class="form-group">
        <label class="col-sm-2 control-label">
            Tên <span class="symbol required"></span>
        </label>
        <div class="col-sm-8">
            {{ form.render('name', {'class': 'form-control'}) }}
            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">
            Icon
        </label>
        <div class="col-sm-1">
            <div class="thumbnail-img">
                <span id="icon-choosed">
                    {% if attribute.image_one is defined and attribute.image_one != '' %}
                        <img src="{{ attribute.image_one_url ~ '?' ~ config.asset.version }}" id="image_one_url"  />
                        <input type="hidden" id="image_one_name" name="image_one" value="{{ attribute.image_one }}" />
                        <input type="hidden" id="image_two_name" name="image_two" value="{{ attribute.image_two }}" />
                    {% else %}
                        <img src="{{ config.asset.backend_url ~ 'img/choose.png?' ~ config.asset.version }}" id="image_one_url"/>
                        <input type="hidden" id="image_one_name" name="image_one" value="" />
                        <input type="hidden" id="image_two_name" name="image_two" value="" />
                    {% endif %}
                </span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="col-sm-3">
            <a data-toggle="modal" id="modal_ajax_demo_btn" class="demo btn btn-primary">
                Chọn icon
            </a>

            <div id="ajax-modal" class="modal fade" tabindex="-1" style="display: none;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <div><b>Chọn Icon</b></div>
                </div>
                <div class="modal-body">
                    {% for item in iconsList %}
                        <span class="item-icon-select">
                            <img src="{{ item['image_url'] ~ '?' ~ config.asset.version }}" />
                            <input type="hidden" class="image_url" value="{{ item['image_name'] }}" />
                        </span>
                    {% endfor %}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="select_icon">Chọn</button>
                    <button type="button" class="btn" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">
            Ngôn ngữ
        </label>
        <div class="col-sm-3">
            {{ form.render('language', {'class': 'form-control'}) }}
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">
            Kiểu
        </label>
        <div class="col-sm-3">
            {{ form.render('type', {'class': 'form-control'}) }}
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">
        </label>
        <div class="col-sm-3">
            <button type="submit" id="form-article-button" class="btn btn-bricky">
                {% if addAction is defined and addAction %}
                    Thêm
                {% else %}
                    Cập nhật
                {% endif %}
            </button>

            {% if addAction is not defined %}
                <a href="{{ url({'for': 'project_list_attribute'}) }}" class="btn btn-primary">Trở lại</a>
            {% endif %}
        </div>
    </div>
</form>
