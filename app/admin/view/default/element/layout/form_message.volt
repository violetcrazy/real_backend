{% if (form is defined and element|length) %}
    {% set messages = form.getMessagesFor(element) %}

    {% if (messages|length) %}
        <div class="has-error">
            <span class="help-block" style="margin-bottom: 0 !important;">{{ messages[0] }}</span>
        </div>
    {% endif %}
{% endif %}