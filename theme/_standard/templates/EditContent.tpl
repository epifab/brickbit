<{ciderbit_edit_form}>
<{if $recordset->id}>
<input type="hidden" name="id" value="<{$recordset->getEdit('id')}>"/>
<{elseif $recordset->supercontent_id}>
<input type="hidden" name="supercontent_id" value="<{$recordset->getEdit("supercontent_id")}>"/>
<{else}>
<input type="hidden" name="page_id" value="<{$recordset->getEdit("page_id")}>"/>
<{/if}>

<{ciderbit_javascript}>
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
	$('#<{$private.formId}>').submit(function(e) {
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
	EditPageLang('<{$private.defaultLang}>');
});
$(function() {
	var availableTags = [
<{foreach name="tags" from=$tags item="tag"}>
		"<{$tag->value}>"<{if !$smarty.foreach.tags.last}>,<{/if}>
<{/foreach}>

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
<{/ciderbit_javascript}>
	
<table class="ciderbit_dataedit">
	<tr>
		<th class="label">URL</th>
		<td class="input">
				<input class="text l" onchange="ChangeUrl()" id="edit_content_input_url" type="text" name="recordset[url]" value="<{$recordset->getEdit('url')}>"/>
				<p class="info">
					Una volta impostata una URL questa non potr&agrave; pi&ugrave; essere modificata.<br/>
					&Egrave; importante (per il posizionamento sui motori di ricerca) scegliere una URL vicina il pi&ugrave; possibile al titolo e/o ad una brevissima descrizione del contenuto stesso.<br/><br/>
					Il contenuto sar&agrave; direttamente raggiungibile all'indirizzo <{$private.siteAddr}>Content/<span id="edit_content_label_url">[URL]</span>.html
				</p>
				<{if array_key_exists("url", $errors)}>
				<p class="alert"><{$errors.url}></p>
				<{/if}>
			<{*if $recordset->id}>
				<input class="text l" type="text" readonly="readonly" name="recordset[url]" value="<{$recordset->getEdit('url')}>"/>
				<p class="info">
					Il contenuto &egrave; direttamente raggiungibile all'indirizzo:<br/>
					<b><{$private.siteAddr}>Content/<{$recordset->url}>.html</b>
				</p>
			<{else}>
				<input class="text l" onchange="ChangeUrl()" id="edit_content_input_url" type="text" name="recordset[url]" value="<{$recordset->getEdit('url')}>"/>
				<p class="info">
					Una volta impostata una URL questa non potr&agrave; pi&ugrave; essere modificata.<br/>
					&Egrave; importante (per il posizionamento sui motori di ricerca) scegliere una URL vicina il pi&ugrave; possibile al titolo e/o ad una brevissima descrizione del contenuto stesso.<br/><br/>
					Il contenuto sar&agrave; direttamente raggiungibile all'indirizzo <{$private.siteAddr}>Content/<span id="edit_content_label_url">[URL]</span>.html
				</p>
				<{if array_key_exists("url", $errors)}>
				<p class="alert"><{$errors.url}></p>
				<{/if}>
			<{/if*}>

			
		</td>
	</tr>
	<tr>
		<th class="label">Tags</th>
		<td class="input">
			<input type="textbox" class="content_tags" name="recordset[tags]" size="50" value="<{$recordset->tags|ciderbit_tags}>"/>
		</td>
	</tr>
	<tr>
		<th class="label">Lingua</th>
		<td class="input">
			<div class="ciderbit_languages_input">
				<{foreach $private.languages as $lang}>
				<a href="javascript:EditPageLang('<{$lang}>')" id="lang_control_<{$lang}>" class="lang_control"><img src="<{theme}>img/lang/40/<{$lang}>.jpg"/></a>
				<{/foreach}>
			</div>
		</td>
	</tr>
	<{foreach $private.languages as $lang}>
	<{assign var='text' value="text_`$lang`"}>
	<tr class="lang lang_<{$lang}>">
		<th class="label">Titolo</th>
		<td class="input">
			<input class="text xl" type="text" name="recordset[<{$text}>.title]" value="<{$recordset->$text->getEdit('title')}>"/>
			<{assign var='field' value="text_`$lang`.title"}>
			<{if array_key_exists($field, $errors)}>
			<p class="alert"><{$errors.$field}></p>
			<{/if}>
			<input type="hidden" name="recordset[<{$text}>.lang_id]" value="<{$lang}>"/>
		</td>
	</tr>
	<tr class="lang lang_<{$lang}>">
		<th class="label">Sottotitolo</th>
		<td class="input">
			<input class="text xl" type="text" name="recordset[<{$text}>.subtitle]" value="<{$recordset->$text->getEdit('subtitle')}>"/>
			<{assign var='field' value="text_`$lang`.subtitle"}>
			<{if array_key_exists($field, $errors)}>
			<p class="alert"><{$errors.$field}></p>
			<{/if}>
			<input type="hidden" name="recordset[<{$text}>.lang_id]" value="<{$lang}>"/>
		</td>
	</tr>
	<tr class="lang lang_<{$lang}>">
		<th class="label">Anteprima</th>
		<td class="input">
			<textarea rows="4" cols="50" class="rich_text_light" name="recordset[<{$text}>.preview]"><{$recordset->$text->getEdit("preview")}></textarea>
			<{if array_key_exists($text, $errors) && array_key_exists("preview", $errors.$text)}>
			<p class="alert"><{$errors.$text.preview}></p>
			<{/if}>
		</td>
	</tr>
	<tr class="lang lang_<{$lang}>">
		<th class="label">Contenuto</th>
		<td class="input">
			<textarea rows="4" cols="50" class="rich_text" name="recordset[<{$text}>.body]"><{$recordset->$text->getEdit("body")}></textarea>
			<{if array_key_exists($text, $errors) && array_key_exists("body", $errors.$text)}>
			<p class="alert"><{$errors.$text.body}></p>
			<{/if}>
		</td>
	</tr>
	<{/foreach}>
	
	<tr>
		<th class="label">Stile</th>
		<td class="input">
			<select name="recordset[style_code]" class="xl">
				<{foreach $contentStyles as $contentStyle}>
					<option value="<{$contentStyle->code}>"<{if $recordset->style_code == $contentStyle->code}> selected="selected"<{/if}>><{$contentStyle->description}></option>
				<{/foreach}>
			</select>
		</td>
	</tr>
	<tr>
		<th class="label">Mostra / nascondi contenuto</th>
		<td class="input">
			<input type="checkbox" name="recordset[expandable]" id="expandable_input" value="1"<{if $recordset->expandable}> checked="checked"<{/if}>/> <label for="expandable_input">Controllo anteprima</label>
			<{if array_key_exists("expandable", $errors)}>
			<p class="alert"><{$errors.expandable}></p>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Commenti</th>
		<td class="input">
			<input type="checkbox" name="recordset[comments]" id="comments_input" value="1"<{if $recordset->comments}> checked="checked"<{/if}>/> <label for="comments_input">Permetti commenti da parte degli utenti</label>
			<{if array_key_exists("comments", $errors)}>
			<p class="alert"><{$errors.comments}></p>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Plugin sociali</th>
		<td class="input">
			<input type="checkbox" name="recordset[social_networks]" id="social_networks_input" value="1"<{if $recordset->social_networks}> checked="checked"<{/if}>/> <label for="social_networks_input">Plugin sociali</label>
			<{if array_key_exists("social_networks", $errors)}>
			<p class="alert"><{$errors.social_networks}></p>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Immagine</th>
		<td class="input">
			<input type="file" name="image"/>
			<{if ($recordset->image_id)}>
			<div class="info">
				<img src="<{$recordset->image4_url}>" style="float: left; margin: 5px 5px 0px 0px"/>
				Scegliendo una nuova immagine, l'immagine corrente verr&agrave; sostituita.<br/>
				<input type="checkbox" name="deleteimg" value="1"/> Elimina l'immagine corrente
				<div style="clear: both"></div>
			</div>
			<{/if}>
			<{if array_key_exists("image", $errors)}>
			<p class="alert"><{$errors.image}></p>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Download</th>
		<td class="input">
			<{if ($recordset->download_file_id)}>
				<input type="text" value="<{$recordset->download_file_url}>" class="text l readonly" readonly="readonly"/>
			<{else}>
				<div class="plupload" id="download_<{$private.requestId}>"></div>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Audio</th>
		<td class="input">
			<{if ($recordset->audio_file_id)}>
				<input type="text" value="<{$recordset->audio_file_url}>" class="text l readonly" readonly="readonly"/>
			<{else}>
				<div class="plupload" id="audio_<{$private.requestId}>"></div>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Video</th>
		<td class="input">
			<{if ($recordset->video_file_id)}>
				<input type="text" value="<{$recordset->video_file_url}>" class="text l readonly" readonly="readonly"/>
			<{else}>
				<div class="plupload" id="video_<{$private.requestId}>"></div>
			<{/if}>
		</td>
	</tr>
	
	<tr>
		<th colspan="2" class="row">Gestione permessi</th>
	</tr>
	<tr>
		<th class="label">Gruppo utenza</th>
		<td class="input">
			<select class="xl" name="recordset[record_mode.group_id]">
				<{foreach $groups as $group}>
					<option value="<{$group->id}>"<{if $recordset->record_mode->group_id == $group->id}> selected="selected"<{/if}>><{$group->name}></option>
				<{/foreach}>
			</select>
			<{if array_key_exists("content_sorting", $errors)}>
			<p class="alert"><{$errors.content_sorting}></p>
			<{/if}>
		</td>
	</tr>
	
	<tr>
		<th class="label">Permessi di lettura</th>
		<td class="input">
			<select class="xl" name="recordset[record_mode.read_mode]">
				<option value="2"<{if $recordset->record_mode->read_mode == 2}> selected="selected"<{/if}>>Amministratori, owner</option>
				<option value="3"<{if $recordset->record_mode->read_mode == 3}> selected="selected"<{/if}>>Amministratori, owner, gruppo utenza</option>
				<option value="4"<{if $recordset->record_mode->read_mode == 4}> selected="selected"<{/if}>>Pagina pubblica</option>
			</select>
			<{if array_key_exists("content_sorting", $errors)}>
			<p class="alert"><{$errors.content_sorting}></p>
			<{/if}>
		</td>
	</tr>
	
	<tr>
		<th class="label">Permessi di modifica e creazione contenuti</th>
		<td class="input">
			<select class="xl" name="recordset[record_mode.edit_mode]">
				<option value="2"<{if $recordset->record_mode->edit_mode == 2}> selected="selected"<{/if}>>Amministratori, owner</option>
				<option value="3"<{if $recordset->record_mode->edit_mode == 3}> selected="selected"<{/if}>>Amministratori, owner, gruppo utenza</option>
				<option value="4"<{if $recordset->record_mode->edit_mode == 4}> selected="selected"<{/if}>>Pagina pubblica</option>
			</select>
			<{if array_key_exists("content_sorting", $errors)}>
			<p class="alert"><{$errors.content_sorting}></p>
			<{/if}>
		</td>
	</tr>
	
	<{if !$private.isAjaxRequest}>
	<tr>
		<th class="controls" colspan="2"><input type="submit" class="ciderbit_control xxl" value="Invia"/></th>
	</tr>
	<{/if}>
</table>
<{/ciderbit_edit_form}>