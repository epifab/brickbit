<?php /* Smarty version Smarty-3.1.12, created on 2012-11-18 17:59:42
         compiled from "theme\standard\templates\Login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2037150a9220e22f089-31372520%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e031ee1d15dce5154d1e9d276aaeefc266466ac' => 
    array (
      0 => 'theme\\standard\\templates\\Login.tpl',
      1 => 1353011075,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2037150a9220e22f089-31372520',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'private' => 0,
    'errorMessage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a9220e2dcab9_10592156',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a9220e2dcab9_10592156')) {function content_50a9220e2dcab9_10592156($_smarty_tpl) {?><div class="xmca_login_form">
	<form id="<?php echo $_smarty_tpl->tpl_vars['private']->value['formId'];?>
" class="login" method="post" action="<?php echo $_smarty_tpl->tpl_vars['private']->value['componentAddr'];?>
">
		<input type="hidden" name="login_form" value="1"/>
		<div><label for="username">Email</label><br/><input type="text" id="username" name="username"/></div>
		<div><label for="userpass">Password</label><br/><input type="password" id="userpass" name="userpass"/></div>
		<?php if (isset($_smarty_tpl->tpl_vars['errorMessage']->value)){?>
			<p class="alert"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorMessage']->value)===null||$tmp==='' ? '' : $tmp);?>
</p>
		<?php }?>
	</form>
</div><?php }} ?>