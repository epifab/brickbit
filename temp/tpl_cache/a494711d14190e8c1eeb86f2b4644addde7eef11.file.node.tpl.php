<?php /* Smarty version Smarty-3.1.12, created on 2013-01-30 00:14:13
         compiled from "module\core\templates\node.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21919510401aa2d25f6-80870920%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a494711d14190e8c1eeb86f2b4644addde7eef11' => 
    array (
      0 => 'module\\core\\templates\\node.tpl',
      1 => 1359504850,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21919510401aa2d25f6-80870920',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_510401aa461249_47172414',
  'variables' => 
  array (
    'node' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_510401aa461249_47172414')) {function content_510401aa461249_47172414($_smarty_tpl) {?><?php if (!is_callable('smarty_block_protected')) include 'system/tpl-api\\block.protected.php';
if (!is_callable('smarty_block_link')) include 'system/tpl-api\\block.link.php';
if (!is_callable('smarty_modifier_t')) include 'system/tpl-api\\modifier.t.php';
?><?php if ($_smarty_tpl->tpl_vars['node']->value){?>
<div class="node node-<?php echo $_smarty_tpl->tpl_vars['node']->value->type;?>
 node-<?php echo $_smarty_tpl->tpl_vars['node']->value->id;?>
">
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('protected', array('url'=>$_smarty_tpl->tpl_vars['node']->value->edit_url)); $_block_repeat=true; echo smarty_block_protected(array('url'=>$_smarty_tpl->tpl_vars['node']->value->edit_url), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<div class="page-controls">
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>$_smarty_tpl->tpl_vars['node']->value->edit_url,'width'=>800,'height'=>420,'title'=>"Edit ".((string)$_smarty_tpl->tpl_vars['node']->value->type))); $_block_repeat=true; echo smarty_block_link(array('url'=>$_smarty_tpl->tpl_vars['node']->value->edit_url,'width'=>800,'height'=>420,'title'=>"Edit ".((string)$_smarty_tpl->tpl_vars['node']->value->type)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_t("Edit ".((string)$_smarty_tpl->tpl_vars['node']->value->type));?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>$_smarty_tpl->tpl_vars['node']->value->edit_url,'width'=>800,'height'=>420,'title'=>"Edit ".((string)$_smarty_tpl->tpl_vars['node']->value->type)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>$_smarty_tpl->tpl_vars['node']->value->delete_url,'confirm'=>true,'confirmTitle'=>"The ".((string)$_smarty_tpl->tpl_vars['node']->value->type)." will be deleted",'title'=>"Delete ".((string)$_smarty_tpl->tpl_vars['node']->value->type))); $_block_repeat=true; echo smarty_block_link(array('url'=>$_smarty_tpl->tpl_vars['node']->value->delete_url,'confirm'=>true,'confirmTitle'=>"The ".((string)$_smarty_tpl->tpl_vars['node']->value->type)." will be deleted",'title'=>"Delete ".((string)$_smarty_tpl->tpl_vars['node']->value->type)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_t("Delete ".((string)$_smarty_tpl->tpl_vars['node']->value->type));?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>$_smarty_tpl->tpl_vars['node']->value->delete_url,'confirm'=>true,'confirmTitle'=>"The ".((string)$_smarty_tpl->tpl_vars['node']->value->type)." will be deleted",'title'=>"Delete ".((string)$_smarty_tpl->tpl_vars['node']->value->type)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			
		</div>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_protected(array('url'=>$_smarty_tpl->tpl_vars['node']->value->edit_url), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	<?php if ($_smarty_tpl->tpl_vars['node']->value->text){?>
		<div class="node-text" lang="<?php echo $_smarty_tpl->tpl_vars['node']->value->text->lang;?>
">
			<?php if ($_smarty_tpl->tpl_vars['node']->value->text->title){?>
				<h1 class="node-text-title"><?php echo $_smarty_tpl->tpl_vars['node']->value->text->title;?>
</h1>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['node']->value->text->subtitle){?>
				<h2 class="node-text-subtitle"><?php echo $_smarty_tpl->tpl_vars['node']->value->text->subtitle;?>
</h1>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['node']->value->text->preview){?>
				<div class="node-text-preview"><?php echo $_smarty_tpl->tpl_vars['node']->value->text->preview;?>
</div>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['node']->value->text->body){?>
				<div class="node-text-body"><?php echo $_smarty_tpl->tpl_vars['node']->value->text->body;?>
</div>
			<?php }?>
		</div>
	<?php }?>
	
	<!-- CONTENUTI SEZIONE LIVELLO 1 -->
	<?php  $_smarty_tpl->tpl_vars['n'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['n']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['node']->value->children; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['n']->key => $_smarty_tpl->tpl_vars['n']->value){
$_smarty_tpl->tpl_vars['n']->_loop = true;
?>
	<?php } ?>
</div>
<?php }?><?php }} ?>