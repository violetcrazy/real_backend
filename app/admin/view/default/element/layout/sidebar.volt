{% set sidebar_menu = getSidebarMenu() %}

{% set userSession = session.get('USER') %}
{% set controller  = router.getControllerName() %}
{% set action      = router.getActionName() %}

<div class="main-navigation navbar-collapse collapse">
    <div class="navigation-toggler">
        <i class="clip-chevron-left"></i>
        <i class="clip-chevron-right"></i>
    </div>

    <ul class="main-navigation-menu">
        {% for key, item in sidebar_menu %}
            {% set class = '' %}

            {% if (controller == item['controller']) %}
                {% set class = 'active open' %}
            {% endif %}

            {% if (in_array(userSession['membership'], item['roles'])) %}
                <li class="{{ class }}">
                    {% if (item['sub_menu'] is defined and item['sub_menu']|length) %}
                        <a href="javascript:void(0);">
                            <i class="{{ item['icon_class'] }}"></i>
                            <span class="title">{{ item['title'] }}</span>
                            <i class="icon-arrow"></i>
                        </a>

                        <ul class="sub-menu">
                            {% set sub_menu = item['sub_menu'] %}

                            {% for sub_key, sub_item in sub_menu %}
                                {% set sub_class = '' %}
                                {% if (controller == sub_item['controller'] and action == sub_item['action']) %}
                                    {% set sub_class = 'active' %}
                                {% endif %}

                                {% if (in_array(userSession['membership'], sub_item['roles'])) %}
                                    <li class="{{ sub_class }}">
                                        <a href="{{ url({'for': sub_key}) }}">
                                            <span class="title">{{ sub_item['title'] }}</span>
                                        </a>
                                    </li>
                                {% endif %}
                            {% endfor %}
                        </ul>
                    {% else %}
                        <a href="{{ url({'for': key}) }}">
                            <i class="{{ item['icon_class'] }}"></i>
                            <span class="title">{{ item['title'] }}</span>
                        </a>
                    {% endif %}
                </li>
            {% endif %}
        {% endfor %}

        <li>
            <a href="javascript:void(0);" class="add-gallery">
                <i class="clip-pictures"></i>
                Quản lý hình ảnh
            </a>
        </li>
    </ul>
</div>
