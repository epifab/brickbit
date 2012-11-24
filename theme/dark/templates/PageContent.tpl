<{function name=content_display content=null}>
	<{xmca_restricted_area component="EditContent" args=["id" => $content->id]}>
		<div class="content_controls top">
			<{xmca_control class="update" component="EditContent" width=800 height=550 title="Modifica dettaglio" args=["id" => $content->id]}> 
			<{if $level == 0}>
					<{xmca_control class="create" component="EditContent" width=800 height=550 title="Aggiungi dettaglio" args=["supercontent_id" => $content->id]}> 
			<{/if}>
			<{xmca_control class="delete" confirm=true confirmTitle="Il contenuto verr&agrave; eliminato definitivamente" component="DeleteContent" title="Elimina dettaglio" args=["id" => $content->id]}>
		</div>
	<{/xmca_restricted_area}>

	<div class="content_box">
		
		<{$content->tags|xmca_tags_link}>
	
		<{if $content->expandable}>
			<div class="content_preview" id="content_preview_<{$content->id}>">
				<{if $content->image_id}>
					<div class="content_preview_image">
						<img src="<{$content->image3_url}>" alt="<{$content->getEdit("title")}>" width="<{$content->image->width3}>" height="<{$content->image->height3}>"/>
					</div>
				<{/if}>
				<div<{if $content->image_id}> class="content_preview_body"<{/if}>>
					<h2 class="content_title"><a href="content/<{$content->url}>.html"><{$content->title}></a></h2>
					<{if $content->subtitle}>
						<h3 class="content_subtitle"><a href="content/<{$content->url}>.html"><{$content->subtitle}></a></h3>
					<{/if}>
					<{$content->preview}>
				</div>
					<div class="content_preview_controls">
						<a class="xmca_control" href="content/<{$content->url}>.html" onclick="ShowContent(<{$content->id}>); return false">Visualizza tutto &raquo;</a>
					</div>
			</div>
		<{/if}>

		<div class="content_full<{if $content->expandable}> hidden<{/if}>" id="content_full_<{$content->id}>">
			<{if $content->style_code == "STANDARD"}>
				<h2 class="content_title"><a href="content/<{$content->url}>.html"><{$content->title}></a></h2>
				<{if $content->subtitle}>
					<h3 class="content_subtitle"><a href="content/<{$content->url}>.html"><{$content->subtitle}></a></h3>
				<{/if}>
				<{if $content->image_id}>
					<div class="content_full_image upper">
						<img src="<{$content->image1_url}>" alt="<{$content->getEdit("title")}>" width="<{$content->image->width1}>" height="<{$content->image->height1}>"/>
					</div>
				<{/if}>
				<div class="content_full_body">
					<{$content->body}>
				</div>
			<{elseif $content->style_code == 2}>
				<h2 class="content_title"><a href="content/<{$content->url}>.html"><{$content->title}></a></h2>
				<{if $content->subtitle}>
					<h3 class="subtitle"><a href="content/<{$content->url}>.html"><{$content->subtitle}></a></h3>
				<{/if}>
				<{if $content->image_id}>
					<div class="content_full_image left">
						<img src="<{$content->image2_url}>" alt="<{$content->getEdit("title")}>" width="<{$content->image->width2}>" height="<{$content->image->height2}>"/>
					</div>
				<{/if}>
				<div class="content_full_body">
					<{$content->body}>
				</div>
			<{elseif $content->style_code == 3}>
				<{if $content->image_id}>
					<div class="content_full_image left">
						<img src="<{$content->image2_url}>" alt="<{$content->getEdit("title")}>" width="<{$content->image->width2}>" height="<{$content->image->height2}>"/>
					</div>
				<{/if}>
				<div class="content_full_body">
					<h2 class="content_title"><a href="content/<{$content->url}>.html"><{$content->title}></a></h2>
					<{if $content->subtitle}>
						<h3 class="content_subtitle"><a href="content/<{$content->url}>.html"><{$content->subtitle}></a></h3>
					<{/if}>
					<{$content->body}>
				</div>
			<{/if}>
			<div style="clear: both"></div>
			<{if $content->expandable}>
				<div class="content_preview_controls">
					<a class="xmca_control" href="javascript:HideContent(<{$content->id}>)">&laquo; Anteprima</a>
				</div>
			<{/if}>
		</div>

		<div style="clear: both"></div>
		
		<div class="subcontents<{if $content->expandable}> hidden<{/if}>" id="subcontents_<{$content->id}>">
			<{if count($content->contents)}>
				<{foreach $content->contents as $subcontent}>
					<div class="subcontent<{xmca_restricted_area component="EditContent"}> admin<{/xmca_restricted_area}>">
						<{content_display content=$subcontent level=($level+1)}>
					</div>
				<{/foreach}>
			<{/if}>
		</div>

		<{if $content->download_file_id}>
			<div class="download_link">
				<a href="<{if $private.login->isAnonymous()}>content/<{$content->url}>.html<{else}>content/Download/<{$content->download_file_name}><{/if}>">
<!--					<img src="img/download.jpg" alt="Download"/>-->
					<p class="download"><span class="first">D</span>OWNLOAD<br/></p>
					<{*$content->download_file_name|filename*}>
				</a>
			</div>
		<{/if}>
		<{if $content->social_networks}>
		<div class="social_network_controls">
<{* !--			<p>
				<div class="fb-like" data-href="<{$private.siteAddr}>content/<{$content->url}>" data-send="true" data-width="450" data-show-faces="true"></div>
			</p>--*}>
			<p>
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="<{$private.siteAddr}>content/<{$content->url}>.html" data-text="<{$content->getEdit('title')}>" data-via="EGerboni" data-lang="it">Tweet</a>
			</p>
		</div>
		<{/if}>
	</div>
		
	
	<{xmca_restricted_area component="EditContent" args=["id" => $content->id]}>
		<div class="content_controls bottom">
			<{xmca_control class="update" component="EditContent" width=800 height=550 title="Modifica dettaglio" args=["id" => $content->id]}> 
			<{if $level == 0}>
					<{xmca_control class="create" component="EditContent" width=800 height=550 title="Aggiungi dettaglio" args=["supercontent_id" => $content->id]}> 
			<{/if}>
			<{xmca_control class="delete" confirm=true confirmTitle="Il contenuto verr&agrave; eliminato definitivamente" component="DeleteContent" title="Elimina dettaglio" args=["id" => $content->id]}>
		</div>
	<{/xmca_restricted_area}>
<{/function}>