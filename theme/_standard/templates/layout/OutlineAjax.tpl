<response
	type="<{$private.responseType}>"
	name="<{$private.componentName}>"
	address="<{$private.componentAddr}>"
	title="<{$private.pageTitle}>"
	formId="<{$private.formId}>"
	contId="<{$private.contId}>"
	id="<{$private.requestId}>">
	<content><{include file=$private.mainTemplate}></content>
	<javascript><{xmca_get_javascript}></javascript>
</response>