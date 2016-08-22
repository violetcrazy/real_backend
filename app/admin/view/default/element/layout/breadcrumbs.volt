<ol class="breadcrumb">
    {% if breadcrumbs is defined %}
        {% for item in breadcrumbs %}
            {% if item['active'] %}
                <li class="active">
                    {{ item['title'] }}
                </li>
            {% else %}
                <li>
                <a href="{{ item['url'] }}">{{ item['title'] }}</a>
                </li>
            {% endif %}
        {% endfor %}
    {% endif %}
</ol>