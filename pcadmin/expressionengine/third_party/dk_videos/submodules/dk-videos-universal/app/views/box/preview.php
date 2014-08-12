<?php
if($video)
{
	?>
	<div class="dukt-videos-current" data-video-url="<?=$video['url']?>">
		<div class="controls">	
			<a class="videoplayer-preview-fullscreen">
				<?php echo $this->lang_line('fullscreen'); ?>
			</a>
	
			<div class="splitter-light-top"></div>
	
			<a class="videoplayer-preview-favorite<?php
			if($video['is_favorite'])
			{
				echo " videoplayer-preview-favorite-selected";
			}
			?>" data-service="<?=$service?>"><?php echo $this->lang_line('add_favorite'); ?></a>
	
		</div>
	
		<div class="videoplayer-preview-video">
			<?php
			if(isset($video))
			{
				echo $video['embed'];
			}
			?>
		</div>
	
		<div class="videoplayer-preview-description">
			<div class="videoplayer-preview-description-in">
				<?php
		
				$description = $video['description'];
		
				if(strlen($description) > 0)
				{
					echo $description;
				} else {
					?>
					<p class="videoplayer-preview-description-empty"><?php echo $this->lang_line('no_description'); ?></p>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<?php
}
?>