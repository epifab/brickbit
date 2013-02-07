<{if $node}>
<div class="page">
	<{protected url="node/edit/" args=["id" => $node->id]}>
		<div class="page-controls">
			<{link url="page/edit/`$node->urn`" width=800 height=420 title="Edit page" args=["id" => $node->id]}><{"Edit page"|t}><{/link}>
			<{link url="article/add" width=800 height=550 title="New article" args=["parent_id" => $node->id]}><{"New article"|t}><{/link}>
			<{link url="page/delete/`$node->urn`" confirm=true confirmTitle="The page will be deleted" title="Delete page"}><{"Delete page"|t}><{/link}>
		</div>
	<{/protected}>

	<div class="page-body">
		<div><{$node->getRead("body")}></div>
	</div>

	<!-- CONTENUTI SEZIONE LIVELLO 1 -->
	<{if count($page->articles) > 0}>
		<{include file="PageContent.tpl"}>
		<{foreach $page->contents as $content}>
			<div class="content">
				<{call name="content_display" content=$content level=0}>
			</div>
		<{/foreach}>
	<{/if}>
</div>
<{/if}>