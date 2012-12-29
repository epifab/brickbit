<?php /* Smarty version Smarty-3.1.12, created on 2012-12-29 17:27:10
         compiled from "module\core\templates\content-page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1868350df1e0b3635e2-10530162%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfcfe55358aa71a1f9fba7e3f255df95dd226e0a' => 
    array (
      0 => 'module\\core\\templates\\content-page.tpl',
      1 => 1356802008,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1868350df1e0b3635e2-10530162',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50df1e0b462161_15221793',
  'variables' => 
  array (
    'node' => 0,
    'page' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50df1e0b462161_15221793')) {function content_50df1e0b462161_15221793($_smarty_tpl) {?><?php if (!is_callable('smarty_block_protected')) include 'system/tpl-api\\block.protected.php';
if (!is_callable('smarty_block_link')) include 'system/tpl-api\\block.link.php';
if (!is_callable('smarty_modifier_t')) include 'system/tpl-api\\modifier.t.php';
?><?php if ($_smarty_tpl->tpl_vars['node']->value){?>
<div class="page">
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('protected', array('url'=>"node/edit/",'args'=>array("id"=>$_smarty_tpl->tpl_vars['node']->value->id))); $_block_repeat=true; echo smarty_block_protected(array('url'=>"node/edit/",'args'=>array("id"=>$_smarty_tpl->tpl_vars['node']->value->id)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<div class="page-controls">
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"page/edit/".((string)$_smarty_tpl->tpl_vars['node']->value->urn),'width'=>800,'height'=>420,'title'=>"Edit page",'args'=>array("id"=>$_smarty_tpl->tpl_vars['node']->value->id))); $_block_repeat=true; echo smarty_block_link(array('url'=>"page/edit/".((string)$_smarty_tpl->tpl_vars['node']->value->urn),'width'=>800,'height'=>420,'title'=>"Edit page",'args'=>array("id"=>$_smarty_tpl->tpl_vars['node']->value->id)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_t("Edit page");?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"page/edit/".((string)$_smarty_tpl->tpl_vars['node']->value->urn),'width'=>800,'height'=>420,'title'=>"Edit page",'args'=>array("id"=>$_smarty_tpl->tpl_vars['node']->value->id)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"article/add",'width'=>800,'height'=>550,'title'=>"New article",'args'=>array("parent_id"=>$_smarty_tpl->tpl_vars['node']->value->id))); $_block_repeat=true; echo smarty_block_link(array('url'=>"article/add",'width'=>800,'height'=>550,'title'=>"New article",'args'=>array("parent_id"=>$_smarty_tpl->tpl_vars['node']->value->id)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_t("New article");?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"article/add",'width'=>800,'height'=>550,'title'=>"New article",'args'=>array("parent_id"=>$_smarty_tpl->tpl_vars['node']->value->id)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"page/delete/".((string)$_smarty_tpl->tpl_vars['node']->value->urn),'confirm'=>true,'confirmTitle'=>"The page will be deleted",'title'=>"Delete page")); $_block_repeat=true; echo smarty_block_link(array('url'=>"page/delete/".((string)$_smarty_tpl->tpl_vars['node']->value->urn),'confirm'=>true,'confirmTitle'=>"The page will be deleted",'title'=>"Delete page"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_t("Delete page");?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"page/delete/".((string)$_smarty_tpl->tpl_vars['node']->value->urn),'confirm'=>true,'confirmTitle'=>"The page will be deleted",'title'=>"Delete page"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		</div>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_protected(array('url'=>"node/edit/",'args'=>array("id"=>$_smarty_tpl->tpl_vars['node']->value->id)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	<div class="page-body">
		<div><?php echo $_smarty_tpl->tpl_vars['node']->value->getRead("body");?>
</div>
	</div>

	<!-- CONTENUTI SEZIONE LIVELLO 1 -->
	<?php if (count($_smarty_tpl->tpl_vars['page']->value->articles)>0){?>
		<?php echo $_smarty_tpl->getSubTemplate ("PageContent.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php  $_smarty_tpl->tpl_vars['content'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['content']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['page']->value->contents; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['content']->key => $_smarty_tpl->tpl_vars['content']->value){
$_smarty_tpl->tpl_vars['content']->_loop = true;
?>
			<div class="content">
				<?php smarty_template_function_content_display($_smarty_tpl,array('content'=>$_smarty_tpl->tpl_vars['content']->value,'level'=>0));?>

			</div>
		<?php } ?>
	<?php }?>
</div>
<?php }?><?php }} ?>