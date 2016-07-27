{% extends 'default.volt' %}

{% block content %}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Tài khoản</h3>
            </div>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        {#
        <div class="col-sm-2">
            <div class="row">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="user-image">
                        <div class="fileupload-new thumbnail text-center user-upload-avatar">
                            {% if user.avatar is defined and user.avatar != '' %}
                                <img src="{{ config.asset.frontend_url ~ 'img/avatar/' ~ user.avatar }}" alt="Avatar" class="w100" />
                            {% else %}
                                <img src="{{ config.asset.backend_url ~ 'img/avatar_default.png' }}" alt="Avatar" class="w100">
                            {% endif %}
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 100%; max-height: 150px;"></div>
                        <div class="user-image-buttons">
                            <span class="btn btn-teal btn-file btn-sm">
                                <span class="fileupload-new"><i class="fa fa-pencil"></i></span>
                                <span class="fileupload-exists"><i class="fa fa-pencil"></i></span>
                                <input type="file" name="file_avatar" id="file_avatar">
                            </span>
                            <a href="javascript:void(0)" class="btn fileupload-exists btn-bricky btn-sm" data-dismiss="fileupload">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        #}

        <div class="col-sm-10">
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    Tên đăng nhập
                </label>
                <div class="col-sm-5">
                    {{ form.render('username', {'class': 'form-control'}) }}
                    {% include 'default/element/layout/form_message' with {'form': form, 'element': 'username'} %}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    Mật khẩu mới
                </label>
                <div class="col-sm-5">
                    {{ form.render('new_password', {'class': 'form-control', 'autocomplete': 'off'}) }}
                    {% include 'default/element/layout/form_message' with {'form': form, 'element': 'new_password'} %}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    Họ tên
                </label>
                <div class="col-sm-5">
                    {{ form.render('name', {'class': 'form-control'}) }}
                    {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    Email
                </label>
                <div class="col-sm-5">
                    {{ form.render('email', {'class': 'form-control'}) }}
                    {% include 'default/element/layout/form_message' with {'form': form, 'element': 'email'} %}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    Đăng nhập
                </label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" disabled="disabled" value="{% if user.logined_at is defined and user.logined_at != '' %}{{ date("d-m-Y H:i:s", strtotime(user.logined_at)) }}{% endif %}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-bricky">Cập nhật</button>
                </div>
            </div>
        </div>
    </form>
{% endblock %}