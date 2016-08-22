{% extends 'default.volt' %}

{% block content %}
    {% set user_session = session.get('USER') %}
    {% set user_membership = getUserMembership() %}
    {% set user_exclusive = getUserExclusive() %}

    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Chỉnh sửa thành viên</h3>
            </div>
        </div>
    </div>

    <form name="form-user" id="form-user" data-id="{{ user.id }}" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ flashSession.output() }}
        <div class="col-sm-12">
            <div class="row">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="user-image">
                        <span class="status-upload-cover"></span>
                        <div class="fileupload-new thumbnail text-center user-upload-avatar" style="width: 100%;">
                            {% if user.cover is defined and user.cover != '' %}
                                <img src="{{ config.asset.frontend_url ~ 'img/cover/' ~ user.cover }}" alt="Avatar" class="h320" />
                            {% else %}
                                <img src="{{ config.asset.backend_url ~ 'img/cover_default.png' }}" alt="Avatar" class="h320">
                            {% endif %}
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail cover-preview" style="max-height: 320px;"></div>
                        <div class="user-image-buttons">
                            <span class="btn btn-teal btn-file btn-sm">
                                <span class="fileupload-new"><i class="fa fa-pencil"></i></span>
                                <span class="fileupload-exists"><i class="fa fa-pencil"></i></span>
                                <input type="file" name="file_cover" id="file_cover" data-url="{{ url({"for": "load_cover_ajax"}) }}">
                            </span>
                            <a href="javascript:void(0)" class="btn btn-bricky btn-sm {% if user.cover != '' %}show-delete{% endif %}" id="delete-cover" data-url="{{ url({"for": "load_delete_cover_ajax"}) }}">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="row">
                <div class="page-header">
                    <h4>Tài khoản</h4>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Ảnh đại diện
                        </label>
                        <div class="col-sm-3">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="user-image">
                                    <span class="status-upload-avatar"></span>
                                    <div class="fileupload-new thumbnail text-center user-upload-avatar avatar-preview">
                                        {% if user.avatar is defined and user.avatar != '' %}
                                            <img src="{{ config.asset.frontend_url ~ 'img/avatar/' ~ user.avatar }}" alt="Avatar" class="w100" />
                                        {% else %}
                                            <img src="{{ config.asset.backend_url ~ 'img/avatar_default.png' }}" alt="Avatar" class="w100">
                                        {% endif %}
                                    </div>
                                    <div class="fileupload-preview fileupload-exists thumbnail avatar-preview" style="max-width: 100%; max-height: 100%;"></div>
                                    <div class="user-image-buttons">
                                        <span class="btn btn-teal btn-file btn-sm">
                                            <span class="fileupload-new"><i class="fa fa-pencil"></i></span>
                                            <span class="fileupload-exists"><i class="fa fa-pencil"></i></span>
                                            <input type="file" name="file_avatar" id="file_avatar" data-url="{{ url({"for": "load_avatar_ajax"}) }}">
                                        </span>
                                        <a href="javascript:void(0)" class="btn btn-bricky btn-sm {% if user.avatar != '' %}show-delete{% endif %}" id="delete-avatar" data-url="{{ url({"for": "load_delete_avatar_ajax"}) }}">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p>
                                {% if user.membership == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_VIP') %}
                                    <span class="btn btn btn-yellow btn-sm">{{ user_membership[user.membership] }}</span>
                                {% elseif user.membership == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_PARTNER') %}
                                    <span class="btn btn-danger btn-sm">{{ user_membership[user.membership] }}</span>
                                {% elseif user.membership == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_BASIC') %}
                                    <span class="btn btn-light-grey btn-sm">{{ user_membership[user.membership] }}</span>
                                {% endif %}
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Số điện thoại
                        </label>
                        <div class="col-sm-5">
                            {{ form.render('username', {'class': 'form-control', 'disabled':'disabled'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'username'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Mật khẩu mới
                        </label>
                        <div class="col-sm-5">
                            {{ form.render('new_password', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'new_password'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                        </label>
                        <div class="col-sm-5">
                            {{ form.render('is_verified', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'is_verified'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Trạng thái
                        </label>
                        <div class="col-sm-5">
                            {{ form.render('status', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'status'} %}
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="page-header">
                        <h4>Thông tin cá nhân</h4>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Số điện thoại hiển thị
                        </label>
                        <div class="col-sm-5">
                            {{ form.render('display', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'display'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Họ và tên</label>
                        <div class="col-sm-5">
                            {{ form.render('name', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'name'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-5">
                            {{ form.render('email', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'email'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Giới tính</label>
                        <div class="col-sm-5">
                            {{ form.render('gender', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'gender'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Ngày sinh</label>
                        <div class="col-sm-5">
                            {{ form.render('birthday', {'class': 'form-control date-picker', 'data-date-format': 'dd-mm-yyyy'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'birthday'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">CMND/Passport</label>
                        <div class="col-sm-5">
                            {{ form.render('identity_number', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'identity_number'} %}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Địa chỉ</label>
                        <div class="col-sm-5">
                            {{ form.render('address', {'class': 'form-control'}) }}
                            {% include 'default/element/layout/form_message' with {'form': form, 'element': 'address'} %}
                        </div>
                    </div>
                    <div class="page-header">
                        <h4>Thông tin kinh doanh</h4>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Tên giao dịch
                        </label>
                        <div class="col-sm-5">
                            <input type="text" name="user_business_info_name" class="form-control" value="{% if user_business_info.name is defined %}{{ user_business_info.name }}{% endif %}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Tỉnh/Thành phố
                        </label>
                        <div class="col-sm-5">
                            <select name="province_id" id="province_id" class="form-control search-select" data-url="{{ url({"for": "load_district_ajax"}) }}">
                                <option value="">--- Tỉnh/ Thành phố ---</option>
                                {% for item in provinces %}
                                    {% if user.province_id is defined and user.province_id == item['id'] %}
                                        <option value="{{ item['id'] }}" selected="selected">{{ item['name'] }}</option>
                                    {% else %}
                                        <option value="{{ item['id'] }}">{{ item['name'] }}</option>
                                    {% endif %}
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2 control-label">Quận/Huyện</label>
                        <div class="col-xs-5">
                            <div class="select-style select-control">
                                <select id="district_id" name="district_id" class="form-control search-select">
                                    <option value="">--- Quận huyện ---</option>
                                    {% for item in districts %}
                                        {% if user.district_id is defined and user.district_id == item['id'] %}
                                            <option value="{{ item['id'] }}" selected="selected">{{ item['name'] }}</option>
                                        {% else %}
                                            <option value="{{ item['id'] }}">{{ item['name'] }}</option>
                                        {% endif %}
                                    {% endfor %}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Loại hình kinh doanh
                        </label>
                        <div class="col-sm-5">
                            <input type="text" name="user_business_info_type" class="form-control" value="{% if user_business_info.type is defined %}{{ user_business_info.type }}{% endif %}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Giấy phép kinh doanh
                        </label>
                        <div class="col-sm-5">
                            <input type="text" name="user_business_info_license" class="form-control" value="{% if user_business_info.license is defined %}{{ user_business_info.license }}{% endif %}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            Giới thiệu
                        </label>
                        <div class="col-sm-5">
                            <textarea name="user_business_info_description" class="form-control" style="height: 300px;">{% if user_business_info.description is defined %}{{ user_business_info.description }}{% endif %}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            {{ form.render('id') }}
                            <button type="submit" class="btn btn-bricky">
                                Cập nhật
                            </button>
                            <a href="{{ url({'for': 'user_list', 'query': '?' ~ http_build_query({'q': q, 'filter': filter})}) }}" class="btn btn-primary">
                                Trở lại
                            </a>
                            <a href="{{ url({'for': 'article_user', 'query': '?' ~ http_build_query({'id': user.id})}) }}" class="btn btn-primary">
                                Danh sách tin
                            </a>

                            <a href="{{ url({'for': 'article_add', 'query': '?' ~ http_build_query({'user_id': user.id})}) }}" class="btn btn-primary">
                                Đăng tin
                            </a>
                        </div>
                    </div>
                    {% if user_session['membership'] == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN') %}
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-9">
                                {% if user.membership == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_PARTNER') %}
                                    <a href="{{ url({'for': 'user_downgrade', 'query': '?' ~ http_build_query({'id': user.id, 'filter': filter})}) }}" class="btn btn-warning">
                                        Bỏ Partner
                                    </a>
                                    {% if user.is_exclusive == constant('\MBN\Data\Lib\Constant::USER_IS_EXCLUSIVE_NOT') %}
                                        <a href="{{ url({'for': 'user_exclusive', 'query': '?' ~ http_build_query({'id': user.id, 'to': 'exclusive', 'filter': filter})}) }}" class="btn btn-warning">
                                            Đăng ký độc quyền
                                        </a>
                                    {% else %}
                                        <a href="{{ url({'for': 'user_exclusive_downgrade', 'query': '?' ~ http_build_query({'id': user.id, 'to': 'exclusive', 'filter': filter})}) }}" class="btn btn-warning">
                                            Hủy bỏ độc quyền
                                        </a>
                                    {% endif %}
                                {% elseif user.membership == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_VIP') %}
                                    <a href="{{ url({'for': 'user_downgrade', 'query': '?' ~ http_build_query({'id': user.id, 'filter': filter})}) }}" class="btn btn-warning">
                                        Hạ VIP
                                    </a>
                                    <a href="{{ url({'for': 'user_upgrade', 'query': '?' ~ http_build_query({'id': user.id, 'to': 'vip', 'filter': filter})}) }}" class="btn btn-warning">
                                        Gia hạn VIP
                                    </a>
                                    <a href="{{ url({'for': 'user_upgrade', 'query': '?' ~ http_build_query({'id': user.id, 'to': 'partner', 'filter': filter})}) }}" class="btn btn-warning">
                                        Nâng cấp Partner
                                    </a>
                                    {% if user.is_exclusive == constant('\MBN\Data\Lib\Constant::USER_IS_EXCLUSIVE_NOT') %}
                                        <a href="{{ url({'for': 'user_exclusive', 'query': '?' ~ http_build_query({'id': user.id, 'filter': filter})}) }}" class="btn btn-warning">
                                            Đăng ký độc quyền
                                        </a>
                                    {% else %}
                                        <a href="{{ url({'for': 'user_exclusive_downgrade', 'query': '?' ~ http_build_query({'id': user.id, 'filter': filter})}) }}" class="btn btn-warning">
                                            Hủy bỏ độc quyền
                                        </a>
                                    {% endif %}
                                {% else %}
                                    <a href="{{ url({'for': 'user_upgrade', 'query': '?' ~ http_build_query({'id': user.id, 'to': 'vip', 'filter': filter})}) }}" class="btn btn-warning">
                                        Đăng ký VIP
                                    </a>
                                    <a href="{{ url({'for': 'user_upgrade', 'query': '?' ~ http_build_query({'id': user.id, 'to': 'partner', 'filter': filter})}) }}" class="btn btn-warning">
                                        Nâng cấp Partner
                                    </a>
                                {% endif %}
                            </div>
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>Lịch sử tài khoản</h3>
            </div>
        </div>
    </div>
    {% if user_membership_history | length %}
        <table class="table table-striped table-bordered table-hover table-full-width">
            <thead>
                <tr role="row">
                    <th>Loại</th>
                    <th>Độc quyền</th>
                    <th>Tham gia</th>
                    <th>Kết thúc</th>
                    <th>Chăm sóc KH</th>
                    <th>Thực hiện</th>
                </tr>
            </thead>
            <tbody>
                {% for item in user_membership_history %}
                    <tr>
                        <th>
                            {% if item.type == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_VIP') %}
                                <span class="btn btn btn-yellow btn-xs">{{ user_membership[item.type] }}</span>
                            {% elseif item.type == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_PARTNER') %}
                                <span class="btn btn-primary btn-xs">{{ user_membership[item.type] }}</span>
                            {% elseif item.type == constant('\MBN\Data\Lib\Constant::USER_MEMBERSHIP_USER_BASIC') %}
                                <span class="btn btn-light-grey btn-xs">{{ user_membership[item.type] }}</span>
                            {% endif %}
                        </th>
                        <td>{{ user_exclusive[item.is_exclusive] }}</td>
                        <td>{{ date('d-m-Y H:i:s', strtotime(item.created_at)) }}</td>
                        <td>
                            {% if item.expired_at is defined and item.expired_at != '' %}
                                {{ date('d-m-Y H:i:s', strtotime(item.expired_at)) }}
                            {% endif %}
                        </td>
                        <td>{{ item.user_referral_name }}</td>
                        <td>{{ item.user_name }}</td>
                    </tr>
                {% endfor %}
            </tbody>
        </table>
    {% endif %}
{% endblock %}

{% block bottom_js %}
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/form.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/ajax-province.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript" src="{{ config.asset.backend_url ~ 'js/form-user-edit.js?' ~ config.asset.version }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-select').select2({
                allowClear: true,
                maximumSelectionSize: 1
            });
        });
    </script>
{% endblock %}