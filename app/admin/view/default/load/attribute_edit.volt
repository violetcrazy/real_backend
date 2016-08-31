{% extends 'default-ajax.volt' %}

{% block content %}
    {% set getAttributeType = getApartmentAttributeType() %}
    <form action="{{ url({'for' : 'load_attribute_save_ajax'}) }}" class="form-attr">
        {% if attrDetail.id is defined %}
            <input type="hidden" name="id" value="{{ attrDetail.id }}">
        {% endif %}

        <input type="hidden" name="module_attr" value="{{ request.getQuery('module_attr') }}">
        <div class="form-group">
            <label for="">Tên tiếng việt</label>
            <input name="name" required type="text" class="form-control" value="{{ attrDetail.name is defined ? attrDetail.name : '' }}">
        </div>
        <div class="form-group">
            <label for="">Tên tiếng anh</label>
            <input required name="name_eng" type="text" class="form-control" value="{{ attrDetail.name_eng is defined ? attrDetail.name_eng : ''  }}">
        </div>
        <div class="form-group">
            <label for="">Nhóm</label>
            <select name="type" required id="" class="form-control">
                <option value="">Chọn nhóm thuộc tính</option>
                {% for key, attr in getAttributeType %}
                    <option {{ attrDetail.type is defined and key == attrDetail.type ? 'selected' : '' }} value="{{ key }}">{{ attr }}</option>
                {% endfor %}
            </select>
        </div>
        <div class="text-right">
            <a href="javascript:parent.$.fancybox.close();" class="btn btn-default btn-sm">
                Hủy
            </a>
            <button type="submit" class="btn btn-success btn-sm">
                Lưu lại
            </button>
        </div>
    </form>

    <script>
        $(document).ready(function(){
            $('.form-attr').on('submit', function(event){
                event.preventDefault();
                $this = $(this);
                var url = $this.attr('action');
                var data = $this.serialize();
                $this.find('.alert').remove();
                $.ajax({
                    url: url,
                    data: data,
                    dataType: 'json',
                    type: 'post',
                    success: function(data){
                        if (data.status == 200) {
                            if ({{ attrDetail is defined ? 'true': 'false' }}) {
                                window.parent.callBackOnSave(1);
                            } else {
                                window.parent.callBackOnSave(0);
                            }
                            $this.prepend('<div class="alert alert-success">{{ attrDetail.id is defined ? 'Cập nhật' : 'Thêm' }} thuộc tính thành công.</div>');
                            parent.$.fancybox.update();
                            {% if attrDetail.id is not defined %}
                                $this.trigger('reset');
                            {% endif %}
                            //parent.$.fancybox.close();
                        } else {
                            $this.prepend('<div class="alert alert-danger">'+ data.message +'</div>');
                            parent.$.fancybox.update();
                        }
                    }
                });
            });
        });
    </script>
{% endblock %}
