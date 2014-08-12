<h2><a href="<?

echo $links['back'];

// echo admin_url('admin.php?page=dk-videos-account');

?>"><?=$this->lang_line('services_settings');?></a> / <?=$service->service_name?></h2>

<?
if(!$service->is_authenticated())
{
	?>
	
	<p><?=$this->lang_line('connect_website_to');?>  <?=$service->service_name?></p>
		
	<?=$form_open?>
		
		<fieldset>
			<legend>API Configuration</legend>
			<?
			echo $this->lang_line($service->service_key.'_api_instructions');
			?>
			<?
			foreach($service->api_options as $k => $v)
			{
				?>
				<p>
					<label><?=$this->lang_line(''.$service->service_key."_".$k);?></label><br />
					<input type="text" name="<?=$k?>" value="<?=$dukt_videos->get_option($service->service_key, $k)?>" />
				</p>
				<?
			}
		
			?>
			<div class="margin-form">
				<input type="submit" class="button-primary" name="connect" value="<?=$this->lang_line('connect_to');?> <?=$service->service_name?>" />
			</div>
			
		</fieldset>
	<?=$form_close?>
	
	<?
}
else
{
	?>
	
	<fieldset>
		<p><?=$this->lang_line('website_connected_to');?> <?=$service->service_name?>.</p>
		
		<p><a href="<?
			echo $links['reset'];
			// echo admin_url('admin.php?page=dk-videos-account&method=reset&service='.$service->service_key);
			?>"><?=$this->lang_line('reset_connection');?></a></p>
	
	</fieldset>
	<?	
}
?>