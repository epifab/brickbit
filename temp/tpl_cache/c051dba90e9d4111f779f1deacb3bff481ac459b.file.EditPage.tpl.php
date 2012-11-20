<?php /* Smarty version Smarty-3.1.12, created on 2012-11-17 23:32:03
         compiled from "theme\standard\templates\EditPage.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2795950a81e737b3e64-73804865%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c051dba90e9d4111f779f1deacb3bff481ac459b' => 
    array (
      0 => 'theme\\standard\\templates\\EditPage.tpl',
      1 => 1353171851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2795950a81e737b3e64-73804865',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'recordset' => 0,
    'private' => 0,
    'errors' => 0,
    'lang' => 0,
    'text' => 0,
    'field' => 0,
    'pageStyles' => 0,
    'pageStyle' => 0,
    'groups' => 0,
    'group' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a81e73c0eed2_14892215',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a81e73c0eed2_14892215')) {function content_50a81e73c0eed2_14892215($_smarty_tpl) {?><?php if (!is_callable('smarty_block_xmca_edit_form')) include 'tpl_plugins\\block.xmca_edit_form.php';
if (!is_callable('smarty_block_xmca_javascript')) include 'tpl_plugins\\block.xmca_javascript.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_edit_form', array()); $_block_repeat=true; echo smarty_block_xmca_edit_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<?php if ($_smarty_tpl->tpl_vars['recordset']->value->id){?>
<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit('id');?>
"/>
<?php }?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_javascript', array()); $_block_repeat=true; echo smarty_block_xmca_javascript(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

$(document).ready(function() {
	EditPageLang('<?php echo $_smarty_tpl->tpl_vars['private']->value['defaultLang'];?>
');
});
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_javascript(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<table class="xmca_dataedit">
	<tr>
		<th class="label">URL</th>
		<td class="input">
			<input class="text xl" onchange="ChangeUrl()" id="edit_content_input_url" type="text" name="recordset[url]" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit('url');?>
"/>
			<p class="info">
				Una volta impostata una URL questa non potr&agrave; pi&ugrave; essere modificata.<br/>
				&Egrave; importante (per il posizionamento sui motori di ricerca) scegliere una URL vicina il pi&ugrave; possibile al titolo e/o ad una brevissima descrizione della pagina.<br/><br/>
				La pagina sar&agrave; direttamente raggiungibile all'indirizzo <?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
<span id="edit_content_label_url">[URL]</span>.html
			</p>
			<?php if (array_key_exists("url",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['url'];?>
</p>
			<?php }?>
			
		</td>
	</tr>
	<tr>
		<th class="label">Lingua</th>
		<td class="input">
			<div class="xmca_languages_input">
				<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['private']->value['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
				<a href="javascript:EditPageLang('<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
')" id="lang_control_<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="lang_control"><img src="img/lang/40/<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
.jpg"/></a>
				<?php } ?>
			</div>
		</td>
	</tr>
	<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['private']->value['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
	<?php $_smarty_tpl->tpl_vars['text'] = new Smarty_variable("text_".((string)$_smarty_tpl->tpl_vars['lang']->value), null, 0);?>
	<tr class="lang lang_<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
">
		<th class="label">Titolo</th>
		<td class="input">
			<input class="text xl" type="text" name="recordset[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.title]" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('title');?>
"/>
			<?php $_smarty_tpl->tpl_vars['field'] = new Smarty_variable("text_".((string)$_smarty_tpl->tpl_vars['lang']->value).".title", null, 0);?>
			<?php if (array_key_exists($_smarty_tpl->tpl_vars['field']->value,$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value[$_smarty_tpl->tpl_vars['field']->value];?>
</p>
			<?php }?>
			<input type="hidden" name="recordset[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.lang_id]" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
"/>
		</td>
	</tr>
	<tr class="lang lang_<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
">
		<th class="label">Contenuto</th>
		<td class="input">
			<textarea rows="4" cols="50" class="rich_text_light" name="recordset[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.body]"><?php echo $_smarty_tpl->tpl_vars['recordset']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit("body");?>
</textarea>
			<?php if (array_key_exists($_smarty_tpl->tpl_vars['text']->value,$_smarty_tpl->tpl_vars['errors']->value)&&array_key_exists("body",$_smarty_tpl->tpl_vars['errors']->value[$_smarty_tpl->tpl_vars['text']->value])){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value[$_smarty_tpl->tpl_vars['text']->value]['body'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<th class="label">Stile</th>
		<td class="input">
			<select name="recordset[style_code]" class="xl">
				<?php  $_smarty_tpl->tpl_vars['pageStyle'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pageStyle']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pageStyles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pageStyle']->key => $_smarty_tpl->tpl_vars['pageStyle']->value){
$_smarty_tpl->tpl_vars['pageStyle']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['pageStyle']->value->code;?>
"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->style_code==$_smarty_tpl->tpl_vars['pageStyle']->value->code){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['pageStyle']->value->description;?>
</option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr>
		<th class="label">Ricerca contenuti</th>
		<td class="input">
			<input type="checkbox" name="recordset[content_filters]" id="content_filters_input" value="1"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_filters){?> checked="checked"<?php }?>/> <label for="content_filters_input">Ricerca nella pagina</label>
			<?php if (array_key_exists("content_filters",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_filters'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Pagina contenuti</th>
		<td class="input">
			<select class="xl" name="recordset[content_paging]">
				<option value="0">Non paginare i contenuti</option>
				<option value="5"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_paging==5){?> selected="selected"<?php }?>>5 Contenuti per pagina</option>
				<option value="10"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_paging==10){?> selected="selected"<?php }?>>10 Contenuti per pagina</option>
				<option value="15"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_paging==15){?> selected="selected"<?php }?>>15 Contenuti per pagina</option>
				<option value="20"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_paging==20){?> selected="selected"<?php }?>>20 Contenuti per pagina</option>
				<option value="30"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_paging==30){?> selected="selected"<?php }?>>30 Contenuti per pagina</option>
				<option value="50"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_paging==50){?> selected="selected"<?php }?>>50 Contenuti per pagina</option>
			</select>
			<?php if (array_key_exists("content_paging",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_paging'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Ordinamento contenuti</th>
		<td class="input">
			<select class="xl" name="recordset[content_sorting]">
				<option value="sort_index_asc"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_sorting=="sort_index_asc"){?> selected="selected"<?php }?>>Ordinamento manuale</option>
				<option value="date_desc"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_sorting=="date_desc"){?> selected="selected"<?php }?>>Dal pi&ugrave; recente al pi&ugrave; vecchio</option>
				<option value="date_asc"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->content_sorting=="date_asc"){?> selected="selected"<?php }?>>Dal pi&ugrave; vecchio al pi&ugrave; recente</option>
			</select>
			<?php if (array_key_exists("content_sorting",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_sorting'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th colspan="2" class="row">Gestione permessi</th>
	</tr>
	<tr>
		<th class="label">Gruppo utenza</th>
		<td class="input">
			<select class="xl" name="recordset[record_mode.group_id]">
				<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value){
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['group']->value->id;?>
"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->group_id==$_smarty_tpl->tpl_vars['group']->value->id){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['group']->value->name;?>
</option>
				<?php } ?>
			</select>
			<?php if (array_key_exists("content_sorting",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_sorting'];?>
</p>
			<?php }?>
		</td>
	</tr>
	
	<tr>
		<th class="label">Permessi di lettura</th>
		<td class="input">
			<select class="xl" name="recordset[record_mode.read_mode]">
				<option value="2"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->read_mode==2){?> selected="selected"<?php }?>>Amministratori, owner</option>
				<option value="3"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->read_mode==3){?> selected="selected"<?php }?>>Amministratori, owner, gruppo utenza</option>
				<option value="4"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->read_mode==4){?> selected="selected"<?php }?>>Pagina pubblica</option>
			</select>
			<?php if (array_key_exists("content_sorting",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_sorting'];?>
</p>
			<?php }?>
		</td>
	</tr>
	
	<tr>
		<th class="label">Permessi di modifica e creazione contenuti</th>
		<td class="input">
			<select class="xl" name="recordset[record_mode.edit_mode]">
				<option value="2"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->edit_mode==2){?> selected="selected"<?php }?>>Amministratori, owner</option>
				<option value="3"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->edit_mode==3){?> selected="selected"<?php }?>>Amministratori, owner, gruppo utenza</option>
				<option value="4"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->record_mode->edit_mode==4){?> selected="selected"<?php }?>>Pagina pubblica</option>
			</select>
			<?php if (array_key_exists("content_sorting",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['content_sorting'];?>
</p>
			<?php }?>
		</td>
	</tr>
	
	<?php if (!$_smarty_tpl->tpl_vars['private']->value['isAjaxRequest']){?>
	<tr>
		<th class="controls" colspan="2"><input type="submit" class="xmca_control xxl" value="Invia"/></th>
	</tr>
	<?php }?>
</table>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_edit_form(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>