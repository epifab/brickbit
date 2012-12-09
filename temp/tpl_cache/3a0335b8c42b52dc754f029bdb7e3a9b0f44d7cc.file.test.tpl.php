<?php /* Smarty version Smarty-3.1.12, created on 2012-12-09 21:50:38
         compiled from "theme\dark\templates\test.tpl" */ ?>
<?php /*%%SmartyHeaderCode:946650c507ae42a9a1-69456629%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3a0335b8c42b52dc754f029bdb7e3a9b0f44d7cc' => 
    array (
      0 => 'theme\\dark\\templates\\test.tpl',
      1 => 1355087986,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '946650c507ae42a9a1-69456629',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50c507ae48bb98_58484454',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c507ae48bb98_58484454')) {function content_50c507ae48bb98_58484454($_smarty_tpl) {?>theme -> dark

including test
<?php echo $_smarty_tpl->getSubTemplate ("test2.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


including test
<?php echo $_smarty_tpl->getSubTemplate ("test3.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


including test
<?php echo $_smarty_tpl->getSubTemplate ("test4.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>