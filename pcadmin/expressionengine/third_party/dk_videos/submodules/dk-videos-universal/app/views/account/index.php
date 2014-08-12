<h2><?=$this->lang_line('services_settings');?></h2>

<div class="videoplayer-mcp-accounts">
	<table class="">
		<tbody>
			<?
			foreach($services as $service)
			{
				$is_authenticated = $service->is_authenticated();

				?>

				<tr>
					<td class="videoplayer-mcp-col-status">
						<a href="<?
						if(!$service->enabled && $is_authenticated)
						{
							echo $links[$service->service_key]['enable'];
							// echo admin_url('admin.php?page=dk-videos-account&method=enable&service='.$service->service_key);
						}
						else
						{
							echo $links[$service->service_key]['disable'];
							// echo admin_url('admin.php?page=dk-videos-account&method=disable&service='.$service->service_key);
						}
						?>" class="videoplayer-mcp-<?
						if($service->enabled && $is_authenticated)
						{
							echo 'enabled';
						}
						else
						{
							echo 'disabled';
						}
						?>"><?
						if($service->enabled && $is_authenticated)
						{
							echo $this->lang_line('disable');
						}
						else
						{
							echo $this->lang_line('enable');
						}
						?></a>
					</td>
					
					<td>
						<span class="videoplayer-mcp-service-<?=$service->service_key?>">
							<?=$service->service_name?>
						</span>
					</td>
	
					<td class="videoplayer-mcp-col-configure">
						<a href="<?
						
						echo $links[$service->service_key]['configure'];
						// echo admin_url('admin.php?page=dk-videos-account&method=configure&service='.$service->service_key);
						
						?>" class="videoplayer-btn"><?=$this->lang_line('configure');?></a>
					</td>
	
					<td class="videoplayer-mcp-col-enable">
						<a href="<?
						if(!$service->enabled && $is_authenticated)
						{
							echo $links[$service->service_key]['enable'];
							//echo admin_url('admin.php?page=dk-videos-account&method=enable&service='.$service->service_key);
						}
						else
						{
							echo $links[$service->service_key]['disable'];
							//echo admin_url('admin.php?page=dk-videos-account&method=disable&service='.$service->service_key);
						}
						?>" class="videoplayer-btn<?
						if(!$is_authenticated)
						{
							echo ' videoplayer-btn-disabled';
						}
						?>">
						<?
						if($service->enabled && $is_authenticated)
						{
							echo $this->lang_line('disable');
						}
						else
						{
							echo $this->lang_line('enable');
						}
						?>
						</a>
					</td>
				</tr>
				
				<?
			}
			?>
		</tbody>
	</table>
</div>