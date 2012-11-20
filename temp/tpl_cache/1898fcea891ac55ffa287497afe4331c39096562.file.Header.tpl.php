<?php /* Smarty version Smarty-3.1.12, created on 2012-11-17 22:17:40
         compiled from "theme\standard\templates\layout\Header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1102850a80d045de0e0-35712061%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1898fcea891ac55ffa287497afe4331c39096562' => 
    array (
      0 => 'theme\\standard\\templates\\layout\\Header.tpl',
      1 => 1353171851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1102850a80d045de0e0-35712061',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'private' => 0,
    'lang' => 0,
    'menuItems' => 0,
    'url' => 0,
    'menuItem' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a80d04931be9_38583339',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a80d04931be9_38583339')) {function content_50a80d04931be9_38583339($_smarty_tpl) {?><?php if (!is_callable('smarty_block_xmca_read_form')) include 'tpl_plugins\\block.xmca_read_form.php';
if (!is_callable('smarty_block_xmca_read_content')) include 'tpl_plugins\\block.xmca_read_content.php';
if (!is_callable('smarty_function_xmca_control_action')) include 'tpl_plugins\\function.xmca_control_action.php';
if (!is_callable('smarty_block_xmca_restricted_area')) include 'tpl_plugins\\block.xmca_restricted_area.php';
if (!is_callable('smarty_function_xmca_control')) include 'tpl_plugins\\function.xmca_control.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_read_form', array()); $_block_repeat=true; echo smarty_block_xmca_read_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_read_form(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_read_content', array()); $_block_repeat=true; echo smarty_block_xmca_read_content(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<h1 id="header_title"><a href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
Home.html"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/header_title.jpg" alt="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteName'];?>
"/></a></h1>
<h2 id="header_subtitle"><span><?php echo $_smarty_tpl->tpl_vars['private']->value['siteDesc'];?>
</span></h2>
<div id="header_sidebar">
<!--	<div class="search">
		<form action="Home.html" method="post"><input type="text" class="text" name="search" /><input type="image" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/search.png"/></form>
	</div>-->
	<div id="header_sidebar_login">
		<?php if ($_smarty_tpl->tpl_vars['private']->value['login']->isAnonymous()){?>
			<a href="javascript:<?php echo smarty_function_xmca_control_action(array('component'=>"Login",'okButtonLabel'=>"Login",'width'=>300,'showResponse'=>false),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/lock.jpg" alt="Login"/></a></li>
		<?php }else{ ?>
			<a href="javascript:<?php echo smarty_function_xmca_control_action(array('component'=>"Login",'args'=>array('logout'=>1),'showResponse'=>false),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/unlock.jpg" alt="Logout"/></a></li>
		<?php }?>
	</div>
	<div id="header_sidebar_langs">
		<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['private']->value['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
			<?php if ($_smarty_tpl->tpl_vars['lang']->value!=$_smarty_tpl->tpl_vars['private']->value['language']){?>
			<a class="lang" href="<?php echo $_smarty_tpl->tpl_vars['private']->value['self'];?>
?lang=<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/lang/40/<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
.jpg"/></a>
			<?php }?>
		<?php } ?>
	</div>
	<!--	<div class="header_social_networks">
		<a target="blank" href="http://www.facebook.com/Dr.Gerboni"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/social_networks/facebook.png" width="30" height="30" alt="Facebook"/></a>
		<a target="blank" href="https://twitter.com/EGerboni"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/social_networks/twitter.png" width="30" height="30" alt="Twitter"/></a>
		<a target="blank" href="http://www.linkedin.com/profile/view?id=104603791&trk=tab_pro"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/social_networks/linkedin.png" width="30" height="30" alt="Linkedin"/></a>
	</div>-->
</div>
<ul id="header_menu">
<?php  $_smarty_tpl->tpl_vars["menuItem"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["menuItem"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menuItems']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["menuItem"]->key => $_smarty_tpl->tpl_vars["menuItem"]->value){
$_smarty_tpl->tpl_vars["menuItem"]->_loop = true;
?>
	<li <?php if ($_smarty_tpl->tpl_vars['url']->value==$_smarty_tpl->tpl_vars['menuItem']->value->url){?>class="selected" <?php }?>id="item_<?php echo $_smarty_tpl->tpl_vars['menuItem']->value->url;?>
"><a href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
<?php echo $_smarty_tpl->tpl_vars['menuItem']->value->url;?>
.html"><span><?php echo $_smarty_tpl->tpl_vars['menuItem']->value->getRead("title");?>
</span></a></li>
<?php } ?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_restricted_area', array('component'=>"EditPage")); $_block_repeat=true; echo smarty_block_xmca_restricted_area(array('component'=>"EditPage"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<li><?php echo smarty_function_xmca_control(array('component'=>"EditPage",'width'=>800,'height'=>500,'title'=>"+",'style'=>"link"),$_smarty_tpl);?>
</li>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_restricted_area(array('component'=>"EditPage"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</ul>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_read_content(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>