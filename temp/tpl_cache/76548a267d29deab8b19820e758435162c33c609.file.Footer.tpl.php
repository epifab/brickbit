<?php /* Smarty version Smarty-3.1.12, created on 2012-11-17 22:17:45
         compiled from "theme\standard\templates\layout\Footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1971550a80d09f3ae55-04316821%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76548a267d29deab8b19820e758435162c33c609' => 
    array (
      0 => 'theme\\standard\\templates\\layout\\Footer.tpl',
      1 => 1353011075,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1971550a80d09f3ae55-04316821',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'menuItems' => 0,
    'private' => 0,
    'menuItem' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a80d0a0f0525_95718028',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a80d0a0f0525_95718028')) {function content_50a80d0a0f0525_95718028($_smarty_tpl) {?><div id="footer_left">
	<h3>www.cambiamentodue.it &copy; 2011</h3>
	<h4>Developed by Fabio Epifani <a href="http://www.episoft.it">www.episoft.it</a></h4>
</div>
<div id="footer_right">
	<ul class="footer_pages">
	<?php  $_smarty_tpl->tpl_vars["menuItem"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["menuItem"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menuItems']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["menuItem"]->key => $_smarty_tpl->tpl_vars["menuItem"]->value){
$_smarty_tpl->tpl_vars["menuItem"]->_loop = true;
?>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
<?php echo $_smarty_tpl->tpl_vars['menuItem']->value->url;?>
.html"><span><?php echo $_smarty_tpl->tpl_vars['menuItem']->value->getRead("title");?>
</span></a></li>
	<?php } ?>
	</ul>
</div>
<div class="clear"></div><?php }} ?>