<?php /* Smarty version Smarty-3.1.12, created on 2013-01-23 21:56:11
         compiled from "module\core\templates\edit-node-form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3004750f34b4a169fe7-93506193%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bd84e5cfa09fe8d29bde73757466dd9669e56693' => 
    array (
      0 => 'module\\core\\templates\\edit-node-form.tpl',
      1 => 1358978169,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3004750f34b4a169fe7-93506193',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50f34b4a74a272_73538295',
  'variables' => 
  array (
    'system' => 0,
    'node' => 0,
    'lang' => 0,
    'website' => 0,
    'text' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50f34b4a74a272_73538295')) {function content_50f34b4a74a272_73538295($_smarty_tpl) {?><?php if (!is_callable('smarty_function_path')) include 'system/tpl-api\\function.path.php';
if (!is_callable('smarty_modifier_t')) include 'system/tpl-api\\modifier.t.php';
if (!is_callable('smarty_block_edit_form')) include 'system/tpl-api\\block.edit_form.php';
if (!is_callable('smarty_function_theme_path')) include 'system/tpl-api\\function.theme_path.php';
if (!is_callable('smarty_function_de_form_error')) include 'system/tpl-api\\function.de_form_error.php';
if (!is_callable('smarty_function_de_submit_control')) include 'system/tpl-api\\function.de_submit_control.php';
?>

<form class="dataedit" id="fileupload" action="<?php echo smarty_function_path(array('url'=>"file/upload"),$_smarty_tpl);?>
" method="POST" enctype="multipart/form-data">
	<fieldset>
		<legend><?php echo smarty_modifier_t("Image");?>
</legend>
		<div class="de-row">
			<div class="de-label-wrapper">
				<span class="de-label"><?php echo smarty_modifier_t("Image");?>
</span>
			</div>
			<div class="de-input-wrapper">
				<input type="hidden" name="system[requestId]" value="<?php echo $_smarty_tpl->tpl_vars['system']->value['component']['requestId'];?>
"/>
				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
				<div class="row fileupload-buttonbar">
					<div class="span7">
						<!-- The fileinput-button span is used to style the file input field as button -->
						<span class="btn btn-success fileinput-button">
							<i class="icon-plus icon-white"></i>
							<span>Add file...</span>
							<input type="file" name="files[]" multiple>
						</span>
						<button type="submit" class="btn btn-primary start">
							<i class="icon-upload icon-white"></i>
							<span>Start upload</span>
						</button>
						<button type="reset" class="btn btn-warning cancel">
							<i class="icon-ban-circle icon-white"></i>
							<span>Cancel upload</span>
						</button>
					</div>
					<!-- The global progress information -->
					<div class="span5 fileupload-progress fade">
						<!-- The global progress bar -->
						<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
							<div class="bar" style="width:0%;"></div>
						</div>
						<!-- The extended global progress information -->
						<div class="progress-extended">&nbsp;</div>
					</div>
				</div>
				<!-- The loading indicator is shown during file processing -->
				<div classs="fileupload-loading"></div>
				<!-- The table listing the files available for upload/download -->
				<table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
			</div>
		</div>
	</fieldset>
</form>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"/></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
        </td>
    </tr>
{% } %}
</script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
<!-- jQuery Image Gallery -->
<script src="http://blueimp.github.com/jQuery-Image-Gallery/js/jquery.image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo smarty_function_path(array('url'=>"js/jquery-file-upload/js/jquery.iframe-transport.js"),$_smarty_tpl);?>
"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo smarty_function_path(array('url'=>"js/jquery-file-upload/js/jquery.fileupload.js"),$_smarty_tpl);?>
"></script>
<!-- The File Upload file processing plugin -->
<script src="<?php echo smarty_function_path(array('url'=>"js/jquery-file-upload/js/jquery.fileupload-fp.js"),$_smarty_tpl);?>
"></script>
<!-- The File Upload user interface plugin -->
<script src="<?php echo smarty_function_path(array('url'=>"js/jquery-file-upload/js/jquery.fileupload-ui.js"),$_smarty_tpl);?>
"></script>
<!-- The File Upload jQuery UI plugin -->
<script src="<?php echo smarty_function_path(array('url'=>"js/jquery-file-upload/js/jquery.fileupload-jui.js"),$_smarty_tpl);?>
"></script>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('edit_form', array()); $_block_repeat=true; echo smarty_block_edit_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<?php if ($_smarty_tpl->tpl_vars['node']->value->id){?>
	<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['node']->value->getEdit('id');?>
"/>
	<?php }?>

	<div class="dataedit">
		
		<fieldset>
			<legend>
				<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['system']->value['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
				<a href="#" id="node-lang-<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="node-lang-control show-hide-class<?php if ($_smarty_tpl->tpl_vars['lang']->value==$_smarty_tpl->tpl_vars['website']->value['defaultLang']){?> expanded<?php }?>"><img src="<?php echo smarty_function_theme_path(array('url'=>"img/lang/40/".((string)$_smarty_tpl->tpl_vars['lang']->value).".jpg"),$_smarty_tpl);?>
"/></a>
				<?php } ?>
			</legend>
			<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['system']->value['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
			<?php $_smarty_tpl->tpl_vars['text'] = new Smarty_variable("text_".((string)$_smarty_tpl->tpl_vars['lang']->value), null, 0);?>
			<?php $_smarty_tpl->_capture_stack[0][] = array("langDesc", null, null); ob_start(); ?><?php echo smarty_modifier_t("@lang",array('@lang'=>$_smarty_tpl->tpl_vars['lang']->value));?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
			<div class="node-lang node-lang-<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
">
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-urn"><?php echo smarty_modifier_t("URN");?>
</label>
					</div>
					<div class="de-input-wrapper">
						<input type="text" class="de-input xl" name="node[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.urn]" id="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-urn" value="<?php echo $_smarty_tpl->tpl_vars['node']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('urn');?>
"/>
						<div class="de-info">
							<p>
								<?php echo smarty_modifier_t("Once you choose a URN you shouldn't change it anymore.");?>
<br/>
								<?php echo smarty_modifier_t("In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself.");?>

								<?php echo smarty_modifier_t("Each word should be separeted by the dash characted.");?>

							</p>
							<p>
								<?php echo smarty_modifier_t("Please note also that two different contents, translated in @lang, must have two different URNs.",array('@lang'=>Smarty::$_smarty_vars['capture']['langDesc']));?>

							</p>
						</div>
						<?php echo smarty_function_de_form_error(array('path'=>((string)$_smarty_tpl->tpl_vars['text']->value).".urn"),$_smarty_tpl);?>

					</div>
				</div>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-title"><?php echo smarty_modifier_t("Title");?>
</label>
					</div>
					<div class="de-input-wrapper">
						<input class="de-input l" type="text" id="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-title" name="node[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.title]" value="<?php echo $_smarty_tpl->tpl_vars['node']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('title');?>
"/>
						<?php echo smarty_function_de_form_error(array('path'=>((string)$_smarty_tpl->tpl_vars['text']->value).".title"),$_smarty_tpl);?>

					</div>
				</div>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-subtitle"><?php echo smarty_modifier_t("Subtitle");?>
</label>
					</div>
					<div class="de-input-wrapper">
						<input class="de-input xl" type="text" id="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-subtitle" name="node[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.subtitle]" value="<?php echo $_smarty_tpl->tpl_vars['node']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('subtitle');?>
"/>
						<?php echo smarty_function_de_form_error(array('path'=>((string)$_smarty_tpl->tpl_vars['text']->value).".subtitle"),$_smarty_tpl);?>

					</div>
				</div>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-body"><?php echo smarty_modifier_t("Body");?>
</label>
					</div>
					<div class="de-input-wrapper">
						<textarea class="de-input xxl rich-text" id="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-body" name="node[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.body]"><?php echo $_smarty_tpl->tpl_vars['node']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('body');?>
</textarea>
						<?php echo smarty_function_de_form_error(array('path'=>((string)$_smarty_tpl->tpl_vars['text']->value).".body"),$_smarty_tpl);?>

					</div>
				</div>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-preview"><?php echo smarty_modifier_t("Preview");?>
</label>
					</div>
					<div class="de-input-wrapper">
						<textarea class="de-input xxl rich-text" id="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-preview" name="node[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.preview]"><?php echo $_smarty_tpl->tpl_vars['node']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('preview');?>
</textarea>
						<?php echo smarty_function_de_form_error(array('path'=>((string)$_smarty_tpl->tpl_vars['text']->value).".preview"),$_smarty_tpl);?>

					</div>
				</div>
			</div>
			<?php } ?>
		</fieldset>

		
		<fieldset class="de-fieldset">
			<legend><?php echo smarty_modifier_t("Content access");?>
</legend>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-users"><?php echo smarty_modifier_t("Content admininstrators");?>
</label>
				</div>
				<div class="de-input-wrapper">
					<input class="de-input xl" type="text" name="node[record_mode.users]" id="edit-node-record_mode-users" value=""/>
					<?php echo smarty_function_de_form_error(array('path'=>"record_mode.users"),$_smarty_tpl);?>

				</div>
			</div>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-read_mode"><?php echo smarty_modifier_t("Read access");?>
</label>
				</div>
				<div class="de-input-wrapper">
					<select class="de-input l" id="edit-node-record_mode-read_mode" name="node[record_mode.read_mode]">
						<option value="2"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->read_mode==2){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner only");?>
</option>
						<option value="3"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->read_mode==3){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner and group");?>
</option>
						<option value="4"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->read_mode==4){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Public content");?>
</option>
					</select>
					<?php echo smarty_function_de_form_error(array('path'=>"record_mode.read_mode"),$_smarty_tpl);?>

				</div>
			</div>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-edit_mode"><?php echo smarty_modifier_t("Edit access");?>
</label>
				</div>
				<div class="de-input-wrapper">
					<select class="de-input l" id="edit-node-record_mode-edit_mode" name="node[record_mode.edit_mode]">
						<option value="1"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->edit_mode==1){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Nobody");?>
</option>
						<option value="2"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->edit_mode==2){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner only");?>
</option>
						<option value="3"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->edit_mode==3){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner and group");?>
</option>
					</select>
					<?php echo smarty_function_de_form_error(array('path'=>"record_mode.edit_mode"),$_smarty_tpl);?>

				</div>
			</div>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-delete_mode"><?php echo smarty_modifier_t("Delete access");?>
</label>
				</div>
				<div class="de-input-wrapper">
					<select class="de-input l" id="edit-node-record_mode-delete_mode" name="node[record_mode.delete_mode]">
						<option value="1"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->edit_mode==1){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Nobody");?>
</option>
						<option value="2"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->edit_mode==2){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner only");?>
</option>
						<option value="3"<?php if ($_smarty_tpl->tpl_vars['node']->value->record_mode->edit_mode==3){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner and group");?>
</option>
					</select>
					<?php echo smarty_function_de_form_error(array('path'=>"record_mode.delete_mode"),$_smarty_tpl);?>

				</div>
			</div>
		</fieldset>
		<?php echo smarty_function_de_submit_control(array(),$_smarty_tpl);?>

	</div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_edit_form(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>