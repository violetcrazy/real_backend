<option value="">--- Chọn ---</option>

{% if districts is defined and districts|length %}
    {% for key, value in districts %}
        <option value="{{ key }}">{{ value }}</option>
    {% endfor %}
{% endif %}