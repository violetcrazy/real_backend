<h4>
    Nhập mới danh sách sản phẩm
    <a href="{{ config.application.template_excel_apartment }}?{{ config.asset.version }}" class="btn btn-sm btn-warning">Tải file MẪU</a>
</h4>

<hr />
<form action="{{ url({'for': 'system_data_import_apartment'}) }}" method="post" enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="type" value="apartment">
    <div class="form-group">
        <label for="" class="col-sm-3 control-label">Chọn Dự án</label>
        <div class="col-sm-6 col-md-4">
            <select required="required" name="project_id" required id="list_project_apartment" class="form-control">
                <option value="">Chọn dự án</option>
                {% if projects is defined and projects['result']|length %}
                    {% for project in projects['result'] %}
                        <option {{ project_id is defined and project['id'] == project_id ? 'selected' : '' }} value="{{ project['id'] }}">{{ project['name'] }}</option>
                    {% endfor %}
                {% endif %}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 control-label">Chọn Block</label>
        <div class="col-sm-6 col-md-4">
            <select required name="block_id" id="list_block_apartment" class="form-control">
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 control-label">Tải file lên</label>
        <div class="col-sm-9">
            <input required="required" type="file" name="file" placeholder="Chọn file EXCEL dữ liệu Dự án">
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 control-label"></label>
        <div class="col-sm-9">
            <button class="btn btn-success" type="submit">Nhập danh sách sản phẩm</button>
        </div>
    </div>
</form>

<div>
    {% if responseApartment is defined and responseApartment|length %}
        <hr />
        {% for key, mess in responseApartment  %}
            {% if mess['status'] == 200 %}
                <p class="text-success">
                    Dòng <b>{{ key }}</b>
                    - {{ mess['info'].name }} - <b>Thêm thành công.</b>
                    <a target="_blank" href="{{ url({'for': 'apartment_edit', 'query': '?' ~ http_build_query({'id': mess['info'].id, 'block_id': request.getPost('block_id')})}) }}">
                        Cập nhật thông tin
                    </a>
                </p>
            {% endif %}

            {% if mess['status'] == 400 %}
                <div class="text-danger m-b-15">
                    Dòng <b>{{ key }}</b> - sản phẩm <b>{{ mess['name'] }}</b>: Không thêm được
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
<script>
    $(document).on('change', '#list_project_apartment', function(){
        var project_id = $(this).val();
        var urlLoadBlock = '{{ url({'for': 'block_ajax'}) }}';
        $.ajax({
            url: urlLoadBlock,
            data: {project_id: project_id},
            dataType: 'json',
            success: function(data){
                if (data.status == 200) {
                    var option = '';

                    $.each(data.result, function(index, value){
                        option += '<option value="'+ value.id +'">'+ value.name +'</option>';
                    });

                    $('#list_block_apartment').html(option);
                }
            }
        });
    }).ready(function(){
        var project_id = '{{ project_id is defined ? project_id : '' }}';
        var urlLoadBlock = '{{ url({'for': 'block_ajax'}) }}';
        $.ajax({
            url: urlLoadBlock,
            data: {project_id: project_id},
            dataType: 'json',
            success: function(data){
                if (data.status == 200) {
                    var option = '';

                    $.each(data.result, function(index, value){
                        option += '<option value="'+ value.id +'">'+ value.name +'</option>';
                    });

                    $('#list_block_apartment').html(option);
                }
            }
        });
    });
</script>