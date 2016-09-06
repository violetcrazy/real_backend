<h3>Tiếng Việt</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Tên sản phẩm <span class="symbol required"></span>
    </label>
    <div class="col-sm-5">
        <div class="input-group">
            {{ form.render('name', {'class': 'form-control','readonly': 'readonly'}) }}
            <span class="input-group-btn">
                <button class="btn btn-edit btn-warning edit-name" type="button" id="edit-name">Sửa</button>
            </span>
        </div>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>

<h3>Tiếng Anh</h3>
<hr />
<div class="form-group">
    <label class="col-sm-3 control-label">
        Tên sản phẩm <span class="symbol required"></span>
    </label>
    <div class="col-sm-5">
        <div class="input-group">
            {{ form.render('name_eng', {'class': 'form-control','readonly': 'readonly'}) }}
            <span class="input-group-btn">
                <button class="btn btn-edit btn-warning edit-name" type="button" id="edit-name-eng">Sửa</button>
            </span>
        </div>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name_eng'} %}
    </div>
</div>
<hr>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Dự án
    </label>
    <div class="col-sm-5">
        <select required="required" name="project_id" required id="list_project_apartment" class="form-control">
            <option value="">Chọn dự án</option>
            {% if projects is defined and projects['result']|length %}
                {% for project in projects['result'] %}
                    <option {{ block_detail.project_id is defined and project['id'] == block_detail.project_id ? 'selected' : '' }} value="{{ project['id'] }}">{{ project['name'] }}</option>
                {% endfor %}
            {% endif %}
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Block/Khu
    </label>
    <div class="col-sm-5">
        <select required name="block_id" id="list_block_apartment" class="form-control">
            <option value="">Chọn Block/Khu</option>
            {% if listBlocks is defined and listBlocks['result']|length %}
                {% for _block in listBlocks['result'] %}
                    <option {{ block_id is defined and _block['id'] == block_id ? 'selected' : '' }} value="{{ _block['id'] }}">{{ _block['name'] }}</option>
                {% endfor %}
            {% endif %}
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Tầng
    </label>
    <div class="col-sm-5">
        <select id="floor_count" class="form-control" name="floor">
            {% if (floor_name_list is defined and floor_name_list|length) %}
                {% for index, value in floor_name_list %}
                    <option {{ apartment.floor is defined and apartment.floor == index ? 'selected' : '' }} value="{{ index }}">
                        {{ value }}
                    </option>
                {% endfor %}
            {% else %}
                {% for value in 1..blocks.floor_count %}
                    <option {{ apartment.floor is defined and apartment.floor == value ? 'selected' : '' }} value="{{ value }}">
                        {{ value }}
                    </option>
                {% endfor %}
            {% endif %}
        </select>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'floor_count'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Thứ tự sản phẩm 
    </label>
    <div class="col-sm-5">
        <select id="ordering" class="form-control" name="ordering">
            {% for value in 1..blocks.apartment_count %}
                <option {{ apartment.ordering is defined and apartment.ordering == value ? 'selected' : '' }} value="{{ value }}">
                    {{ value }}
                </option>
            {% endfor %}
        </select>
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'ordering'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Người quản lý
    </label>
    <div class="col-sm-5">
        {{ form.render('user_id', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'user_id'} %}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Diện tích sản phẩm
    </label>
    <div class="col-sm-5">
        {{ form.render('total_area', {'class': 'form-control'}) }}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Diện tích vườn
    </label>
    <div class="col-sm-5">
        {{ form.render('green_area', {'class': 'form-control'}) }}
    </div>
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
                    var option = '<option value="">Chọn block/khu</option>';

                    $.each(data.result, function(index, value){
                        option += '<option value="'+ value.id +'">'+ value.name +'</option>';
                    });

                    $('#list_block_apartment').html(option);
                }
            }
        });
    }).ready(function(){
        var project_id = $('#list_project_apartment').val();
        var urlLoadBlock = '{{ url({'for': 'block_ajax'}) }}';
        $.ajax({
            url: urlLoadBlock,
            data: {project_id: project_id},
            dataType: 'json',
            success: function(data){
                if (data.status == 200) {
                    var option = '<option value="">Chọn block/khu</option>';

                    $.each(data.result, function(index, value){
                        option += '<option value="'+ value.id +'">'+ value.name +'</option>';
                    });

                    $('#list_block_apartment').html(option).val('{{ block_detail.id is defined ? block_detail.id : '' }}');
                }
            }
        });
    });
</script>
