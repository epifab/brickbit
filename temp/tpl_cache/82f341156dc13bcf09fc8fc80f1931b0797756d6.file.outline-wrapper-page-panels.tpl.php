<?php /* Smarty version Smarty-3.1.12, created on 2013-01-04 22:52:07
         compiled from "module\core\templates\outline-wrapper-page-panels.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1550150e75d17292107-32653067%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '82f341156dc13bcf09fc8fc80f1931b0797756d6' => 
    array (
      0 => 'module\\core\\templates\\outline-wrapper-page-panels.tpl',
      1 => 1356640194,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1550150e75d17292107-32653067',
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
  'unifunc' => 'content_50e75d1736a6b2_26167693',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50e75d1736a6b2_26167693')) {function content_50e75d1736a6b2_26167693($_smarty_tpl) {?><?php if (!is_callable('smarty_function_edit_form_id')) include 'system/tpl-api\\function.edit_form_id.php';
if (!is_callable('smarty_function_panel_form_id')) include 'system/tpl-api\\function.panel_form_id.php';
if (!is_callable('smarty_function_panel_form_name')) include 'system/tpl-api\\function.panel_form_name.php';
if (!is_callable('smarty_block_panels')) include 'system/tpl-api\\block.panels.php';
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
	<content><?php $_smarty_tpl->smarty->_tag_stack[] = array('panels', array()); $_block_repeat=true; echo smarty_block_panels(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php if ($_smarty_tpl->tpl_vars['system']->value['templates']['outline']){?><?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['system']->value['templates']['outline'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }else{ ?><?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['system']->value['templates']['main'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_panels(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</content>
	<javascript><?php echo smarty_function_javascript(array(),$_smarty_tpl);?>
</javascript>
</response><?php }} ?>