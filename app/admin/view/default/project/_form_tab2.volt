<div id="form_tab2_error_message" class="alert alert-danger" style="display: none;"></div>

<h3>Tiếng Việt</h3>
<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
    </label>
    <div class="col-sm-8">
        <textarea id="form_tab2_description"  name="description" class="ckeditor form-control">{% if project.description is defined %}{{ project.description }}{% endif %}</textarea>
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_description" class="error_message"></span></div>
    </div>
</div>

<h3>Tiếng Anh</h3>
<hr />

<div class="form-group">
    <label class="col-sm-2 control-label">
    </label>
    <div class="col-sm-8">
        <textarea id="form_tab2_description_eng"  name="description_eng" class="ckeditor form-control">{% if project.description_eng is defined %}{{ project.description_eng }}{% endif %}</textarea>
        <div class="has-error"><span class="help-block" style="margin-bottom: 0 !important;" id="error_description_eng" class="error_message"></span></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        CKEDITOR.replace('form_tab2_description', {
			height: 300
		});
        CKEDITOR.replace('form_tab2_description_eng', {
			height: 300
		});
    });
</script>
