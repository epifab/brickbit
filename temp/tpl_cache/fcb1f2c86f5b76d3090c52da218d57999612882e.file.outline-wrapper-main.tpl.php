<?php /* Smarty version Smarty-3.1.12, created on 2013-01-26 10:25:34
         compiled from "module\core\templates\outline-wrapper-main.tpl" */ ?>
<?php /*%%SmartyHeaderCode:241895103af1e6bb9a7-44248559%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fcb1f2c86f5b76d3090c52da218d57999612882e' => 
    array (
      0 => 'module\\core\\templates\\outline-wrapper-main.tpl',
      1 => 1356640449,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '241895103af1e6bb9a7-44248559',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'system' => 0,
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5103af1e73c932_00622348',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5103af1e73c932_00622348')) {function content_5103af1e73c932_00622348($_smarty_tpl) {?><?php if (!is_callable('smarty_function_edit_form_id')) include 'system/tpl-api\\function.edit_form_id.php';
if (!is_callable('smarty_function_panel_form_id')) include 'system/tpl-api\\function.panel_form_id.php';
if (!is_callable('smarty_function_panel_form_name')) include 'system/tpl-api\\function.panel_form_name.php';
if (!is_callable('smarty_function_javascript')) include 'system/tpl-api\\function.javascript.php';
?><response
	type="<?php echo $_smarty_tpl->tpl_vars['system']->value['responseType'];?>
"
	url="<?php echo $_smarty_tpl->tpl_vars['system']->value['component']['url'];?>
"
	title="<?php echo $_smarty_tpl->tpl_vars['page']->value['title'];?>
"
	editFormId="<?php echo smarty_function_edit_form_id(array(),$_smarty_tpl);?>
"
	panelFormId="<?php echo smarty_function_panel_form_id(array(),$_smarty_tpl);?>
"
	panelFormName="<?php echo smarty_function_panel_form_name(array(),$_smarty_tpl);?>
"
	id="<?php echo $_smarty_tpl->tpl_vars['system']->value['component']['requestId'];?>
">
	<content><?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['system']->value['templates']['main'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</content>
	<javascript><?php echo smarty_function_javascript(array(),$_smarty_tpl);?>
</javascript>
</response><?php }} ?>