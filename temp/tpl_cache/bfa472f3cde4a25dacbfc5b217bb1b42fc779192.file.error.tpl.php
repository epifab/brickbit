<?php /* Smarty version Smarty-3.1.12, created on 2012-12-20 00:42:16
         compiled from "module\core\templates\error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:897050d25ee86083b6-07701033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bfa472f3cde4a25dacbfc5b217bb1b42fc779192' => 
    array (
      0 => 'module\\core\\templates\\error.tpl',
      1 => 1355964126,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '897050d25ee86083b6-07701033',
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
  'unifunc' => 'content_50d25ee86881b3_97261416',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50d25ee86881b3_97261416')) {function content_50d25ee86881b3_97261416($_smarty_tpl) {?><div class="notify_error">
	<h2><?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorTitle']->value)===null||$tmp==='' ? 'Errore interno dello script' : $tmp);?>
</h2>
	<?php echo (($tmp = @$_smarty_tpl->tpl_vars['errorMessage']->value)===null||$tmp==='' ? 'Si è verificato un errore sconosciuto.' : $tmp);?>

</div><?php }} ?>