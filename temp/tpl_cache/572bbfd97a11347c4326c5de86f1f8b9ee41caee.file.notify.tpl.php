<?php /* Smarty version Smarty-3.1.12, created on 2012-12-29 16:52:46
         compiled from "module\core\templates\notify.tpl" */ ?>
<?php /*%%SmartyHeaderCode:192650df1fded04a81-15659880%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
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
  'nocache_hash' => '192650df1fded04a81-15659880',
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
  'unifunc' => 'content_50df1fded6ceb1_39815190',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50df1fded6ceb1_39815190')) {function content_50df1fded6ceb1_39815190($_smarty_tpl) {?><div class="notify <?php if ($_smarty_tpl->tpl_vars['system']->value['responseType']=='ERROR'){?>error<?php }else{ ?>success<?php }?>">
	<h2><?php echo (($tmp = @$_smarty_tpl->tpl_vars['message']->value['title'])===null||$tmp==='' ? 'Done' : $tmp);?>
</h2>
	<?php echo (($tmp = @$_smarty_tpl->tpl_vars['message']->value['body'])===null||$tmp==='' ? '' : $tmp);?>

</div><?php }} ?>