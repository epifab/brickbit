<?php /* Smarty version Smarty-3.1.12, created on 2012-12-29 16:53:01
         compiled from "module\core\templates\login-form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1110350df1fedbb7cf1-62473098%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6eac13088905d15aecbf2c904e9327697e21dd13' => 
    array (
      0 => 'module\\core\\templates\\login-form.tpl',
      1 => 1356361572,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1110350df1fedbb7cf1-62473098',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'system' => 0,
    'errorMessage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50df1fedc37c87_04068612',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50df1fedc37c87_04068612')) {function content_50df1fedc37c87_04068612($_smarty_tpl) {?><?php if (!is_callable('smarty_block_panel')) include 'system/tpl-api\\block.panel.php';
if (!is_callable('smarty_function_edit_form_id')) include 'system/tpl-api\\function.edit_form_id.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('panel', array('name'=>"main",'class'=>"login-form")); $_block_repeat=true; echo smarty_block_panel(array('name'=>"main",'class'=>"login-form"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<form id="<?php echo smarty_function_edit_form_id(array(),$_smarty_tpl);?>
" class="login" method="post" action="<?php echo $_smarty_tpl->tpl_vars['system']->value['component']['url'];?>
">
		<input type="hidden" name="login_form" value="1"/>
		<div><label for="username">Email</label><br/><input type="text" id="username" name="username"/></div>
		<div><label for="userpass">Password</label><br/><input type="password" id="userpass" name="userpass"/></div>
		<?php if (isset($_smarty_tpl->tpl_vars['errorMessage']->value)){?>
			<p class="alert"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorMessage']->value)===null||$tmp==='' ? '' : $tmp);?>
</p>
		<?php }?>
	</form>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_panel(array('name'=>"main",'class'=>"login-form"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>