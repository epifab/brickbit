<?php /* Smarty version Smarty-3.1.12, created on 2012-11-17 23:20:26
         compiled from "theme\standard\templates\EditContent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2022250a81bba10ee07-49457428%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dc5cd97dfdcd8d560c70fc54618359a68b438fec' => 
    array (
      0 => 'theme\\standard\\templates\\EditContent.tpl',
      1 => 1353171784,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2022250a81bba10ee07-49457428',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'recordset' => 0,
    'private' => 0,
    'tags' => 0,
    'tag' => 0,
    'errors' => 0,
    'lang' => 0,
    'text' => 0,
    'field' => 0,
    'contentStyles' => 0,
    'contentStyle' => 0,
    'groups' => 0,
    'group' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a81bbaae54b0_41354391',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a81bbaae54b0_41354391')) {function content_50a81bbaae54b0_41354391($_smarty_tpl) {?><?php if (!is_callable('smarty_block_xmca_edit_form')) include 'tpl_plugins\\block.xmca_edit_form.php';
if (!is_callable('smarty_block_xmca_javascript')) include 'tpl_plugins\\block.xmca_javascript.php';
if (!is_callable('smarty_modifier_xmca_tags')) include 'tpl_plugins\\modifier.xmca_tags.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_edit_form', array()); $_block_repeat=true; echo smarty_block_xmca_edit_form(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<?php if ($_smarty_tpl->tpl_vars['recordset']->value->id){?>
<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit('id');?>
"/>
<?php }elseif($_smarty_tpl->tpl_vars['recordset']->value->supercontent_id){?>
<input type="hidden" name="supercontent_id" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit("supercontent_id");?>
"/>
<?php }else{ ?>
<input type="hidden" name="page_id" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit("page_id");?>
"/>
<?php }?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_javascript', array()); $_block_repeat=true; echo smarty_block_xmca_javascript(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

// Convert divs to queue widgets when the DOM is ready
$(function() {
	$(".plupload").pluploadQueue({
		// General settings
		runtimes : 'flash,silverlight,html5',
		url : 'upload.php',
		max_file_size : '50mb',
		chunk_size : '1mb',
		unique_names : true,

		// Resize images on clientside if we can
		//resize : {width : 320, height : 240, quality : 90},

		// Specify what files to browse for
//		filters : [
//			{title : "Image files", extensions : "jpg,gif,png"},
//			{title : "Pdf files", extensions : "pdf"},
//			{title : "Audio files", extensions : "mp3"},
//			{title : "Video files", extensions : "avi,wmv"},
//			{title : "Zip files", extensions : "zip"}
//		],
		init : {
			FilesAdded: function(up, files) {
				plupload.each(files, function(file) {
					if (up.files.length > 1) {
						up.removeFile(file);
						alert("E' possibile allegare un solo file per ogni contenuto");
					}
				});
				if (up.files.length >= 1) {
				}
			},
			FilesRemoved: function(up, files) {
				if (up.files.length < 1) {
				}
			}
		},
		multi_selection: false,
		// Flash settings
		flash_swf_url : 'js/plupload/js/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url : 'js/plupload/js/plupload.silverlight.xap'
	});

	// Client side form validation
	$('#<?php echo $_smarty_tpl->tpl_vars['private']->value['formId'];?>
').submit(function(e) {
		var uploader = $('#download').plupload('getUploader');

		// Files in queue upload them first
		if (uploader.files.length > 0) {
			// When all files are uploaded submit form
			uploader.bind('StateChanged', function() {
				if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
					$('form')[0].submit();
				}
			});
			uploader.start();
		} else {
			alert('You must at least upload one file.');
		}
		return false;
	});
	
});
$(document).ready(function() {
	EditPageLang('<?php echo $_smarty_tpl->tpl_vars['private']->value['defaultLang'];?>
');
});
$(function() {
	var availableTags = [
<?php  $_smarty_tpl->tpl_vars["tag"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["tag"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tags']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars["tag"]->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars["tag"]->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars["tag"]->key => $_smarty_tpl->tpl_vars["tag"]->value){
$_smarty_tpl->tpl_vars["tag"]->_loop = true;
 $_smarty_tpl->tpl_vars["tag"]->iteration++;
 $_smarty_tpl->tpl_vars["tag"]->last = $_smarty_tpl->tpl_vars["tag"]->iteration === $_smarty_tpl->tpl_vars["tag"]->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["tags"]['last'] = $_smarty_tpl->tpl_vars["tag"]->last;
?>
		"<?php echo $_smarty_tpl->tpl_vars['tag']->value->value;?>
"<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['tags']['last']){?>,<?php }?>
<?php } ?>

	];
	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}
	$(".content_tags")
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			if (event.keyCode === $.ui.keyCode.TAB && $(this).data( "autocomplete" ).menu.active) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source: function( request, response ) {
				// delegate back to autocomplete, but extract the last term
				response( $.ui.autocomplete.filter(availableTags, extractLast( request.term ) ) );
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
			}
		});
}); 
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_javascript(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	
<table class="xmca_dataedit">
	<tr>
		<th class="label">URL</th>
		<td class="input">
				<input class="text l" onchange="ChangeUrl()" id="edit_content_input_url" type="text" name="recordset[url]" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->getEdit('url');?>
"/>
				<p class="info">
					Una volta impostata una URL questa non potr&agrave; pi&ugrave; essere modificata.<br/>
					&Egrave; importante (per il posizionamento sui motori di ricerca) scegliere una URL vicina il pi&ugrave; possibile al titolo e/o ad una brevissima descrizione del contenuto stesso.<br/><br/>
					Il contenuto sar&agrave; direttamente raggiungibile all'indirizzo <?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
Content/<span id="edit_content_label_url">[URL]</span>.html
				</p>
				<?php if (array_key_exists("url",$_smarty_tpl->tpl_vars['errors']->value)){?>
				<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['url'];?>
</p>
				<?php }?>
			
		</td>
	</tr>
	<tr>
		<th class="label">Tags</th>
		<td class="input">
			<input type="textbox" class="content_tags" name="recordset[tags]" size="50" value="<?php echo smarty_modifier_xmca_tags($_smarty_tpl->tpl_vars['recordset']->value->tags);?>
"/>
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
" class="lang_control"><img src="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
img/lang/40/<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
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
		<th class="label">Sottotitolo</th>
		<td class="input">
			<input class="text xl" type="text" name="recordset[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.subtitle]" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit('subtitle');?>
"/>
			<?php $_smarty_tpl->tpl_vars['field'] = new Smarty_variable("text_".((string)$_smarty_tpl->tpl_vars['lang']->value).".subtitle", null, 0);?>
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
		<th class="label">Anteprima</th>
		<td class="input">
			<textarea rows="4" cols="50" class="rich_text_light" name="recordset[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
.preview]"><?php echo $_smarty_tpl->tpl_vars['recordset']->value->{$_smarty_tpl->tpl_vars['text']->value}->getEdit("preview");?>
</textarea>
			<?php if (array_key_exists($_smarty_tpl->tpl_vars['text']->value,$_smarty_tpl->tpl_vars['errors']->value)&&array_key_exists("preview",$_smarty_tpl->tpl_vars['errors']->value[$_smarty_tpl->tpl_vars['text']->value])){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value[$_smarty_tpl->tpl_vars['text']->value]['preview'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr class="lang lang_<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
">
		<th class="label">Contenuto</th>
		<td class="input">
			<textarea rows="4" cols="50" class="rich_text" name="recordset[<?php echo $_smarty_tpl->tpl_vars['text']->value;?>
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
				<?php  $_smarty_tpl->tpl_vars['contentStyle'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contentStyle']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contentStyles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contentStyle']->key => $_smarty_tpl->tpl_vars['contentStyle']->value){
$_smarty_tpl->tpl_vars['contentStyle']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['contentStyle']->value->code;?>
"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->style_code==$_smarty_tpl->tpl_vars['contentStyle']->value->code){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['contentStyle']->value->description;?>
</option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr>
		<th class="label">Mostra / nascondi contenuto</th>
		<td class="input">
			<input type="checkbox" name="recordset[expandable]" id="expandable_input" value="1"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->expandable){?> checked="checked"<?php }?>/> <label for="expandable_input">Controllo anteprima</label>
			<?php if (array_key_exists("expandable",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['expandable'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Commenti</th>
		<td class="input">
			<input type="checkbox" name="recordset[comments]" id="comments_input" value="1"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->comments){?> checked="checked"<?php }?>/> <label for="comments_input">Permetti commenti da parte degli utenti</label>
			<?php if (array_key_exists("comments",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['comments'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Plugin sociali</th>
		<td class="input">
			<input type="checkbox" name="recordset[social_networks]" id="social_networks_input" value="1"<?php if ($_smarty_tpl->tpl_vars['recordset']->value->social_networks){?> checked="checked"<?php }?>/> <label for="social_networks_input">Plugin sociali</label>
			<?php if (array_key_exists("social_networks",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['social_networks'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Immagine</th>
		<td class="input">
			<input type="file" name="image"/>
			<?php if (($_smarty_tpl->tpl_vars['recordset']->value->image_id)){?>
			<div class="info">
				<img src="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->image4_url;?>
" style="float: left; margin: 5px 5px 0px 0px"/>
				Scegliendo una nuova immagine, l'immagine corrente verr&agrave; sostituita.<br/>
				<input type="checkbox" name="deleteimg" value="1"/> Elimina l'immagine corrente
				<div style="clear: both"></div>
			</div>
			<?php }?>
			<?php if (array_key_exists("image",$_smarty_tpl->tpl_vars['errors']->value)){?>
			<p class="alert"><?php echo $_smarty_tpl->tpl_vars['errors']->value['image'];?>
</p>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Download</th>
		<td class="input">
			<?php if (($_smarty_tpl->tpl_vars['recordset']->value->download_file_id)){?>
				<input type="text" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->download_file_url;?>
" class="text l readonly" readonly="readonly"/>
			<?php }else{ ?>
				<div class="plupload" id="download_<?php echo $_smarty_tpl->tpl_vars['private']->value['requestId'];?>
"></div>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Audio</th>
		<td class="input">
			<?php if (($_smarty_tpl->tpl_vars['recordset']->value->audio_file_id)){?>
				<input type="text" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->audio_file_url;?>
" class="text l readonly" readonly="readonly"/>
			<?php }else{ ?>
				<div class="plupload" id="audio_<?php echo $_smarty_tpl->tpl_vars['private']->value['requestId'];?>
"></div>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th class="label">Video</th>
		<td class="input">
			<?php if (($_smarty_tpl->tpl_vars['recordset']->value->video_file_id)){?>
				<input type="text" value="<?php echo $_smarty_tpl->tpl_vars['recordset']->value->video_file_url;?>
" class="text l readonly" readonly="readonly"/>
			<?php }else{ ?>
				<div class="plupload" id="video_<?php echo $_smarty_tpl->tpl_vars['private']->value['requestId'];?>
"></div>
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