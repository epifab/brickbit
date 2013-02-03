<{if $node}>
<div class="node node-<{$node->type}> node-<{$node->id}>">
	<{protected url=$node->edit_url}>
		<div class="page-controls">
			<{link url=$node->edit_url width=800 height=420 title="Edit `$node->type`"}><{"Edit `$node->type`"|t}><{/link}>
			<{link url=$node->delete_url confirm=true confirmTitle="The `$node->type` will be deleted" title="Delete `$node->type`"}><{"Delete `$node->type`"|t}><{/link}>
			<{*link url="article/add" width=800 height=550 title="New article" args=["parent_id" => $node->id]}><{"New article"|t}><{/link*}>
		</div>
	<{/protected}>

	<{if $node->text}>
		<div class="node-text" lang="<{$node->text->lang}>">
			<{if $node->text->title}>
				<h1 class="node-text-title"><{$node->text->title}></h1>
			<{/if}>
			<{if $node->text->subtitle}>
				<h2 class="node-text-subtitle"><{$node->text->subtitle}></h1>
			<{/if}>
			<{if $node->text->preview}>
				<div class="node-text-preview"><{$node->text->preview}></div>
			<{/if}>
			<{if $node->text->body}>
				<div class="node-text-body"><{$node->text->body}></div>
			<{/if}>
		</div>
	<{/if}>
	
	<!-- CONTENUTI SEZIONE LIVELLO 1 -->
	<{foreach $node->children as $n}>
	<{/foreach}>
</div>
<{/if}>