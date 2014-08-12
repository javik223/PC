<h2>Player Settings</h2>

<form method="post" action="<?=admin_url('admin.php?page=dk-videos-player-settings&method=player_settings_save')?>">
	<?
	foreach($services as $service)
	{
		?>
		<h3><?=$service->service_name?></h3>	
	
		<?
		foreach($service->embed_options as $k => $v)
		{
			?>
			<p>
				<label><?=$k?></label><br />
				<input type="text" name="<?=$service->service_key.'_player_'.$k?>" value="<?=$dukt_videos->get_option($service->service_key, 'player_'.$k, $v)?>" />
			</p>
			<?
		}
	}
	?>

	<input type="submit" class="button-primary" value="Save Player Options" />
</form>