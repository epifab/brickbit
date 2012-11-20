<?php /* Smarty version Smarty-3.1.12, created on 2012-11-17 22:17:40
         compiled from "theme\standard\templates\Page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:812550a80d04961d40-34602905%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c7670d49eecf7c582ad28c15fa0e2e6988eaa843' => 
    array (
      0 => 'theme\\standard\\templates\\Page.tpl',
      1 => 1353011075,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '812550a80d04961d40-34602905',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a80d04b41592_88053365',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a80d04b41592_88053365')) {function content_50a80d04b41592_88053365($_smarty_tpl) {?><?php if (!is_callable('smarty_block_xmca_read_form')) include 'tpl_plugins\\block.xmca_read_form.php';
if (!is_callable('smarty_block_xmca_read_content')) include 'tpl_plugins\\block.xmca_read_content.php';
if (!is_callable('smarty_block_xmca_restricted_area')) include 'tpl_plugins\\block.xmca_restricted_area.php';
if (!is_callable('smarty_function_xmca_control')) include 'tpl_plugins\\function.xmca_control.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_read_form', array()); $_block_repeat=true; echo smarty_block_xmca_read_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_read_form(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_read_content', array('element'=>"div")); $_block_repeat=true; echo smarty_block_xmca_read_content(array('element'=>"div"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<?php if ($_smarty_tpl->tpl_vars['page']->value){?>
<div class="page page1col">
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_restricted_area', array('component'=>"EditPage",'args'=>array("id"=>$_smarty_tpl->tpl_vars['page']->value->id))); $_block_repeat=true; echo smarty_block_xmca_restricted_area(array('component'=>"EditPage",'args'=>array("id"=>$_smarty_tpl->tpl_vars['page']->value->id)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<div class="page_controls">
			<?php echo smarty_function_xmca_control(array('component'=>"EditPage",'width'=>800,'height'=>420,'title'=>"Modifica pagina",'args'=>array("id"=>$_smarty_tpl->tpl_vars['page']->value->id)),$_smarty_tpl);?>
 
			<?php echo smarty_function_xmca_control(array('component'=>"EditContent",'width'=>800,'height'=>550,'title'=>"Aggiungi contenuto",'args'=>array("page_id"=>$_smarty_tpl->tpl_vars['page']->value->id)),$_smarty_tpl);?>

			<?php echo smarty_function_xmca_control(array('component'=>"DeletePage",'confirm'=>true,'confirmTitle'=>"La pagina verr&agrave; eliminata definitivamente",'title'=>"Elimina pagina",'args'=>array("id"=>$_smarty_tpl->tpl_vars['page']->value->id)),$_smarty_tpl);?>

		</div>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_restricted_area(array('component'=>"EditPage",'args'=>array("id"=>$_smarty_tpl->tpl_vars['page']->value->id)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	<div class="page_body">
		<div><?php echo $_smarty_tpl->tpl_vars['page']->value->getRead("body");?>
</div>
	</div>

	<!-- CONTENUTI SEZIONE LIVELLO 1 -->
	<?php if (count($_smarty_tpl->tpl_vars['page']->value->contents)>0){?>
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
<?php }?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_read_content(array('element'=>"div"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>