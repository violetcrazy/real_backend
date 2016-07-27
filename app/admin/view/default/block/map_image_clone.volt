{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Sao chép bản đồ</h3>

                <a href="{{ url({'for': 'project_edit', 'query': '?id=' ~ project.id }) }}">
                    {{ project.name }}
                </a>
                &gt;
                <a href="{{ url({'for': 'block_edit', 'query': '?' ~ http_build_query({'project_id': project.id, 'id': blockModel.id}) }) }}">
                    {{ blockModel.name }}
                </a>
                &gt;
                <span>
                    Sao chép bản đồ tầng {{ request.getQuery('floor_count') }}
                </span>
            </div>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="form-group">
            <label class="col-sm-2 control-label">
                Dự án
            </label>
            <div class="col-sm-5">
                <input type="text" class="form-control" disabled="disabled" value="{{ project.name }}" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Block
            </label>
            <div class="col-sm-5">
                <input type="text" class="form-control" disabled="disabled" value="{{ blockModel.name }}" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Từ tầng
            </label>
            <div class="col-sm-4">
                <input type="text" class="form-control" disabled="disabled" value="{{ currentFloor }}" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                Qua tầng
            </label>
            <div class="col-sm-4">
                <select id="floorNumber" name="floor" class="form-control">
                    <option value="">--- Chọn tầng ---</option>
                    {% for key, value in floorSelect %}
                        {% if floorNumber == key %}
                            <option value="{{ key }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ key }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
        </div>

        {% set i = 1 %}
        {% for item in apartments %}
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    {{ item.name }} =>
                </label>
                <div class="col-sm-3">
                    <select name="map_item_id[{{ item.id }}]" class="list-apartment" style="width: 100%;" tabindex="{{ i }}">
                        <option value="">--- Chọn ---</option>
                        {% for newItem in newApartments %}
                            <option value="{{ newItem.id }}">{{ newItem.name }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>

            {% set i = i + 1 %}
        {% endfor %}

        <div class="form-group">
            <label class="col-sm-2 control-label">
            </label>
            <div class="col-sm-5">
                <button type="submit" class="btn btn-bricky">
                    Sao chép
                </button>
            </div>
        </div>
    </form>

    <style type="text/css">
        .select-container-active {
            border: blue;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#floorNumber').change(function() {
                var floorNumber = $(this).val();
                window.location.href = "{{ url({'for': 'block_map_image_clone', 'query': '?' ~ http_build_query({'map_image_id': mapImage.id, 'block_id': blockModel.id})}) }}" + "&floor_number=" + floorNumber;
            });

            $('.list-apartment').each(function() {
                var vl = $(this).val();
                $(this).attr('pre', vl);
            });

            $('.list-apartment').change(function() {
                $this = $(this);
                var vlOld = $this.attr('pre');
                var vl = $this.val();

                $this.attr('pre', vl);
                $('[value="'+ vlOld +'"]').attr('selected-option', 'not-select');
                if (vl != '') {
                    $('[value="'+ vl +'"]').attr('selected-option', 'selected');
                }
            });

            $('.list-apartment').select2({
                allowClear: true
            });

            $('.list-apartment').focus(function() {});
        });
    </script>
{% endblock %}
