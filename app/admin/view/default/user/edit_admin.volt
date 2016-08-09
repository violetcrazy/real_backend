{% extends 'default.volt' %}

{% block content %}
    {% set user_session = session.get('USER') %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}

            <div class="page-header">
                <h3>{{ editTitle }}</h3>
            </div>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="col-sm-12">
            <div class="col-sm-1">
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

            <div class="col-sm-11">
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Tên đăng nhập
                    </label>
                    <div class="col-sm-4">
                        {{ form.render('username', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'username'} %}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Mật khẩu mới
                    </label>
                    <div class="col-sm-4">
                        {{ form.render('new_password', {'class': 'form-control', 'autocomplete': 'off'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'new_password'} %}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Họ và tên</label>
                    <div class="col-sm-4">
                        {{ form.render('name', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-4">
                        {{ form.render('email', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'email'} %}
                    </div>
                </div>

                {% if user.membership != constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN') %}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Dự án</label>
                        <div class="col-sm-8">
                            {{ form.render('projectIds', {'class': 'form-control search-select', 'multiple': 'multiple', 'name': 'projectIds[]', 'value': projectIds}) }}
                        </div>
                    </div>
                {% endif %}

                {% if userSession['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN') %}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Loại
                        </label>
                        <div class="col-sm-3">
                            {{ form.render('membership', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'membership'} %}
                        </div>
                    </div>
                {% endif %}

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Trạng thái
                    </label>
                    <div class="col-sm-3">
                        {{ form.render('status', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Đăng nhập</label>
                    <div class="col-sm-3">
                        {{ form.render('logined_at', {'class': 'form-control', 'disabled' : 'disabled'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'logined_at'} %}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-bricky">
                            Cập nhật
                        </button>
                        <a href="{{ url({'for': urlFor, 'query': '?' ~ http_build_query({'q': q})}) }}" class="btn btn-primary">
                            Trở lại
                        </a>
                    </div>
                </div>
            </div>
            <div class="clear-fix"></div>
        </div>
    </form>
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true
            });
        });
    </script>
{% endblock %}
