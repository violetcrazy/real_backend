{% if attributes is defined and attributes|length %}
    {% for attribute in attributes %}
        <div class="form-group">
            <label class="col-sm-3 control-label">
                {{ attribute['name'] }}
            </label>
            <div class="col-sm-5">
                {% if attribute['type_input'] == 'checkbox' %}
                    {% if attributeValueList[attribute['id']] is defined and attributeValueList[attribute['id']]|length %}
                        {% for item in attributeValueList[attribute['id']] %}
                            {% if attributeValue[attribute['id']] is defined and in_array(item['id'], attributeValue[attribute['id']]) %}
                                <div class="checkbox">
                                    <input type="checkbox" checked="checked" value="{{ item['id'] }}" name='{{ attribute['search'] }}[]' class="square-black" value="">
                                    {{ item['name'] }}
                                </div>
                            {% else %}
                                <div class="checkbox">
                                    <input type="checkbox" value="{{ item['id'] }}" name='{{ attribute['search'] }}[]' class="square-black" value="">
                                    {{ item['name'] }}
                                </div>
                            {% endif %}
                        {% endfor %}
                    {% endif %}
                {% endif %}
                {% if attribute['type_input'] == 'select' %}
                    <select name="{{ attribute['search'] }}" class="form-control">
                        {% for item in attributeValueList[attribute['id']] %}
                            {% if attributeValue[attribute['id']] is defined and in_array(item['id'], attributeValue[attribute['id']]) %}
                                <option selected="selected" value="{{ item['id'] }}">{{ item['name'] }}</option>
                            {% else %}
                                <option value="{{ item['id'] }}">{{ item['name'] }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                {% endif %}
                
            </div>
        </div>
    {% endfor %}
{% endif %}