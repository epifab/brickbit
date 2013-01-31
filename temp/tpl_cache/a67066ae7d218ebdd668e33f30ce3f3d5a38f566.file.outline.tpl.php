<?php /* Smarty version Smarty-3.1.12, created on 2013-01-26 01:21:36
         compiled from "module\core\templates\outline.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2821951032fa0094985-47928867%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a67066ae7d218ebdd668e33f30ce3f3d5a38f566' => 
    array (
      0 => 'module\\core\\templates\\outline.tpl',
      1 => 1357601785,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2821951032fa0094985-47928867',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'js' => 0,
    'css' => 0,
    'system' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51032fa0208957_32291815',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51032fa0208957_32291815')) {function content_51032fa0208957_32291815($_smarty_tpl) {?><?php if (!is_callable('smarty_function_path')) include 'system/tpl-api\\function.path.php';
if (!is_callable('smarty_function_theme_path')) include 'system/tpl-api\\function.theme_path.php';
if (!is_callable('smarty_function_region')) include 'system/tpl-api\\function.region.php';
if (!is_callable('smarty_function_javascript')) include 'system/tpl-api\\function.javascript.php';
?><!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><?php echo $_smarty_tpl->tpl_vars['page']->value['title'];?>
</title>
		
		<script type="text/javascript" src="<?php echo smarty_function_path(array('url'=>"js/jquery-1.8.2.js"),$_smarty_tpl);?>
"></script>
		<script type="text/javascript" src="<?php echo smarty_function_path(array('url'=>"js/jquery_ui/jquery-ui-1.9.0.custom.js"),$_smarty_tpl);?>
"></script>
		<script type="text/javascript" src="<?php echo smarty_function_path(array('url'=>"js/jquery.form.js"),$_smarty_tpl);?>
"></script>
		<script type="text/javascript" src="<?php echo smarty_function_path(array('url'=>"js/jquery.xmca.js"),$_smarty_tpl);?>
"></script>
		<?php  $_smarty_tpl->tpl_vars["js"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["js"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['page']->value['js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["js"]->key => $_smarty_tpl->tpl_vars["js"]->value){
$_smarty_tpl->tpl_vars["js"]->_loop = true;
?>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['js']->value;?>
"></script>
		<?php } ?>
		
		<link href="<?php echo smarty_function_path(array('url'=>"js/jquery_ui/css/jquery_ui.css"),$_smarty_tpl);?>
" type="text/css" rel="stylesheet"/>
		<link href="<?php echo smarty_function_theme_path(array('url'=>"css/layout.css"),$_smarty_tpl);?>
" type="text/css" rel="stylesheet"/>
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
		<div id="container">
			<?php echo smarty_function_region(array('name'=>"header"),$_smarty_tpl);?>


			<div id="main-wrapper">
				<div id="main">
					<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['system']->value['templates']['main'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


					<?php echo smarty_function_region(array('name'=>"sidebar"),$_smarty_tpl);?>

				</div>
			</div>

			<div id="footer-wrapper">
				<?php echo smarty_function_region(array('name'=>"footer"),$_smarty_tpl);?>

			</div>

		</div>
		<script type="text/javascript"><?php echo smarty_function_javascript(array(),$_smarty_tpl);?>
</script>
	</body>
</html><?php }} ?>