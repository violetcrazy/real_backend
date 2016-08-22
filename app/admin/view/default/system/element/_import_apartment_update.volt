<h4>
    Cập nhật sản phẩm đã có
</h4>
<hr />

<form action="{{ url({'for': 'system_data_import_update_apartment'}) }}" method="post" enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="type" value="update_apartment">

    <div class="form-group">
        <label for="" class="col-sm-3 control-label">Tải file lên</label>
        <div class="col-sm-9">
            <input required="required" type="file" name="file" placeholder="Chọn file EXCEL dữ liệu Dự án">
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 control-label"></label>
        <div class="col-sm-9">
            <button class="btn btn-success" type="submit">Cập nhật sản phẩm</button>
        </div>
    </div>
</form>

<div>
    {% if responseApartmentUpdate is defined and responseApartmentUpdate|length %}
        <hr />
        {% for key, mess in responseApartmentUpdate  %}
            {% if mess['status'] == 200 %}
                <p class="text-success">
                    Dòng <b>{{ key }}</b>
                    - {{ mess['info'].name }} - <b>cập nhật thành công.</b>
                    <a target="_blank" href="{{ url({'for': 'apartment_edit', 'query': '?' ~ http_build_query({'id': mess['info'].id, 'block_id': request.getPost('block_id')})}) }}">
                        Cập nhật thông tin
                    </a>
                </p>
            {% endif %}

            {% if mess['status'] == 400 %}
                <div class="text-danger m-b-15">
                    Dòng <b>{{ key }}</b> - sản phẩm <b>{{ mess['name'] }}</b>: Không cập nhật được
                    {% if mess['info'] is defined and mess['info']|length > 0 %}
                        <ul class="m-t-5" style="color: black">
                            {% if mess['info'] is defined and is_array(mess['info']) > 0 %}
                                {% for error in  mess['info'] %}
                                    <li>
                                        {{ error }}
                                    </li>
                                {% endfor %}
                            {% else %}
                                {{mess['info'] is defined ? mess['info'] : ''  }}
                            {% endif %}
                        </ul>
                    {% endif %}
                </div>
            {% endif %}
        {% endfor %}
    {% endif %}
</div>