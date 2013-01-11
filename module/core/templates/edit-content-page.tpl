<{edit_form}>
	<{if $node->id}>
	<input type="hidden" name="id" value="<{$node->getEdit('id')}>"/>
	<{/if}>

	<div class="dataedit">
		<div class="de-row">
			<div class="de-label-wrapper">
				<label class="de-label" for="edit-node-urn"><{"URN"|t}></label>
			</div>
			<div class="de-input-wrapper">
				<input type="text" class="de-input xl" name="node[urn]" id="edit-node-urn" value="<{$node->getEdit('urn')}>"/>
				<p class="de-info">
					<{"Once you choose a URN you shouldn't change it anymore."|t}><br/>
					<{"In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself."|t}>
					<{"Each word should be separeted by the dash characted."|t}>
				</p>
				<{de_form_error path="urn"}>
			</div>
		</div>
		<fieldset>
			<legend>
				<{foreach $system.langs as $lang}>
				<a href="#" id="node-lang-<{$lang}>" class="node-lang-control show-hide-class<{if $lang == $website.defaultLang}> expanded<{/if}>"><img src="<{theme_path url="img/lang/40/`$lang`.jpg"}>"/></a>
				<{/foreach}>
			</legend>
			<{foreach $system.langs as $lang}>
			<{assign var='text' value="text_`$lang`"}>
			<{capture name="langDesc"}><{"@lang"|t:['@lang'=>$lang]}><{/capture}>
			<div class="node-lang node-lang-<{$lang}>">
				<{if $lang != $website.defaultLang}>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<{$text}>-urn"><{"URN alias"|t}></label>
					</div>
					<div class="de-input-wrapper">
						<input type="text" class="de-input xl" name="node[<{$text}>.urn]" id="edit-node-<{$text}>-urn" value="<{$node->$text->getEdit('urn')}>"/>
						<p class="de-info">
							<{"URN translation."|t}><br/>
							<{"Please follow the same instruction as the general URN."|t}><br/>
							<{"Please note also that two different contents, translated in @lang, must have two different URNs."|t:['@lang'=>$smarty.capture.langDesc]}>
						</p>
						<{de_form_error path="`$text`.urn"}>
					</div>
				</div>
				<{/if}>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<{$text}>-title"><{"Title"|t}></label>
					</div>
					<div class="de-input-wrapper">
						<input class="de-input l" type="text" id="edit-node-<{$text}>-title" name="node[<{$text}>.title]" value="<{$node->$text->getEdit('title')}>"/>
						<{de_form_error path="`$text`.title"}>
					</div>
				</div>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<{$text}>-subtitle"><{"Subtitle"|t}></label>
					</div>
					<div class="de-input-wrapper">
						<input class="de-input xl" type="text" id="edit-node-<{$text}>-subtitle" name="node[<{$text}>.subtitle]" value="<{$node->$text->getEdit('subtitle')}>"/>
						<{de_form_error path="`$text`.subtitle"}>
					</div>
				</div>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<{$text}>-body"><{"Body"|t}></label>
					</div>
					<div class="de-input-wrapper">
						<textarea class="de-input xxl rich-text" id="edit-node-<{$text}>-body" name="node[<{$text}>.body]"><{$node->$text->getEdit('body')}></textarea>
						<{de_form_error path="`$text`.body"}>
					</div>
				</div>
				<div class="de-row">
					<div class="de-label-wrapper">
						<label class="de-label" for="edit-node-<{$text}>-preview"><{"Preview"|t}></label>
					</div>
					<div class="de-input-wrapper">
						<textarea class="de-input xxl rich-text" id="edit-node-<{$text}>-preview" name="node[<{$text}>.preview]"><{$node->$text->getEdit('preview')}></textarea>
						<{de_form_error path="`$text`.preview"}>
					</div>
				</div>
			</div>
			<{/foreach}>
		</fieldset>
		
		<fieldset class="de-fieldset">
			<legend><{"Content access"|t}></legend>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-users"><{"Content admininstrators"|t}></label>
				</div>
				<div class="de-input-wrapper">
					<input class="de-input xl" type="text" name="node[record_mode.users]" id="edit-node-record_mode-users" value=""/>
					<{de_form_error path="record_mode.users"}>
				</div>
			</div>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-read_mode"><{"Read access"|t}></label>
				</div>
				<div class="de-input-wrapper">
					<select class="de-input l" id="edit-node-record_mode-read_mode" name="node[record_mode.read_mode]">
						<option value="2"<{if $node->record_mode->read_mode == 2}> selected="selected"<{/if}>><{"Owner only"|t}></option>
						<option value="3"<{if $node->record_mode->read_mode == 3}> selected="selected"<{/if}>><{"Owner and group"|t}></option>
						<option value="4"<{if $node->record_mode->read_mode == 4}> selected="selected"<{/if}>><{"Public content"|t}></option>
					</select>
					<{de_form_error path="record_mode.read_mode"}>
				</div>
			</div>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-edit_mode"><{"Edit access"|t}></label>
				</div>
				<div class="de-input-wrapper">
					<select class="de-input l" id="edit-node-record_mode-edit_mode" name="node[record_mode.edit_mode]">
						<option value="1"<{if $node->record_mode->edit_mode == 1}> selected="selected"<{/if}>><{"Nobody"|t}></option>
						<option value="2"<{if $node->record_mode->edit_mode == 2}> selected="selected"<{/if}>><{"Owner only"|t}></option>
						<option value="3"<{if $node->record_mode->edit_mode == 3}> selected="selected"<{/if}>><{"Owner and group"|t}></option>
					</select>
					<{de_form_error path="record_mode.edit_mode"}>
				</div>
			</div>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-delete_mode"><{"Delete access"|t}></label>
				</div>
				<div class="de-input-wrapper">
					<select class="de-input l" id="edit-node-record_mode-delete_mode" name="node[record_mode.delete_mode]">
						<option value="1"<{if $node->record_mode->edit_mode == 1}> selected="selected"<{/if}>><{"Nobody"|t}></option>
						<option value="2"<{if $node->record_mode->edit_mode == 2}> selected="selected"<{/if}>><{"Owner only"|t}></option>
						<option value="3"<{if $node->record_mode->edit_mode == 3}> selected="selected"<{/if}>><{"Owner and group"|t}></option>
					</select>
					<{de_form_error path="record_mode.delete_mode"}>
				</div>
			</div>
		</fieldset>
		<{de_submit_control}>
	</div>
<{/edit_form}>