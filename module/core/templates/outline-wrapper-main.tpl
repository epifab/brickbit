<response
	type="<{$system.responseType}>"
	url="<{$system.component.url}>"
	title="<{$page.title}>"
	editFormId="<{edit_form_id}>"
	panelFormId="<{panel_form_id}>"
	panelFormName="<{panel_form_name}>"
	id="<{$system.component.requestId}>">
	<content><{include file=$system.templates.main}></content>
	<javascript><{javascript}></javascript>
</response>