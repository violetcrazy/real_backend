<div class="form-group">
    <label class="col-sm-2 control-label">
        Năm
    </label>
    <div class="col-sm-5">
        {{ form.render('name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Tên năm Âm lịch
    </label>
    <div class="col-sm-5">
        {{ form.render('middle_name', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'middle_name'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta title
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_title', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_title'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta keyword
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_keyword', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_keyword'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Meta description
    </label>
    <div class="col-sm-5">
        {{ form.render('meta_description', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'meta_description'} %}
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        Trạng thái
    </label>
    <div class="col-sm-5">
        {{ form.render('status', {'class': 'form-control'}) }}
        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
    </div>
</div>

{% block bottom_js %}
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true
            });

            $('#name').blur(function(){
                var vl = $(this).val();
                var year = ['Thân', 'Dậu', 'Tức', 'Hợi', 'Tý', 'Sửu', 'Dần', 'Mẹo', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi'];
                var nameYear = ['Canh', 'Tân', 'Nhâm', 'Quý', 'Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ'];

                var indexY = parseInt(vl % 12) ;
                var indexYN = parseInt(vl % 10) ;

                var _name = nameYear[indexYN] + ' ' + year[indexY];
                $('#middle_name').val(_name);
            });

        });
    </script>
{% endblock %}