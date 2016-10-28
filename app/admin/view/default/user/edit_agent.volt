{% extends 'default.volt' %}

{% block content %}
    {% set user_session = session.get('USER') %}

    <div class="row">
        <div class="col-sm-12">
            {% include 'default/element/layout/breadcrumbs.volt' %}
            <div class="page-header">
                <h3>Chỉnh sửa quản trị viên</h3>
            </div>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}

        <div class="col-sm-12">
            <div class="col-sm-2">
                <div class="row">
                    <div class="fileupload fileupload-new text-center" data-provides="fileupload">
                        <div class="user-image avatar">
                            <div class="fileupload-new thumbnail text-center user-upload-avatar">
                                {% if user.avatar is defined and user.avatar != '' %}
                                    <img src="{{ user.avatar }}" alt="Avatar" class="w100" />
                                {% else %}
                                    <img src="{{ config.asset.backend_url ~ 'img/avatar_default.png' }}" alt="Avatar" class="w100">
                                {% endif %}
                            </div>
                            {#
                            <a href="" class="btn-upload-img">Update avatar</a>
                            <input name="files" id="upload-avatar" type="file" accept="image/*" class="hidden">
                            #}
                        </div>

                        {% if user.avatar is defined and user.avatar != '' %}
                            <a href="{{ url({'for': 'user_delete_avatar', 'query': '?' ~ http_build_query({'user_id': user.id, 'from': 'agent'})}) }}" class="btn btn-primary">
                                Xoá avatar
                            </a>
                        {% endif %}
                    </div>
                </div>
            </div>

            <div class="col-sm-10">
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Tên đăng nhập
                    </label>
                    <div class="col-sm-9">
                        {{ form.render('username', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'username'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Mật khẩu mới
                    </label>
                    <div class="col-sm-9">
                        {{ form.render('new_password', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'new_password'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Họ và tên</label>
                    <div class="col-sm-9">
                        {{ form.render('name', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-9">
                        {{ form.render('email', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'email'} %}
                    </div>
                </div>

                {% if userSession['membership'] == constant('\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN') %}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Loại
                        </label>
                        <div class="col-sm-4">
                            {{ form.render('membership', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'membership'} %}
                        </div>
                    </div>
                {% endif %}

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        Trạng thái
                    </label>
                    <div class="col-sm-4">
                        {{ form.render('status', {'class': 'form-control'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Đăng nhập</label>
                    <div class="col-sm-9">
                        {{ form.render('logined_at', {'class': 'form-control', 'disabled' : 'disabled'}) }}
                        {% include 'default/element/layout/form_message' with {'form': form, 'element': 'logined_at'} %}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-bricky">
                            Cập nhật
                        </button>

                        <a href="{{ url({'for': 'userAgentList'}) }}" class="btn btn-primary">
                            Trở lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        var url_upload = "{{ config.application.base_url }}load/upload-image-user/ajax";
        var user_id = "{{ user.id }}";

        $(document).ready(function () {
            $('.btn-upload-img').click(function (event) {
                event.preventDefault();
                $('#upload-avatar').trigger('click');
            });

            $('#upload-avatar').change(function () {
                var file_data = $(this).prop('files')[0];
                $this = $(this);
                var form_data = new FormData();

                form_data.append('user_id', user_id);
                form_data.append('type_upload', 'avatar');

                $.ajax({
                    url: url_upload,
                    dataType: 'json',
                    data: {user_id: user_id},
                    cache: false,
                    type: 'post',
                    success: function(data) {
                        $this.closest('div').find('img').attr('src', data.result.image_url);
                    }
                });
            });
        });
    </script>
{% endblock %}
