<div id="settings">
	
	<h2>Setup video services</h2>
	
	<?
	foreach($services as $service)
	{
		?>
		<h3><?=$service->service_name;?></h3>

		<?
		
		$pre = '';
		
		foreach($service->options as $k => $v)
		{
			if($v)
			{
				switch($k)
				{
					case "api_key":
					case "api_secret":
					case "client_id":
					case "client_secret":
					case "developer_key":
					break;
					
					default:
					$pre .= $k." : ".$v."\r\n";					
				}
			}
		}
		
		if(!empty($pre))
		{
			echo '<pre>'.$pre.'</pre>';
		}

		if($service->is_authenticated())
		{
			?>
			<p>
				You are connected to <?=$service->service_name?>
				<br />

				<?=anchor($links[$service->_service_key]['reset'], 'Reset authentication')?>
			</p>
			<?
		}
		else
		{
			?>
			<p><?=anchor('/wp-admin/admin.php?page=dk-videos-account&method=connect&service='.$service->service_key, 'Connect')?></p>
			<?
		}
	}
	?>
</div>