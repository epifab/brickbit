<?php /* Smarty version Smarty-3.1.12, created on 2012-11-18 17:59:10
         compiled from "theme\standard\templates\layout\Success.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1206050a921ee0acc16-16749676%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4e665b67fc826b6a5569bd0c85b6810e8103e764' => 
    array (
      0 => 'theme\\standard\\templates\\layout\\Success.tpl',
      1 => 1353011075,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1206050a921ee0acc16-16749676',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'successTitle' => 0,
    'successMessage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a921ee1d8d80_82096216',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a921ee1d8d80_82096216')) {function content_50a921ee1d8d80_82096216($_smarty_tpl) {?><div class="notify_success">
	<h2><?php echo (($tmp = @$_smarty_tpl->tpl_vars['successTitle']->value)===null||$tmp==='' ? 'Operazione completata' : $tmp);?>
</h2>
	<?php echo (($tmp = @$_smarty_tpl->tpl_vars['successMessage']->value)===null||$tmp==='' ? '' : $tmp);?>

</div><?php }} ?>