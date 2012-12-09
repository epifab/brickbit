<?php /* Smarty version Smarty-3.1.12, created on 2012-12-09 21:50:38
         compiled from "theme\dark\templates\layout\Error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:948050c507ae4d1f59-25966760%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '07edfa1b979550d28c43f96c8bf4d9e90770660f' => 
    array (
      0 => 'theme\\dark\\templates\\layout\\Error.tpl',
      1 => 1353708267,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '948050c507ae4d1f59-25966760',
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
  'unifunc' => 'content_50c507ae523498_01553046',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c507ae523498_01553046')) {function content_50c507ae523498_01553046($_smarty_tpl) {?><div class="notify_error">
	<h2><?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorTitle']->value)===null||$tmp==='' ? 'Errore interno dello script' : $tmp);?>
</h2>
	<?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorMessage']->value)===null||$tmp==='' ? 'Si è verificato un errore sconosciuto.' : $tmp);?>

</div><?php }} ?>