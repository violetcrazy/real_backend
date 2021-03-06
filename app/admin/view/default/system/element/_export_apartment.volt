<h4>
   Chọn dự án để Export</h4>
<hr />
<form action="{{ url({'for': 'system_export_apartment'}) }}" method="post" id="form-export-apartment" enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="type" value="export_apartment">
    <div class="form-group">
        <label for="" class="col-sm-3 control-label">Chọn Dự án</label>
        <div class="col-sm-6 col-md-4">
            <select required="required" name="project_id" required id="project_id_export" class="form-control">
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
            <select required name="block_id" id="list_block_apartment_export" class="form-control">
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 control-label"></label>
        <div class="col-sm-9">
            <button class="btn btn-success btn-custom-animate" type="submit">Xuất danh sách Sản phẩm</button>
        </div>
    </div>
</form>

<script>
    var flat = false;
    $(document).on('submit', '#form-export-apartment', function(event){
        if (!flat) {
            flat = true;
            event.preventDefault();
            $this = $(this);
            $this.find('.btn-success').addClass('loadding').attr('disabled','disabled');
            var link = $this.attr('action');
            var  projectName = $this.find('#project_id_export').find(':selected').text();
            var  blockName = $this.find('#list_block_apartment_export').find(':selected').text();
            $.ajax({
                url : link,
                data: $this.serialize(),
                dataType: 'json',
                method: 'post',
                success: function(data) {
                    if (data.status == 200) {
                        var html = '<div class="alert alert-success"><b>' + projectName + ' - ' + blockName +'</b> - xuất dữ liệu thành công , <a class="alert-link" href="'+ data.result +'">tải tại đây</a></div>';
                    } else {
                        var html = '<div class="alert alert-danger"><b>' + projectName + ' - ' + blockName +'</b> - Lỗi xuất dữ liệu, dự án chưa có sản phẩm hoặc không thể tạo file</div>';
                    }
                    $this.prepend(html);
                },
                complete: function(){
                    flat = false;
                    $this.find('.btn-success').removeClass('loadding').removeAttr('disabled');
                }
            })
        }
    }).on('change', '#project_id_export', function(){
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

                    $('#list_block_apartment_export').html(option);
                }
            }
        });
    })
</script>
