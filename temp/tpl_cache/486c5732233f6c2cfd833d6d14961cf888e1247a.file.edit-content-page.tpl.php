<?php /* Smarty version Smarty-3.1.12, created on 2013-01-11 19:35:23
         compiled from "module\core\templates\edit-content-page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:344550e85bf14c6ed3-03649821%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '486c5732233f6c2cfd833d6d14961cf888e1247a' => 
    array (
      0 => 'module\\core\\templates\\edit-content-page.tpl',
      1 => 1357932921,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '344550e85bf14c6ed3-03649821',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50e85bf14c87b1_72270052',
  'variables' => 
  array (
    'node' => 0,
    'system' => 0,
    'lang' => 0,
    'website' => 0,
    'text' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50e85bf14c87b1_72270052')) {function content_50e85bf14c87b1_72270052($_smarty_tpl) {?><?php if (!is_callable('smarty_block_edit_form')) include 'system/tpl-api\\block.edit_form.php';
if (!is_callable('smarty_modifier_t')) include 'system/tpl-api\\modifier.t.php';
if (!is_callable('smarty_function_de_form_error')) include 'system/tpl-api\\function.de_form_error.php';
if (!is_callable('smarty_function_theme_path')) include 'system/tpl-api\\function.theme_path.php';
if (!is_callable('smarty_function_de_submit_control')) include 'system/tpl-api\\function.de_submit_control.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('edit_form', array()); $_block_repeat=true; echo smarty_block_edit_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<?php if ($_smarty_tpl->tpl_vars['node']->value->id){?>
	<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['node']->value->getEdit('id');?>
"/>
	<?php }?>

	<div class="dataedit">
		<div class="de-row">
			<div class="de-label-wrapper">
				<label class="de-label" for="edit-node-urn"><?php echo smarty_modifier_t("URN");?>
</label>
			</div>
			<div class="de-input-wrapper">
				<input type="text" class="de-input xl" name="node[urn]" id="edit-node-urn" value="<?php echo $_smarty_tpl->tpl_vars['node']->value->getEdit('urn');?>
"/>
				<p class="de-info">
					<?php echo smarty_modifier_t("Once you choose a URN you shouldn't change it anymore.");?>
<br/>
					<?php echo smarty_modifier_t("In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself.");?>

					<?php echo smarty_modifier_t("Each word should be separeted by the dash characted.");?>

				</p>
				<?php echo smarty_function_de_form_error(array('path'=>"urn"),$_smarty_tpl);?>

			</div>
		</div>
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
				<?php if ($_smarty_tpl->tpl_vars['lang']->value!=$_smarty_tpl->tpl_vars['website']->value['defaultLang']){?>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-urn"><?php echo smarty_modifier_t("URN alias");?>
</label>
					</div>
					<div class="de-input-wrapper">
						<input type="text" class="de-input xl" name="node[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.urn]" id="edit-node-<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
-urn" value="<?php echo $_smarty_tpl->tpl_vars['node']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('urn');?>
"/>
						<p class="de-info">
							<?php echo smarty_modifier_t("URN translation.");?>
<br/>
							<?php echo smarty_modifier_t("Please follow the same instruction as the general URN.");?>
<br/>
							<?php echo smarty_modifier_t("Please note also that two different contents, translated in @lang, must have two different URNs.",array('@lang'=>Smarty::$_smarty_vars['capture']['langDesc']));?>

						</p>
						<?php echo smarty_function_de_form_error(array('path'=>((string)$_smarty_tpl->tpl_vars['text']->value).".urn"),$_smarty_tpl);?>

					</div>
				</div>
				<?php }?>
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