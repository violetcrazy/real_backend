<h4>
    Nhập Dự án từ file EXCEL
    <a href="{{ config.application.template_excel_project ~ '?' ~ config.asset.version }}" class="btn btn-sm btn-warning">
        Tải file MẪU
    </a>
</h4>

<hr />
<form action="{{ url({'for': 'system_data_import_project'}) }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="type" value="project">
    <input required="required" type="file" name="file" placeholder="Chọn file EXCEL dữ liệu Dự Án">
    <br>
    <button class="btn btn-success" type="submit">Nhập danh sách dự án</button>
</form>

<div>
    {% if responseProject is defined and responseProject|length > 0 %}
        <hr />
        {% for key, mess in responseProject  %}
            {% if mess['status'] == 200 %}
                <p class="text-success">
                    Dòng <b>{{ key }}</b>
                    - {{ mess['info'].name }} - <b>Thêm thành công.</b>
                    <a target="_blank" href="{{ url({'for': 'project_edit', 'query': '?' ~ http_build_query({'id': mess['info'].id})}) }}">
                        Cập nhật thông tin
                    </a>
                </p>
            {% endif %}

            {% if mess['status'] == 400 %}
                <div class="text-danger m-b-15">
                    Dòng <b>{{ key }}</b> - Dự án <b>{{ mess['name'] }}</b>: Không thêm được
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