<{function name=print_content content=null}>
	<div class="content_box">
	
		<div class="content_full" id="content_full_<{$content->id}>">
			<{if $content->layout_type == 1}>
				<h2 class="title"><{$content->title}></h2>
				<{if $content->subtitle}>
					<h3 class="subtitle"><{$content->subtitle}></h3>
				<{/if}>
				<{if $content->image_id}>
					<div class="content_full_image upper">
						<img src="<{$content->image1_url}>" alt="<{$content->getEdit("title")}>" width="<{$content->image->width1}>" height="<{$content->image->height1}>"/>
					</div>
				<{/if}>
				<div class="content_full_body">
					<{$content->body}>
				</div>
			<{elseif $content->layout_type == 2}>
				<h2 class="title"><{$content->title}></h2>
				<{if $content->subtitle}>
					<h3 class="subtitle"><{$content->subtitle}></h3>
				<{/if}>
				<{if $content->image_id}>
					<div class="content_full_image left">
						<img src="<{$content->image2_url}>" alt="<{$content->getEdit("title")}>" width="<{$content->image->width2}>" height="<{$content->image->height2}>"/>
					</div>
				<{/if}>
				<div class="content_full_body">
					<{$content->body}>
				</div>
			<{elseif $content->layout_type == 3}>
				<{if $content->image_id}>
					<div class="content_full_image left">
						<img src="<{$content->image2_url}>" alt="<{$content->getEdit("title")}>" width="<{$content->image->width2}>" height="<{$content->image->height2}>"/>
					</div>
				<{/if}>
				<div class="content_full_body">
					<h2 class="title"><{$content->title}></h2>
					<{if $content->subtitle}>
						<h3 class="subtitle"><{$content->subtitle}></h3>
					<{/if}>
					<{$content->body}>
				</div>
			<{/if}>
		</div>

		<div style="clear: both"></div>

		<div class="subcontents" id="subcontents_<{$content->id}>">
			<{if count($content->contents)}>
				<{foreach $content->contents as $subcontent}>
					<div class="subcontent<{xmca_restricted_area component="EditContent"}> admin<{/xmca_restricted_area}>">
						<{content_display content=$subcontent level=($level+1)}>
					</div>
				<{/foreach}>
			<{/if}>
		</div>

		<{if $content->download_file_id}>
			<{if !$private.login->isAnonymous()}>
				<div class="download_link">
					<a href="Content/Download/<{$content->download_file_name}>">
						<p class="download"><span class="first">D</span>OWNLOAD<br/></p>
						<{*$content->download_file_name|filename*}>
					</a>
				</div>
			<{elseif $level == 1}>
				<div class="download_link">
					<div class="disabled_link">
						<p class="download"><span class="first">D</span>OWNLOAD<br/></p>
						<{*$content->download_file_name|filename*}>
					</div>
				</div>

				<{if $signup}>
					<{* Iscrizione appena completata al portale *}>
					<div>
						<h3>Benvenuto su <{$private.siteName}>!</h3>
						<p>La password per accedere al portale &egrave; stata inviata al tuo indirizzo email.</p>
						<p class="info">Se non hai ricevuto la password <{xmca_control component="SendUserPassword" title="clicca qui" style="link"}> per riceverla nuovamente o in alternativa contatta il nostro staff tramite la pagina <a href="Contatti.html">contatti</a>.</p>
					</div>
					<h2>Effettua il login per accedere</h2>
					<div class="signup">
						<form action="Content.html" method="post">
							<div class="row">
								<div class="cell s"><label for="login_username">Email</label></div>
								<div class="cell l">
									<input type="text" class="text l" id="login_username" name="username"/>
								</div>
							</div>
							<div class="row">
								<div class="cell s"><label for="login_userpass">Password</label></div>
								<div class="cell l">
									<input type="password" class="text l" id="login_userpass" name="userpass"/>
								</div>
							</div>
							<div class="row">
								<div class="cell l right">
									<{if isset($loginError)}><p class="alert"><{$loginError}></p><{/if}>
									<input type="hidden" name="login" value="1"/>
									<input type="hidden" name="id" value="<{$content->id}>"/>
									<input type="submit" class="xmca_control xl" value="Accedi"/>
								</div>
							</div>
							<div style="clear: both"></div>
						</form>
					</div>
				<{else}>
					<div>
						<h3 class="alert">Per scaricare questo contenuto devi essere iscritto al portale</h3>
						<p>L'iscrizione &egrave; totalmente gratuita e ti porterà via soltanto un minuto!</p>
					</div>

					<div class="signup">
						<form action="Content.html" method="post">
							<div class="row">
								<div class="cell s"><label for="full_name">Nome completo</label></div>
								<div class="cell l">
									<input type="text" class="text l" id="full_name" name="recordset[full_name]"/>
									<{if isset($signupErrors) && array_key_exists("full_name", $signupErrors)}>
									<p class="alert"><{$signupErrors.full_name}></p>
									<{/if}>
								</div>
							</div>
							<div class="row">
								<div class="cell s"><label for="email">Email</label></div>
								<div class="cell l">
									<input type="text" class="text l" id="email" name="recordset[email]"/>
									<{if isset($signupErrors) && array_key_exists("email", $signupErrors)}>
									<p class="alert"><{$signupErrors.email}></p>
									<{/if}>
								</div>
							</div>
							<div class="row">
								<div class="cell l right">
									<input type="hidden" name="signup" value="1"/>
									<input type="hidden" name="id" value="<{$content->id}>"/>
									<input type="submit" class="xmca_control xl" value="Completa iscrizione"/>
								</div>
							</div>
							<div style="clear: both"></div>
						</form>

						<div></div>
					</div>

					<div>
						<p>La password per accedere al portale verr&agrave; inviata al tuo indirizzo email.</p>
						<p class="info">Se non hai ricevuto la password o l'hai dimenticata, <{xmca_control component="SendUserPassword" title="clicca qui" okButtonLabel="Invia nuova password" style="link"}> per riceverla nuovamente o in alternativa contatta il nostro staff tramite la pagina <a href="Contatti.html">contatti</a></p>
					</div>

					<h3>Sei già iscritto? <{xmca_control component="Login" style="link" title="Effettua il login" okButtonLabel="Login" width=300 showResponse=false}></h3>
				<{/if}>
			<{else}>
				<div class="download_link">
					<a href="Content/<{$content->url}>.html">
						<p class="download"><span class="first">D</span>OWNLOAD<br/></p>
						<{*$content->download_file_name|filename*}>
					</a>
				</div>
			<{/if}>
		<{/if}>

	</div>
<{/function}>
<{xmca_read_form}><{/xmca_read_form}>
<{xmca_read_content}>
<div class="content_nav">
	<a href="<{$content->section->component_name}>.<{$private.componentExt}>"><{$content->section->title}></a>
	<{if $content->parent_content_id}>
		| <a href="Content/<{$content->content->url}>.html"><{$content->content->title}></a>
	<{/if}>
	| <a href="Content/<{$content->url}>.html"><{$content->title}></a>
</div>
<div class="section content">
	<{content_display content=$content level=1}>
</div>
<{/xmca_read_content}>