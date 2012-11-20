<?php /* Smarty version Smarty-3.1.12, created on 2012-11-18 17:59:10
         compiled from "theme\standard\templates\PageNotFound.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3187450a921ee43db72-73392458%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '811e64ed370e78b030c0ac35ab452ebb66a7e328' => 
    array (
      0 => 'theme\\standard\\templates\\PageNotFound.tpl',
      1 => 1353011075,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3187450a921ee43db72-73392458',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a921ee4d1c67_06766507',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a921ee4d1c67_06766507')) {function content_50a921ee4d1c67_06766507($_smarty_tpl) {?><?php if (!is_callable('smarty_block_xmca_read_form')) include 'tpl_plugins\\block.xmca_read_form.php';
if (!is_callable('smarty_block_xmca_read_content')) include 'tpl_plugins\\block.xmca_read_content.php';
if (!is_callable('smarty_modifier_lang')) include 'tpl_plugins\\modifier.lang.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_read_form', array()); $_block_repeat=true; echo smarty_block_xmca_read_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_read_form(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_read_content', array('element'=>"div")); $_block_repeat=true; echo smarty_block_xmca_read_content(array('element'=>"div"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<div class="page">
	<h2 class="page_title"><?php echo smarty_modifier_lang("page_not_found");?>
</h2>
	<div class="page_body">
		<div><?php echo smarty_modifier_lang("page_not_found2");?>
</div>
	</div>
</div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_read_content(array('element'=>"div"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>