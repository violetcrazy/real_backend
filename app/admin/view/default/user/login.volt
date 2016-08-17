{% extends 'default_login.volt' %}

{% block content %}
    <div class="box-login">
        <h3>Administrator</h3>
        <br />
        <form class="form-login" action="" method="POST" enctype="multipart/form-data">
            {{ flashSession.output() }}

            <fieldset>
                <div class="form-group">
                    <span class="input-icon">
                        {{ form.render('username', {'class': 'form-control', 'placeholder': 'Tên đăng nhập'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'username'} %}
                        <i class="fa fa-user"></i>
                    </span>
                </div>
                <div class="form-group form-actions">
                    <span class="input-icon">
                        {{ form.render('password', {'class': 'form-control password', 'placeholder': 'Mật khẩu'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'password'} %}
                        <i class="fa fa-lock"></i>
                    </span>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-bricky pull-right">
                        Đăng nhập
                    </button>
                </div>
            </fieldset>

            <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />
        </form>
    </div>
{% endblock %}