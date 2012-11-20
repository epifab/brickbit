<?php /* Smarty version Smarty-3.1.12, created on 2012-11-17 23:20:25
         compiled from "theme\standard\templates\layout\OutlineAjax.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2572850a80d10da3ce1-78590747%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '88bf8530b6546fc0819c3d0e68263b2df2bb152d' => 
    array (
      0 => 'theme\\standard\\templates\\layout\\OutlineAjax.tpl',
      1 => 1353194410,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2572850a80d10da3ce1-78590747',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a80d10e7c490_51189582',
  'variables' => 
  array (
    'private' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a80d10e7c490_51189582')) {function content_50a80d10e7c490_51189582($_smarty_tpl) {?><?php if (!is_callable('smarty_function_xmca_get_javascript')) include 'tpl_plugins\\function.xmca_get_javascript.php';
?><response
	type="<?php echo $_smarty_tpl->tpl_vars['private']->value['responseType'];?>
"
	name="<?php echo $_smarty_tpl->tpl_vars['private']->value['componentName'];?>
"
	address="<?php echo $_smarty_tpl->tpl_vars['private']->value['componentAddr'];?>
"
	title="<?php echo $_smarty_tpl->tpl_vars['private']->value['pageTitle'];?>
"
	formId="<?php echo $_smarty_tpl->tpl_vars['private']->value['formId'];?>
"
	contId="<?php echo $_smarty_tpl->tpl_vars['private']->value['contId'];?>
"
	id="<?php echo $_smarty_tpl->tpl_vars['private']->value['requestId'];?>
">
	<content><?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['private']->value['mainTemplate'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</content>
	<javascript><?php echo smarty_function_xmca_get_javascript(array(),$_smarty_tpl);?>
</javascript>
</response><?php }} ?>