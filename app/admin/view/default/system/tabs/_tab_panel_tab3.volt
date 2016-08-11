<div class="tab-pane in" id="panel_tab3">
    <div class="form-horizontal col-xs-12 clearfix">
        <div class="form-group">
            <label class="col-sm-4 control-label">
                Thời gian hết hạn REQUEST
            </label>
            <div class="col-sm-8">
                <div class="input-group">
                    <input type="text" name="request_timeout" class="form-control format-number" value="{{ requestTimeout }}" />
                    <span class="input-group-addon">Giờ</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">
                Giá cộng trừ.
            </label>
            <div class="col-sm-8">
                <div class="input-group">
                    <input type="text" name="range_price" class="form-control format-number" value="{{ rangePrice }}" />
                    <span class="input-group-addon">VND</span>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="text-right text-danger col-xs-12 m-t-5">
                <i class="">Dùng để tính MIN - MAX trong tìm kiếm sản phẩm tương tự khi REJECT 1 REQUEST</i>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">
                Tỷ lệ tính điểm
            </label>
            <div class="col-sm-8">
                <div class="input-group">
                    <input type="text" name="price_score" class="form-control format-number" value="{{ priceScore }}" />
                    <span class="input-group-addon">VND/1 điểm</span>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="text-right text-danger col-xs-12 m-t-5">
                <i class="">Dùng để tính điểm của thành viên dựa trên doanh số bán hàng</i>
            </div>
        </div>
        <hr />
        <div class="form-group">
            <label class="col-sm-4 control-label">
                Mức giá MIN khi search
            </label>
            <div class="col-sm-8 build-option-json-single" data-bind="price_search_min" data-class="format-number">
                <div class="clearfix endlist"></div>
                <input type="text" class="form-control main-data" readonly name="price_search_min" value='{{ price_search_min }}'>
                <div class="text-right m-t-10">
                    <a href="" class="btn btn-xs btn-primary btn-add">Thêm 1 dòng</a>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">
                Mức giá MAX khi search
            </label>
            <div class="col-sm-8 build-option-json-single" data-bind="price_search_max" data-class="format-number">
                <div class="clearfix endlist"></div>
                <input type="text" class="form-control main-data" readonly name="price_search_max" value='{{ price_search_max }}'>
                <div class="text-right m-t-10">
                    <a href="" class="btn btn-xs btn-primary btn-add">Thêm 1 dòng</a>
                </div>
            </div>
        </div>
        <hr />

        <div class="form-group">
            <label class="col-sm-4 control-label">
                Hình mặc định
            </label>
            <div class="col-sm-8">
                {% set value = '' %}
                {% set src = '' %}

                {% set value = noimage %}
                {% set src = config.cdn.dir_upload ~ noimage %}

                {{ templateUpload('no_image', src,value) }}
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>