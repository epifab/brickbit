<?php /* Smarty version Smarty-3.1.12, created on 2012-11-18 11:43:57
         compiled from "theme\standard\templates\layout\Outline.tpl" */ ?>
<?php /*%%SmartyHeaderCode:35950a7f2998dfb61-48513360%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfe881b162bb3698b1eb0c9588a6bbc738d979bd' => 
    array (
      0 => 'theme\\standard\\templates\\layout\\Outline.tpl',
      1 => 1353239030,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '35950a7f2998dfb61-48513360',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a7f299a5a178_43327495',
  'variables' => 
  array (
    'private' => 0,
    'url' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a7f299a5a178_43327495')) {function content_50a7f299a5a178_43327495($_smarty_tpl) {?><?php if (!is_callable('smarty_function_xmca_nested_component')) include 'tpl_plugins\\function.xmca_nested_component.php';
if (!is_callable('smarty_function_xmca_get_javascript')) include 'tpl_plugins\\function.xmca_get_javascript.php';
?><!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><?php echo (($tmp = @$_smarty_tpl->tpl_vars['private']->value['pageTitle'])===null||$tmp==='' ? ((string)$_smarty_tpl->tpl_vars['private']->value['siteName'])." | ".((string)$_smarty_tpl->tpl_vars['private']->value['componentName']) : $tmp);?>
</title>
		
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/jquery-1.8.2.js"></script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/jquery_ui/jquery-ui-1.9.0.custom.js"></script>
<!--		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/jquery.autocomplete.js"></script>-->
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/nivoslider/jquery.nivoslider.js"></script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/jquery.form.js"></script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/jquery.xmca.js"></script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/plupload/js/plupload.full.js"></script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
		
		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
css/xmca.css" type="text/css" rel="stylesheet"/>
		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/jquery_ui/css/jquery_ui.css" type="text/css" rel="stylesheet"/>
<!--		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
css/jquery.autocomplete.css" type="text/css" rel="stylesheet"/>-->
		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/nivoslider/css/jquery.nivoslider.css" type="text/css" rel="stylesheet"/>
		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/nivoslider/css/default.css" type="text/css" rel="stylesheet"/>
		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/nivoslider/css/pascal.css" type="text/css" rel="stylesheet"/>
		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
js/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css"/>
		<link href="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
css/layout.css" type="text/css" rel="stylesheet"/>
	</head>
	
	<body>

</head>
<body>
		
		<div id="main">
		
			<div id="header">
				<?php echo smarty_function_xmca_nested_component(array('component'=>"Header",'prefix'=>"header",'args'=>array("url"=>$_smarty_tpl->tpl_vars['url']->value)),$_smarty_tpl);?>

			</div>
			
			<div id="container">
				<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['private']->value['mainTemplate'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			</div>

			<div id="footer">
				<?php echo smarty_function_xmca_nested_component(array('component'=>"Footer",'prefix'=>"footer",'args'=>array()),$_smarty_tpl);?>

			</div>
		</div>

		<script type="text/javascript"><?php echo smarty_function_xmca_get_javascript(array(),$_smarty_tpl);?>
</script>
	</body>
</html><?php }} ?>