<?php /* Smarty version Smarty-3.1.12, created on 2012-12-20 00:52:30
         compiled from "module\core\templates\content-page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1482350d25c746e3698-90556453%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfcfe55358aa71a1f9fba7e3f255df95dd226e0a' => 
    array (
      0 => 'module\\core\\templates\\content-page.tpl',
      1 => 1355964749,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1482350d25c746e3698-90556453',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50d25c74959137_50300959',
  'variables' => 
  array (
    'page' => 0,
    'js' => 0,
    'css' => 0,
    'website' => 0,
    'private' => 0,
    'user' => 0,
    'system' => 0,
    'lang' => 0,
    'menuItem' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50d25c74959137_50300959')) {function content_50d25c74959137_50300959($_smarty_tpl) {?><?php if (!is_callable('smarty_function_theme_path')) include 'system/tpl-api\\function.theme_path.php';
if (!is_callable('smarty_block_link')) include 'system/tpl-api\\block.link.php';
if (!is_callable('smarty_block_protected')) include 'system/tpl-api\\block.protected.php';
if (!is_callable('smarty_function_javascript')) include 'system/tpl-api\\function.javascript.php';
?><!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><?php echo $_smarty_tpl->tpl_vars['page']->value['title'];?>
</title>
		
		<?php  $_smarty_tpl->tpl_vars["js"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["js"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['page']->value['js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["js"]->key => $_smarty_tpl->tpl_vars["js"]->value){
$_smarty_tpl->tpl_vars["js"]->_loop = true;
?>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['js']->value;?>
"></script>
		<?php } ?>
		
		<?php  $_smarty_tpl->tpl_vars["css"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["css"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['page']->value['css']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["css"]->key => $_smarty_tpl->tpl_vars["css"]->value){
$_smarty_tpl->tpl_vars["css"]->_loop = true;
?>
		<link href="<?php echo $_smarty_tpl->tpl_vars['css']->value;?>
" type="text/css" rel="stylesheet"/>
		<?php } ?>
	</head>
	
	<body>
		<div id="main">

			<div id="header">
				<h1 id="header-title">
					<a href="<?php echo $_smarty_tpl->tpl_vars['website']->value['base'];?>
">
						<img src="<?php echo smarty_function_theme_path(array('url'=>"img/website-logo.jpg"),$_smarty_tpl);?>
" alt="<?php echo $_smarty_tpl->tpl_vars['website']->value['title'];?>
"/>
					</a>
				</h1>
				
				<h2 id="header-subtitle"><span><?php echo $_smarty_tpl->tpl_vars['website']->value['subtitle'];?>
</span></h2>
				
				
				<div id="header-sidebar">
				<!--	<div class="search">
						<form action="Home.html" method="post"><input type="text" class="text" name="search" /><input type="image" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/search.png"/></form>
					</div>-->
					<div id="header-sidebar-login">
						<?php if (!$_smarty_tpl->tpl_vars['user']->value){?>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"user/login",'okButtonLabel'=>"Login",'width'=>300,'showResponse'=>false)); $_block_repeat=true; echo smarty_block_link(array('url'=>"user/login",'okButtonLabel'=>"Login",'width'=>300,'showResponse'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<img src="<?php echo smarty_function_theme_path(array('url'=>"img/login.jpg"),$_smarty_tpl);?>
" alt="Login"/><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"user/login",'okButtonLabel'=>"Login",'width'=>300,'showResponse'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
						<?php }else{ ?>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"user/logout",'okButtonLabel'=>"Logout",'width'=>300,'showResponse'=>false)); $_block_repeat=true; echo smarty_block_link(array('url'=>"user/logout",'okButtonLabel'=>"Logout",'width'=>300,'showResponse'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<img src="<?php echo smarty_function_theme_path(array('url'=>"img/logout.jpg"),$_smarty_tpl);?>
" alt="Logout"/><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"user/logout",'okButtonLabel'=>"Logout",'width'=>300,'showResponse'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
						<?php }?>
					</div>
					<div id="header-sidebar-langs">
						<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['system']->value['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
							<?php if ($_smarty_tpl->tpl_vars['lang']->value!=$_smarty_tpl->tpl_vars['system']->value['lang']){?>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"lang/".((string)$_smarty_tpl->tpl_vars['lang']->value))); $_block_repeat=true; echo smarty_block_link(array('url'=>"lang/".((string)$_smarty_tpl->tpl_vars['lang']->value)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<img src="<?php echo smarty_function_theme_path(array('url'=>"img/lang/40/".((string)$_smarty_tpl->tpl_vars['lang']->value).".jpg"),$_smarty_tpl);?>
"/><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"lang/".((string)$_smarty_tpl->tpl_vars['lang']->value)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

							<?php }?>
						<?php } ?>
					</div>
				</div>
				<ul id="main-menu">
					<?php  $_smarty_tpl->tpl_vars["menuItem"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["menuItem"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['page']->value['mainMenu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["menuItem"]->key => $_smarty_tpl->tpl_vars["menuItem"]->value){
$_smarty_tpl->tpl_vars["menuItem"]->_loop = true;
?>
					<li <?php if ($_smarty_tpl->tpl_vars['page']->value['url']==$_smarty_tpl->tpl_vars['menuItem']->value['url']){?>class="selected" <?php }?>id="item-<?php echo $_smarty_tpl->tpl_vars['menuItem']->value['id'];?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>$_smarty_tpl->tpl_vars['menuItem']->value['url'])); $_block_repeat=true; echo smarty_block_link(array('url'=>$_smarty_tpl->tpl_vars['menuItem']->value['url']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<span><?php echo $_smarty_tpl->tpl_vars['menuItem']->value['title'];?>
</span><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>$_smarty_tpl->tpl_vars['menuItem']->value['url']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
					<?php } ?>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('protected', array('url'=>"page/add")); $_block_repeat=true; echo smarty_block_protected(array('url'=>"page/add"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

					<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"page/add",'width'=>800,'height'=>500,'title'=>"Create new page")); $_block_repeat=true; echo smarty_block_link(array('url'=>"page/add",'width'=>800,'height'=>500,'title'=>"Create new page"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
+<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"page/add",'width'=>800,'height'=>500,'title'=>"Create new page"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
					<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_protected(array('url'=>"page/add"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				</ul>
				
			</div>
			
			<div id="container">
				<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['private']->value['mainTemplate'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			</div>

			<div id="footer">
				
			</div>
		</div>

		<script type="text/javascript"><?php echo smarty_function_javascript(array(),$_smarty_tpl);?>
</script>
	</body>
</html><?php }} ?>