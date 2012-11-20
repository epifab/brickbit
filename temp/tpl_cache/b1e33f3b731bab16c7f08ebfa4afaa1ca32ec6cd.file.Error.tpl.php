<?php /* Smarty version Smarty-3.1.12, created on 2012-11-18 01:09:38
         compiled from "theme\standard\templates\layout\Error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:401850a835522779b5-35516760%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b1e33f3b731bab16c7f08ebfa4afaa1ca32ec6cd' => 
    array (
      0 => 'theme\\standard\\templates\\layout\\Error.tpl',
      1 => 1353011075,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '401850a835522779b5-35516760',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'errorTitle' => 0,
    'errorMessage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a835522d6602_27779869',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a835522d6602_27779869')) {function content_50a835522d6602_27779869($_smarty_tpl) {?><div class="notify_error">
	<h2><?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorTitle']->value)===null||$tmp==='' ? 'Errore interno dello script' : $tmp);?>
</h2>
	<?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorMessage']->value)===null||$tmp==='' ? 'Si è verificato un errore sconosciuto.' : $tmp);?>

</div><?php }} ?>