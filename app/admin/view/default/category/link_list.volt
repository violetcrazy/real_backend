{% extends 'default.volt' %}

{% block content %}
    {% set statusSelect = getGroupStatus() %}
    {% set typeSelect = getGroupType() %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Liên kết</h3>
            </div>
        </div>
        <div class="col-sm-12 m-b-20">
            <a href="{{ url({'for': 'category_add_link'}) }}" class="btn btn-primary float-right">Thêm liên kết</a>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-12">
            {{ flashSession.output() }}

            {% if result is defined and result|length %}
                {% for item in result %}
                    <ul class="link-group">
                        <li>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-reorder">{{ item.id }}</i>
                                    {{ item.name }}
                                    <div class="visible-md visible-lg hidden-sm hidden-xs float-right">
                                        <a class="btn btn-xs btn-teal tooltips" data-original-title="Sửa" data-placement="bottom" href="{{ url({'for': 'category_edit', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-xs btn-bricky tooltips" data-original-title="Xóa" data-placement="bottom" href="{{ url({'for': 'category_edit', 'query': '?' ~ http_build_query({'id': item.id})}) }}">
                                            <i class="fa fa-times fa fa-white"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="dd nestable" id="nestable3" data-url="{{ url({'for': 'load_sort_link_ajax'}) }}">
                                        {% if groupLinkLayout[item.id] is defined %}
                                           {{ groupLinkLayout[item.id] }}
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                {% endfor %}
           {% endif %}
            <div class="clearfix"></div>
        </div>

        <div class="col-sm-12">
            {{ paginationLayout }}
        </div>
    </div>
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'plugins/nestable/jquery.nestable.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.application.base_url ~ 'asset/default/js/link/ui-nestable.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.nestable').each(function(){
                var $nestable = $(this);
                $nestable.nestable({
                })
                    .on('change', function(e) {
                            return $.post($nestable.attr('data-url'), {link:$nestable.nestable('serialize')});
                        })
                    .on('mouseenter mouseleave', 'div.dd3-content', function(event){
                        var $task = $(this).find('.btn-task-group').stop(true,true);
                        if(event.type == 'mouseenter'){
                            $task.css('visibility', 'visible');
                        } else {
                            $task.css('visibility', 'hidden');
                        }
                        return false;
                });
            });

            $('#nestable3').nestable();

            $('.search-select').select2({
                allowClear: true
            });
        });
    </script>
{% endblock %}