<?php /* Smarty version Smarty-3.1.12, created on 2012-11-17 22:17:40
         compiled from "theme\standard\templates\PageContent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1886650a80d04bb1951-74297541%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2f4143dd7aeeea513dd917d7bd2de1859578da32' => 
    array (
      0 => 'theme\\standard\\templates\\PageContent.tpl',
      1 => 1353171851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1886650a80d04bb1951-74297541',
  'function' => 
  array (
    'content_display' => 
    array (
      'parameter' => 
      array (
        'content' => NULL,
      ),
      'compiled' => '',
    ),
  ),
  'variables' => 
  array (
    'content' => 0,
    'level' => 0,
    'subcontent' => 0,
    'private' => 0,
  ),
  'has_nocache_code' => 0,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a80d055670c3_39878553',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a80d055670c3_39878553')) {function content_50a80d055670c3_39878553($_smarty_tpl) {?><?php if (!is_callable('smarty_block_xmca_restricted_area')) include 'tpl_plugins\block.xmca_restricted_area.php';
if (!is_callable('smarty_function_xmca_control')) include 'tpl_plugins\function.xmca_control.php';
if (!is_callable('smarty_modifier_xmca_tags_link')) include 'tpl_plugins\modifier.xmca_tags_link.php';
?><?php if (!function_exists('smarty_template_function_content_display')) {
    function smarty_template_function_content_display($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['content_display']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_restricted_area', array('component'=>"EditContent",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id))); $_block_repeat=true; echo smarty_block_xmca_restricted_area(array('component'=>"EditContent",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<div class="content_controls top">
			<?php echo smarty_function_xmca_control(array('class'=>"update",'component'=>"EditContent",'width'=>800,'height'=>550,'title'=>"Modifica dettaglio",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)),$_smarty_tpl);?>
 
			<?php if ($_smarty_tpl->tpl_vars['level']->value==0){?>
					<?php echo smarty_function_xmca_control(array('class'=>"create",'component'=>"EditContent",'width'=>800,'height'=>550,'title'=>"Aggiungi dettaglio",'args'=>array("supercontent_id"=>$_smarty_tpl->tpl_vars['content']->value->id)),$_smarty_tpl);?>
 
			<?php }?>
			<?php echo smarty_function_xmca_control(array('class'=>"delete",'confirm'=>true,'confirmTitle'=>"Il contenuto verr&agrave; eliminato definitivamente",'component'=>"DeleteContent",'title'=>"Elimina dettaglio",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)),$_smarty_tpl);?>

		</div>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_restricted_area(array('component'=>"EditContent",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	<div class="content_box">
		
		<?php echo smarty_modifier_xmca_tags_link($_smarty_tpl->tpl_vars['content']->value->tags);?>

	
		<?php if ($_smarty_tpl->tpl_vars['content']->value->expandable){?>
			<div class="content_preview" id="content_preview_<?php echo $_smarty_tpl->tpl_vars['content']->value->id;?>
">
				<?php if ($_smarty_tpl->tpl_vars['content']->value->image_id){?>
					<div class="content_preview_image">
						<img src="<?php echo $_smarty_tpl->tpl_vars['content']->value->image3_url;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['content']->value->getEdit("title");?>
" width="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->width3;?>
" height="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->height3;?>
"/>
					</div>
				<?php }?>
				<div<?php if ($_smarty_tpl->tpl_vars['content']->value->image_id){?> class="content_preview_body"<?php }?>>
					<h2 class="content_title"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->title;?>
</a></h2>
					<?php if ($_smarty_tpl->tpl_vars['content']->value->subtitle){?>
						<h3 class="content_subtitle"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->subtitle;?>
</a></h3>
					<?php }?>
					<?php echo $_smarty_tpl->tpl_vars['content']->value->preview;?>

				</div>
					<div class="content_preview_controls">
						<a class="xmca_control" href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html" onclick="ShowContent(<?php echo $_smarty_tpl->tpl_vars['content']->value->id;?>
); return false">Visualizza tutto &raquo;</a>
					</div>
			</div>
		<?php }?>

		<div class="content_full<?php if ($_smarty_tpl->tpl_vars['content']->value->expandable){?> hidden<?php }?>" id="content_full_<?php echo $_smarty_tpl->tpl_vars['content']->value->id;?>
">
			<?php if ($_smarty_tpl->tpl_vars['content']->value->style_code=="STANDARD"){?>
				<h2 class="content_title"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->title;?>
</a></h2>
				<?php if ($_smarty_tpl->tpl_vars['content']->value->subtitle){?>
					<h3 class="content_subtitle"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->subtitle;?>
</a></h3>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['content']->value->image_id){?>
					<div class="content_full_image upper">
						<img src="<?php echo $_smarty_tpl->tpl_vars['content']->value->image1_url;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['content']->value->getEdit("title");?>
" width="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->width1;?>
" height="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->height1;?>
"/>
					</div>
				<?php }?>
				<div class="content_full_body">
					<?php echo $_smarty_tpl->tpl_vars['content']->value->body;?>

				</div>
			<?php }elseif($_smarty_tpl->tpl_vars['content']->value->style_code==2){?>
				<h2 class="content_title"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->title;?>
</a></h2>
				<?php if ($_smarty_tpl->tpl_vars['content']->value->subtitle){?>
					<h3 class="subtitle"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->subtitle;?>
</a></h3>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['content']->value->image_id){?>
					<div class="content_full_image left">
						<img src="<?php echo $_smarty_tpl->tpl_vars['content']->value->image2_url;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['content']->value->getEdit("title");?>
" width="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->width2;?>
" height="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->height2;?>
"/>
					</div>
				<?php }?>
				<div class="content_full_body">
					<?php echo $_smarty_tpl->tpl_vars['content']->value->body;?>

				</div>
			<?php }elseif($_smarty_tpl->tpl_vars['content']->value->style_code==3){?>
				<?php if ($_smarty_tpl->tpl_vars['content']->value->image_id){?>
					<div class="content_full_image left">
						<img src="<?php echo $_smarty_tpl->tpl_vars['content']->value->image2_url;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['content']->value->getEdit("title");?>
" width="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->width2;?>
" height="<?php echo $_smarty_tpl->tpl_vars['content']->value->image->height2;?>
"/>
					</div>
				<?php }?>
				<div class="content_full_body">
					<h2 class="content_title"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->title;?>
</a></h2>
					<?php if ($_smarty_tpl->tpl_vars['content']->value->subtitle){?>
						<h3 class="content_subtitle"><a href="content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html"><?php echo $_smarty_tpl->tpl_vars['content']->value->subtitle;?>
</a></h3>
					<?php }?>
					<?php echo $_smarty_tpl->tpl_vars['content']->value->body;?>

				</div>
			<?php }?>
			<div style="clear: both"></div>
			<?php if ($_smarty_tpl->tpl_vars['content']->value->expandable){?>
				<div class="content_preview_controls">
					<a class="xmca_control" href="javascript:HideContent(<?php echo $_smarty_tpl->tpl_vars['content']->value->id;?>
)">&laquo; Anteprima</a>
				</div>
			<?php }?>
		</div>

		<div style="clear: both"></div>
		
		<div class="subcontents<?php if ($_smarty_tpl->tpl_vars['content']->value->expandable){?> hidden<?php }?>" id="subcontents_<?php echo $_smarty_tpl->tpl_vars['content']->value->id;?>
">
			<?php if (count($_smarty_tpl->tpl_vars['content']->value->contents)){?>
				<?php  $_smarty_tpl->tpl_vars['subcontent'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subcontent']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['content']->value->contents; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subcontent']->key => $_smarty_tpl->tpl_vars['subcontent']->value){
$_smarty_tpl->tpl_vars['subcontent']->_loop = true;
?>
					<div class="subcontent<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_restricted_area', array('component'=>"EditContent")); $_block_repeat=true; echo smarty_block_xmca_restricted_area(array('component'=>"EditContent"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
 admin<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_restricted_area(array('component'=>"EditContent"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
						<?php smarty_template_function_content_display($_smarty_tpl,array('content'=>$_smarty_tpl->tpl_vars['subcontent']->value,'level'=>($_smarty_tpl->tpl_vars['level']->value+1)));?>

					</div>
				<?php } ?>
			<?php }?>
		</div>

		<?php if ($_smarty_tpl->tpl_vars['content']->value->download_file_id){?>
			<div class="download_link">
				<a href="<?php if ($_smarty_tpl->tpl_vars['private']->value['login']->isAnonymous()){?>content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html<?php }else{ ?>content/Download/<?php echo $_smarty_tpl->tpl_vars['content']->value->download_file_name;?>
<?php }?>">
<!--					<img src="img/download.jpg" alt="Download"/>-->
					<p class="download"><span class="first">D</span>OWNLOAD<br/></p>
					
				</a>
			</div>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['content']->value->social_networks){?>
		<div class="social_network_controls">

			<p>
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $_smarty_tpl->tpl_vars['private']->value['siteAddr'];?>
content/<?php echo $_smarty_tpl->tpl_vars['content']->value->url;?>
.html" data-text="<?php echo $_smarty_tpl->tpl_vars['content']->value->getEdit('title');?>
" data-via="EGerboni" data-lang="it">Tweet</a>
			</p>
		</div>
		<?php }?>
	</div>
		
	
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('xmca_restricted_area', array('component'=>"EditContent",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id))); $_block_repeat=true; echo smarty_block_xmca_restricted_area(array('component'=>"EditContent",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<div class="content_controls bottom">
			<?php echo smarty_function_xmca_control(array('class'=>"update",'component'=>"EditContent",'width'=>800,'height'=>550,'title'=>"Modifica dettaglio",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)),$_smarty_tpl);?>
 
			<?php if ($_smarty_tpl->tpl_vars['level']->value==0){?>
					<?php echo smarty_function_xmca_control(array('class'=>"create",'component'=>"EditContent",'width'=>800,'height'=>550,'title'=>"Aggiungi dettaglio",'args'=>array("supercontent_id"=>$_smarty_tpl->tpl_vars['content']->value->id)),$_smarty_tpl);?>
 
			<?php }?>
			<?php echo smarty_function_xmca_control(array('class'=>"delete",'confirm'=>true,'confirmTitle'=>"Il contenuto verr&agrave; eliminato definitivamente",'component'=>"DeleteContent",'title'=>"Elimina dettaglio",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)),$_smarty_tpl);?>

		</div>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_xmca_restricted_area(array('component'=>"EditContent",'args'=>array("id"=>$_smarty_tpl->tpl_vars['content']->value->id)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
<?php }} ?>