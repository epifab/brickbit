<?php /* Smarty version Smarty-3.1.12, created on 2013-01-08 23:23:34
         compiled from "module\core\templates\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2317850df1e0b1d3f83-82538920%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2afb656c211dbadb47968e6d4e55de4a9b7f633b' => 
    array (
      0 => 'module\\core\\templates\\header.tpl',
      1 => 1357687413,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2317850df1e0b1d3f83-82538920',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50df1e0b34ab84_47350198',
  'variables' => 
  array (
    'website' => 0,
    'user' => 0,
    'system' => 0,
    'lang' => 0,
    'page' => 0,
    'menuItem' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50df1e0b34ab84_47350198')) {function content_50df1e0b34ab84_47350198($_smarty_tpl) {?><?php if (!is_callable('smarty_block_link')) include 'system/tpl-api\\block.link.php';
if (!is_callable('smarty_function_theme_path')) include 'system/tpl-api\\function.theme_path.php';
if (!is_callable('smarty_block_panel')) include 'system/tpl-api\\block.panel.php';
if (!is_callable('smarty_function_lang_link')) include 'system/tpl-api\\function.lang_link.php';
if (!is_callable('smarty_block_protected')) include 'system/tpl-api\\block.protected.php';
?>	<div id="admin-menu-wrapper">
		<ul id="admin-menu">
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"user/account")); $_block_repeat=true; echo smarty_block_link(array('url'=>"user/account"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
account<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"user/account"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"contents")); $_block_repeat=true; echo smarty_block_link(array('url'=>"contents"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contents<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"contents"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"users")); $_block_repeat=true; echo smarty_block_link(array('url'=>"users"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
users<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"users"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>"system")); $_block_repeat=true; echo smarty_block_link(array('url'=>"system"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
system<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>"system"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
		</ul>
	</div>

	<div id="header-wrapper">
		<div id="header">
			<h1 id="header-title">
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>'','ajax'=>false)); $_block_repeat=true; echo smarty_block_link(array('url'=>'','ajax'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

					<img src="<?php echo smarty_function_theme_path(array('url'=>"img/logo.png"),$_smarty_tpl);?>
" alt="<?php echo $_smarty_tpl->tpl_vars['website']->value['title'];?>
"/>
				<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>'','ajax'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</h1>
			

			<div id="header-sidebar">
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('panel', array('name'=>"header-sidebar")); $_block_repeat=true; echo smarty_block_panel(array('name'=>"header-sidebar"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

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
							<a href="http://<?php echo smarty_function_lang_link(array('id'=>$_smarty_tpl->tpl_vars['lang']->value),$_smarty_tpl);?>
"><img src="<?php echo smarty_function_theme_path(array('url'=>"img/lang/40/".((string)$_smarty_tpl->tpl_vars['lang']->value).".jpg"),$_smarty_tpl);?>
"/></a>
							<?php }?>
						<?php } ?>
					</div>
				<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_panel(array('name'=>"header-sidebar"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</div>
		</div>
	</div>

	<div id="main-menu-wrapper">
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

			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>'')); $_block_repeat=true; echo smarty_block_link(array('url'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
about<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>'')); $_block_repeat=true; echo smarty_block_link(array('url'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
live demo<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>'')); $_block_repeat=true; echo smarty_block_link(array('url'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
download<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>'')); $_block_repeat=true; echo smarty_block_link(array('url'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
docs<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('url'=>'')); $_block_repeat=true; echo smarty_block_link(array('url'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contacts<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('url'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
		</ul>
	</div>
<?php }} ?>