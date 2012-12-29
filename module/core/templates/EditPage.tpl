<{xmca_edit_form}>
<{if $recordset->id}>
<input type="hidden" name="id" value="<{$recordset->getEdit('id')}>"/>
<{/if}>
<{xmca_javascript}>
$(document).ready(function() {
	EditPageLang('<{$private.defaultLang}>');
});
<{/xmca_javascript}>

<table class="xmca_dataedit">
	<tr>
		<th class="label">URL</th>
		<td class="input">
			<input class="text xl" onchange="ChangeUrl()" id="edit_content_input_url" type="text" name="recordset[url]" value="<{$recordset->getEdit('url')}>"/>
			<p class="info">
				Una volta impostata una URL questa non potr&agrave; pi&ugrave; essere modificata.<br/>
				&Egrave; importante (per il posizionamento sui motori di ricerca) scegliere una URL vicina il pi&ugrave; possibile al titolo e/o ad una brevissima descrizione della pagina.<br/><br/>
				La pagina sar&agrave; direttamente raggiungibile all'indirizzo <{$private.siteAddr}><span id="edit_content_label_url">[URL]</span>.html
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
		<th class="label">Lingua</th>
		<td class="input">
			<div class="xmca_languages_input">
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
		<th class="label">Contenuto</th>
		<td class="input">
			<textarea rows="4" cols="50" class="rich_text_light" name="recordset[<{$text}>.body]"><{$recordset->$text->getEdit("body")}></textarea>
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
				<{foreach $pageStyles as $pageStyle}>
					<option value="<{$pageStyle->code}>"<{if $recordset->style_code == $pageStyle->code}> selected="selected"<{/if}>><{$pageStyle->description}></option>
				<{/foreach}>
			</select>
		</td>
	</tr>
	<tr>
		<th class="label">Ricerca contenuti</th>
		<td class="input">
			<input type="checkbox" name="recordset[content_filters]" id="content_filters_input" value="1"<{if $recordset->content_filters}> checked="checked"<{/if}>/> <label for="content_filters_input">Ricerca nella pagina</label>
			<{if array_key_exists("content_filters", $errors)}>
			<p class="alert"><{$errors.content_filters}></p>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Pagina contenuti</th>
		<td class="input">
			<select class="xl" name="recordset[content_paging]">
				<option value="0">Non paginare i contenuti</option>
				<option value="5"<{if $recordset->content_paging == 5}> selected="selected"<{/if}>>5 Contenuti per pagina</option>
				<option value="10"<{if $recordset->content_paging == 10}> selected="selected"<{/if}>>10 Contenuti per pagina</option>
				<option value="15"<{if $recordset->content_paging == 15}> selected="selected"<{/if}>>15 Contenuti per pagina</option>
				<option value="20"<{if $recordset->content_paging == 20}> selected="selected"<{/if}>>20 Contenuti per pagina</option>
				<option value="30"<{if $recordset->content_paging == 30}> selected="selected"<{/if}>>30 Contenuti per pagina</option>
				<option value="50"<{if $recordset->content_paging == 50}> selected="selected"<{/if}>>50 Contenuti per pagina</option>
			</select>
			<{if array_key_exists("content_paging", $errors)}>
			<p class="alert"><{$errors.content_paging}></p>
			<{/if}>
		</td>
	</tr>
	<tr>
		<th class="label">Ordinamento contenuti</th>
		<td class="input">
			<select class="xl" name="recordset[content_sorting]">
				<option value="sort_index_asc"<{if $recordset->content_sorting == "sort_index_asc"}> selected="selected"<{/if}>>Ordinamento manuale</option>
				<option value="date_desc"<{if $recordset->content_sorting == "date_desc"}> selected="selected"<{/if}>>Dal pi&ugrave; recente al pi&ugrave; vecchio</option>
				<option value="date_asc"<{if $recordset->content_sorting == "date_asc"}> selected="selected"<{/if}>>Dal pi&ugrave; vecchio al pi&ugrave; recente</option>
			</select>
			<{if array_key_exists("content_sorting", $errors)}>
			<p class="alert"><{$errors.content_sorting}></p>
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
		<th class="controls" colspan="2"><input type="submit" class="xmca_control xxl" value="Invia"/></th>
	</tr>
	<{/if}>
</table>
<{/xmca_edit_form}>