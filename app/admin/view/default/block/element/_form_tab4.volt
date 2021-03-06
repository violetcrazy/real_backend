<div id="form_tab5_error_message" class="alert alert-danger" style="display: none;"></div>

<h3>Tiếng Việt</h3>
<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-8">
        <input type="text" name="meta_title" class="form-control" value="{% if result.meta_title is defined %}{{ result.meta_title }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_meta_title" class="error_message"></span></div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Mô tả
    </label>
    <div class="col-sm-8">
        <input type="text" name="meta_description" class="form-control" value="{% if result.meta_description is defined %}{{ result.meta_description }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_meta_description" class="error_message"></span></div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Từ khoá
    </label>
    <div class="col-sm-8">
        <input type="text" name="meta_keywords" class="form-control" value="{% if result.meta_keywords is defined %}{{ result.meta_keywords }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_meta_keywords" class="error_message"></span></div>
    </div>
</div>

<h3>Tiếng Anh</h3>
<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
        Tiêu đề
    </label>
    <div class="col-sm-8">
        <input type="text" name="meta_title_eng" class="form-control" value="{% if result.meta_title_eng is defined %}{{ result.meta_title_eng }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_meta_title_eng" class="error_message"></span></div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Mô tả
    </label>
    <div class="col-sm-8">
        <input type="text" name="meta_description_eng" class="form-control" value="{% if result.meta_description_eng is defined %}{{ result.meta_description_eng }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_meta_description_eng" class="error_message"></span></div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        Từ khoá
    </label>
    <div class="col-sm-8">
        <input type="text" name="meta_keywords_eng" class="form-control" value="{% if result.meta_keywords_eng is defined %}{{ result.meta_keywords_eng }}{% endif %}" />
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_meta_keywords_eng" class="error_message"></span></div>
    </div>
</div>
