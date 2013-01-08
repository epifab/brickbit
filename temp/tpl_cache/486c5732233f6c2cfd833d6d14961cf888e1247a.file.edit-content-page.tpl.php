<?php /* Smarty version Smarty-3.1.12, created on 2013-01-08 01:49:04
         compiled from "module\core\templates\edit-content-page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:344550e85bf14c6ed3-03649821%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '486c5732233f6c2cfd833d6d14961cf888e1247a' => 
    array (
      0 => 'module\\core\\templates\\edit-content-page.tpl',
      1 => 1357609743,
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
    'recordset' => 0,
    'errors' => 0,
    'system' => 0,
    'lang' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50e85bf14c87b1_72270052')) {function content_50e85bf14c87b1_72270052($_smarty_tpl) {?><?php if (!is_callable('smarty_block_edit_form')) include 'system/tpl-api\\block.edit_form.php';
if (!is_callable('smarty_modifier_t')) include 'system/tpl-api\\modifier.t.php';
if (!is_callable('smarty_function_theme_path')) include 'system/tpl-api\\function.theme_path.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('edit_form', array()); $_block_repeat=true; echo smarty_block_edit_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<?php if ($_smarty_tpl->tpl_vars['recordset']->value->id){?>
	<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit('id');?>
"/>
	<?php }?>

	<table class="dataedit">
		<tr>
			<th class="label">URN</th>
			<td class="input">
				<input class="text xl" onchange="ChangeUrl()" id="edit-content-input-urn" type="text" name="recordset[urn]" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit('urn');?>
"/>
				<p class="info">
					<?php echo smarty_modifier_t("Once you choose a URN you shouldn't change it anymore.");?>
<br/>
					<?php echo smarty_modifier_t("In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself.");?>

					<?php echo smarty_modifier_t("Each word should be separeted by the dash characted.");?>

				</p>
				<?php if (array_key_exists("urn",$_smarty_tpl->tpl_vars['errors']->value)){?>
				<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['urn'];?>
</p>
				<?php }?>
			</td>
		</tr>
		<tr>
			<th class="label"><?php echo smarty_modifier_t("Language");?>
</th>
			<td class="input">
				<div class="langs-input">
					<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['system']->value['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
					<a href="javascript:EditPageLang('<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
')" id="lang-control-<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="lang_control"><img src="<?php echo smarty_function_theme_path(array('url'=>"img/lang/40/".((string)$_smarty_tpl->tpl_vars['lang']->value).".jpg"),$_smarty_tpl);?>
"/></a>
					<?php } ?>
				</div>
			</td>
		</tr>
		
		<tr>
			<th colspan="2" class="row">Gestione permessi</th>
		</tr>
		<tr>
			<th class="label"><?php echo smarty_modifier_t("Content admins");?>
</th>
			<td class="input">
				<input type="text" name="recordset[record_mode_users]" id="edit-record-mode-users" value=""/>
				<?php if (array_key_exists("content_sorting",$_smarty_tpl->tpl_vars['errors']->value)){?>
				<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_sorting'];?>
</p>
				<?php }?>
			</td>
		</tr>

		<tr>
			<th class="label"><?php echo smarty_modifier_t("Read access");?>
</th>
			<td class="input">
				<select class="xl" name="recordset[record_mode.read_mode]">
					<option value="2"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->read_mode==2){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner only");?>
</option>
					<option value="3"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->read_mode==3){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner and group");?>
</option>
					<option value="4"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->read_mode==4){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Public content");?>
</option>
				</select>
				<?php if (array_key_exists("content_sorting",$_smarty_tpl->tpl_vars['errors']->value)){?>
				<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_sorting'];?>
</p>
				<?php }?>
			</td>
		</tr>

		<tr>
			<th class="label"><?php echo smarty_modifier_t("Edit access");?>
</th>
			<td class="input">
				<select class="xl" name="recordset[record_mode.edit_mode]">
					<option value="2"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->edit_mode==2){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner only");?>
</option>
					<option value="3"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->edit_mode==3){?> selected="selected"<?php }?>><?php echo smarty_modifier_t("Owner and group");?>
</option>
				</select>
				<?php if (array_key_exists("content_sorting",$_smarty_tpl->tpl_vars['errors']->value)){?>
				<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_sorting'];?>
</p>
				<?php }?>
			</td>
		</tr>

		<?php if (!$_smarty_tpl->tpl_vars['system']->value['ajax']){?>
		<tr>
			<th class="controls" colspan="2"><input type="submit" class="xmca_control xxl" value="Invia"/></th>
		</tr>
		<?php }?>
	</table>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_edit_form(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>