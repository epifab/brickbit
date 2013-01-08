<{edit_form}>
	<{if $recordset->id}>
	<input type="hidden" name="id" value="<{$recordset->getEdit('id')}>"/>
	<{/if}>

	<table class="dataedit">
		<tr>
			<th class="label">URN</th>
			<td class="input">
				<input class="text xl" onchange="ChangeUrl()" id="edit-content-input-urn" type="text" name="recordset[urn]" value="<{$recordset->getEdit('urn')}>"/>
				<p class="info">
					<{"Once you choose a URN you shouldn't change it anymore."|t}><br/>
					<{"In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself."|t}>
					<{"Each word should be separeted by the dash characted."|t}>
				</p>
				<{if array_key_exists("urn", $errors)}>
				<p class="alert"><{$errors.urn}></p>
				<{/if}>
			</td>
		</tr>
		<tr>
			<th class="label"><{"Language"|t}></th>
			<td class="input">
				<div class="langs-input">
					<{foreach $system.langs as $lang}>
					<a href="javascript:EditPageLang('<{$lang}>')" id="lang-control-<{$lang}>" class="lang_control"><img src="<{theme_path url="img/lang/40/`$lang`.jpg"}>"/></a>
					<{/foreach}>
				</div>
			</td>
		</tr>
		<{*foreach $system.langs as $lang}>
		<{assign var='text' value="text_`$lang`"}>
		<tr class="lang lang_<{$lang}>">
			<th class="label"><{"Title"|t}></th>
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
			<th class="label"><{"Content"|t}></th>
			<td class="input">
				<textarea rows="4" cols="50" class="rich_text_light" name="recordset[<{$text}>.body]"><{$recordset->$text->getEdit("body")}></textarea>
				<{if array_key_exists($text, $errors) && array_key_exists("body", $errors.$text)}>
				<p class="alert"><{$errors.$text.body}></p>
				<{/if}>
			</td>
		</tr>
		<{/foreach*}>
		<tr>
			<th colspan="2" class="row">Gestione permessi</th>
		</tr>
		<tr>
			<th class="label"><{"Content admins"|t}></th>
			<td class="input">
				<input type="text" name="recordset[record_mode_users]" id="edit-record-mode-users" value=""/>
				<{if array_key_exists("content_sorting", $errors)}>
				<p class="alert"><{$errors.content_sorting}></p>
				<{/if}>
			</td>
		</tr>

		<tr>
			<th class="label"><{"Read access"|t}></th>
			<td class="input">
				<select class="xl" name="recordset[record_mode.read_mode]">
					<option value="2"<{if $recordset->record_mode->read_mode == 2}> selected="selected"<{/if}>><{"Owner only"|t}></option>
					<option value="3"<{if $recordset->record_mode->read_mode == 3}> selected="selected"<{/if}>><{"Owner and group"|t}></option>
					<option value="4"<{if $recordset->record_mode->read_mode == 4}> selected="selected"<{/if}>><{"Public content"|t}></option>
				</select>
				<{if array_key_exists("content_sorting", $errors)}>
				<p class="alert"><{$errors.content_sorting}></p>
				<{/if}>
			</td>
		</tr>

		<tr>
			<th class="label"><{"Edit access"|t}></th>
			<td class="input">
				<select class="xl" name="recordset[record_mode.edit_mode]">
					<option value="2"<{if $recordset->record_mode->edit_mode == 2}> selected="selected"<{/if}>><{"Owner only"|t}></option>
					<option value="3"<{if $recordset->record_mode->edit_mode == 3}> selected="selected"<{/if}>><{"Owner and group"|t}></option>
				</select>
				<{if array_key_exists("content_sorting", $errors)}>
				<p class="alert"><{$errors.content_sorting}></p>
				<{/if}>
			</td>
		</tr>

		<{if !$system.ajax}>
		<tr>
			<th class="controls" colspan="2"><input type="submit" class="xmca_control xxl" value="Invia"/></th>
		</tr>
		<{/if}>
	</table>
<{/edit_form}>