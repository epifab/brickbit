<?php /* Smarty version Smarty-3.1.12, created on 2013-01-26 01:21:36
         compiled from "module\core\templates\notify.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1912151032fa04c18f8-63651906%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '572bbfd97a11347c4326c5de86f1f8b9ee41caee' => 
    array (
      0 => 'module\\core\\templates\\notify.tpl',
      1 => 1356598771,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1912151032fa04c18f8-63651906',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'system' => 0,
    'message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51032fa0518434_77350781',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51032fa0518434_77350781')) {function content_51032fa0518434_77350781($_smarty_tpl) {?><div class="notify <?php if ($_smarty_tpl->tpl_vars['system']->value['responseType']=='ERROR'){?>error<?php }else{ ?>success<?php }?>">
	<h2><?php echo (($tmp = @$_smarty_tpl->tpl_vars['message']->value['title'])===null||$tmp==='' ? 'Done' : $tmp);?>
</h2>
	<?php echo (($tmp = @$_smarty_tpl->tpl_vars['message']->value['body'])===null||$tmp==='' ? '' : $tmp);?>

</div><?php }} ?>