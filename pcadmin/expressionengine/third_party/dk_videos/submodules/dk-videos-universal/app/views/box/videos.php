<?php

if(count($videos) > 0)
{
	if(!isset($pagination['next_page']))
	{
		$pagination['next_page'] = false;
	}

	if($pagination['next_page'] === 2)
	{
		echo '<ul>';
	}

	foreach($videos as $video)
	{
		?>
		<li data-video-page="<?php echo $video['video_page']?>">
			<div class="videoplayer-videos-thumb">
				<span class="clip-outer">
					<span class="clip">
						<span class="clip-inner">
							<img src="<?php echo $video['thumbnail']?>" />
							<span class="vertical-align"></span>
						</span>
					</span>
				</span>
			</div>
			<div class="videoplayer-videos-text">
				<strong><?php echo $video['title']?></strong><br />
				<small>
					<?php echo $video['plays']?> <?php echo $this->lang_line('plays'); ?> - <?php echo $video['date']?><br />
					<?php echo $this->lang_line('from'); ?> <?php echo $video['username']?>
				</small>
			</div>
			<div class="clear"></div>
		</li>
		<?php
	}

	if($pagination['next_page'] && count($videos) == $pagination['per_page'])
	{
		?>
		<li class="videoplayer-videos-more" data-next-page="<?php echo $pagination['next_page']?>">
			<span class="videoplayer-videos-more-btn"><?php echo $this->lang_line('load_more_videos'); ?></span>
			<span class="videoplayer-videos-more-loading"><?php echo $this->lang_line('loading_videos'); ?>...</span>
		</li>
		<?php
	}

	if($pagination['next_page'] === 2)
	{
		echo '</ul>';
	}
}
else
{
	if($pagination['page'] == 1)
	{
		if(empty($q) && $this->input_post('method') == "service_search")
		{
			?>
			<p class="dkv-videos-empty"><?php echo $this->lang_line('search_'.$service->service_key.'_videos'); ?></p>
			<?php
		}
		else
		{
			?>
			<p class="dkv-videos-empty"><?php echo $this->lang_line('no_videos'); ?></p>
			<?php
		}
	}
}
?>