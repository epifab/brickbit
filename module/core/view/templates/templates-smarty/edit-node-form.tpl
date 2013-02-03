<{*
<form class="dataedit" id="fileupload" action="<{path url="file/upload"}>" method="POST" enctype="multipart/form-data">
	<fieldset>
		<legend>Attachement</legend>
		<div class="de-row">
			<div class="de-cell">
				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
				<div class="row fileupload-buttonbar">
					<div class="span7">
						<!-- The fileinput-button span is used to style the file input field as button -->
						<span class="btn btn-success fileinput-button">
							<i class="icon-plus icon-white"></i>
							<span>Add files...</span>
							<input type="file" name="files[]" multiple>
						</span>
						<button type="submit" class="btn btn-primary start">
							<i class="icon-upload icon-white"></i>
							<span>Start upload</span>
						</button>
						<button type="reset" class="btn btn-warning cancel">
							<i class="icon-ban-circle icon-white"></i>
							<span>Cancel upload</span>
						</button>
						<button type="button" class="btn btn-danger delete">
							<i class="icon-trash icon-white"></i>
							<span>Delete</span>
						</button>
						<input type="checkbox" class="toggle">
					</div>
					<!-- The global progress information -->
					<div class="span5 fileupload-progress fade">
						<!-- The global progress bar -->
						<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
							<div class="bar" style="width:0%;"></div>
						</div>
						<!-- The extended global progress information -->
						<div class="progress-extended">&nbsp;</div>
					</div>
				</div>
				<!-- The loading indicator is shown during file processing -->
				<div classs="fileupload-loading"></div>
				<!-- The table listing the files available for upload/download -->
				<table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
			</div>
		</div>
	</fieldset>
</form>
*}>

<form class="dataedit" id="fileupload" action="<{path url="file/upload"}>" method="POST" enctype="multipart/form-data">
	<fieldset>
		<legend><{"Image"|t}></legend>
		<div class="de-row">
			<div class="de-label-wrapper">
				<span class="de-label"><{"Image"|t}></span>
			</div>
			<div class="de-input-wrapper">
				<input type="hidden" name="system[requestId]" value="<{$system.component.requestId}>"/>
				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
				<div class="row fileupload-buttonbar">
					<div class="span7">
						<!-- The fileinput-button span is used to style the file input field as button -->
						<span class="btn btn-success fileinput-button">
							<i class="icon-plus icon-white"></i>
							<span>Add file...</span>
							<input type="file" name="files[]" multiple>
						</span>
						<button type="submit" class="btn btn-primary start">
							<i class="icon-upload icon-white"></i>
							<span>Start upload</span>
						</button>
						<button type="reset" class="btn btn-warning cancel">
							<i class="icon-ban-circle icon-white"></i>
							<span>Cancel upload</span>
						</button>
					</div>
					<!-- The global progress information -->
					<div class="span5 fileupload-progress fade">
						<!-- The global progress bar -->
						<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
							<div class="bar" style="width:0%;"></div>
						</div>
						<!-- The extended global progress information -->
						<div class="progress-extended">&nbsp;</div>
					</div>
				</div>
				<!-- The loading indicator is shown during file processing -->
				<div classs="fileupload-loading"></div>
				<!-- The table listing the files available for upload/download -->
				<table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
			</div>
		</div>
	</fieldset>
</form>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"/></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
        </td>
    </tr>
{% } %}
</script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
<!-- jQuery Image Gallery -->
<script src="http://blueimp.github.com/jQuery-Image-Gallery/js/jquery.image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<{path url="js/jquery-file-upload/js/jquery.iframe-transport.js"}>"></script>
<!-- The basic File Upload plugin -->
<script src="<{path url="js/jquery-file-upload/js/jquery.fileupload.js"}>"></script>
<!-- The File Upload file processing plugin -->
<script src="<{path url="js/jquery-file-upload/js/jquery.fileupload-fp.js"}>"></script>
<!-- The File Upload user interface plugin -->
<script src="<{path url="js/jquery-file-upload/js/jquery.fileupload-ui.js"}>"></script>
<!-- The File Upload jQuery UI plugin -->
<script src="<{path url="js/jquery-file-upload/js/jquery.fileupload-jui.js"}>"></script>


<{edit_form}>
	<{if $node->id}>
	<input type="hidden" name="id" value="<{$node->getEdit('id')}>"/>
	<{/if}>

	<div class="dataedit">
		<{*
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
		*}>
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
				<div class="de-row">
					<div class="de-label-wrapper">
					</div>
					<div class="de-input-wrapper">
						<input type="checkbox" class="de-input show-hide-class" name="node[<{$text}>.enable]"<{if $node->$text->lang}> checked="checked"<{/if}> id="edit-node-<{$text}>-enable"/> <label for="edit-node-<{$text}>-enable"><{"Content available for this language."|t}></label>
					</div>
				</div>
				<div id="node-lang-<{$lang}>-fields" class="edit-node-<{$text}>-enable">
					<div class="de-row">
						<div class="de-label-wrapper">
							<label class="de-label" for="edit-node-<{$text}>-urn"><{"URN"|t}></label>
						</div>
						<div class="de-input-wrapper">
							<input type="text" class="de-input xl" name="node[<{$text}>.urn]" id="edit-node-<{$text}>-urn" value="<{$node->$text->getEdit('urn')}>"/>
							<div class="de-info">
								<p>
									<{"Once you choose a URN you shouldn't change it anymore."|t}><br/>
									<{"In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself."|t}>
									<{"Each word should be separeted by the dash characted."|t}>
								</p>
								<p>
									<{"Please note also that two different contents, translated in @lang, must have two different URNs."|t:['@lang'=>$smarty.capture.langDesc]}>
								</p>
							</div>
							<{de_form_error path="`$text`.urn"}>
						</div>
					</div>
					<div class="de-row">
						<div class="de-label-wrapper">
							<label class="de-label" for="edit-node-<{$text}>-description"><{"Description"|t}></label>
						</div>
						<div class="de-input-wrapper">
							<input class="de-input xxl" type="text" id="edit-node-<{$text}>-description" name="node[<{$text}>.description]" value="<{$node->$text->getEdit('description')}>"/>
							<div class="de-info">
								<p>
									<{"The description is not directly shown to the user but it's used as a meta-data for search engines purposes."|t}>
								</p>
							</div>
							<{de_form_error path="`$text`.description"}>
						</div>
					</div>
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
			</div>
			<{/foreach}>
		</fieldset>

		<fieldset>
			<legend><{"Tags"|t}></legend>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-terms"/>
						<{"Tags"|t}>
					</label>
				</div>
				<div class="de-input-wrapper">
					<input type="text" class="de-input xl" name="node[tags]"/>
				</div>
			</div>
		</fieldset>
		<{*
		<fieldset class="de-fieldset">
			<legend><{"Attachements"|t}></legend>
			<div class="de-row">
				<div class="de-label-wrapper">
					<label class="de-label" for="edit-node-record_mode-users"><{"Image"|t}></label>
				</div>
				<div class="de-input-wrapper">
					
					
					<{de_form_error path="record_mode.users"}>
				</div>
			</div>
		</fieldset>
		*}>
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
						<option value="3"<{if $node->record_mode->read_mode == 3}> selected="selected"<{/if}>><{"Content admins"|t}></option>
						<option value="4"<{if $node->record_mode->read_mode == 4}> selected="selected"<{/if}>><{"Registered users"|t}></option>
						<option value="5"<{if $node->record_mode->read_mode == 5}> selected="selected"<{/if}>><{"Anyone"|t}></option>
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
						<option value="3"<{if $node->record_mode->edit_mode == 3}> selected="selected"<{/if}>><{"Content admins"|t}></option>
						<option value="3"<{if $node->record_mode->edit_mode == 4}> selected="selected"<{/if}>><{"Registered users"|t}></option>
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
						<option value="1"<{if $node->record_mode->delete_mode == 1}> selected="selected"<{/if}>><{"Nobody"|t}></option>
						<option value="2"<{if $node->record_mode->delete_mode == 2}> selected="selected"<{/if}>><{"Owner only"|t}></option>
						<option value="3"<{if $node->record_mode->delete_mode == 3}> selected="selected"<{/if}>><{"Content admins"|t}></option>
						<option value="3"<{if $node->record_mode->delete_mode == 4}> selected="selected"<{/if}>><{"Registered users"|t}></option>
					</select>
					<{de_form_error path="record_mode.delete_mode"}>
				</div>
			</div>
		</fieldset>
		<{de_submit_control}>
	</div>
<{/edit_form}>