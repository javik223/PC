<!-- .videoplayer-box -->

<div class="dukt-videos-wrapper">
<div class="videoplayer-box<?

if($this->input_post('method'))
{
	// lightbox mode

	echo ' videoplayer-lightbox';
}



?>">
	<!-- .videoplayer-accounts -->

	<div class="videoplayer-accounts">

		<div class="top">
			<a class="videoplayer-close">
				<span class="splitter-top-bottom">
					<span class="splitter-top-right">
						<?php echo $this->lang_line('close'); ?>
					</span>
				</span>
			</a>
		</div>

		<!-- .videoplayer-services -->

		<ul class="videoplayer-services">
			<?php

			foreach($services as $service)
			{
				if($service->enabled && $service->is_authenticated())
				{
					?>
					<li class="videoplayer-<?php echo $service->service_key?>" data-service="<?php echo $service->service_key?>">
						<a data-method="videos" data-service="<?php echo $service->service_key?>" class="videoplayer-service"><?php echo $service->service_key?></a>
						<ul class="videoplayer-service-actions">
							<li><a data-service="<?=$service->service_key?>" data-listing="search" data-method="service_search" class="videoplayer-service-search"><?php echo $this->lang_line('search'); ?></a></li>
							<li><a data-service="<?=$service->service_key?>" data-listing="my-videos" data-method="service_videos" class="videoplayer-service-videos"><?php echo $this->lang_line('videos'); ?></a></li>
							<li><a data-service="<?=$service->service_key?>" data-listing="favorites" data-method="service_favorites" class="videoplayer-service-favorites"><?php echo $this->lang_line('favorites'); ?></a></li>
<!-- 							<li><a data-service="<?=$service->service_key?>" data-listing="playlists" data-method="service_playlists" class="videoplayer-service-playlists"><?php echo $this->lang_line('Playlists'); ?></a></li> -->
						</ul>
					</li>
					<?php
				}
			}
			?>

		</ul>

		<!-- /.videoplayer-services -->

		<div class="bottom">
			<span class="splitter-bottom">
				<span class="splitter-bottom-right">
					<a class="videoplayer-manage-btn" href="<?php echo $manage_link?>"><?php echo $this->lang_line('configure'); ?></a>
					
					<div class="dk-videos-status">
						<div class="spin"></div>
						
						<div class="reload"></div>
					</div>

				</span>
			</span>
		</div>
		
		<!-- .bottom -->
		
	</div>

	<!-- /.videoplayer-accounts -->



	<!-- .videoplayer-listings -->

	<div class="videoplayer-listings">
	
		<?
		
			foreach($services as $service)
			{
				if($service->enabled && $service->is_authenticated())
				{
					$vars = array('service' => $service);
					
					$this->load_view('box/listing-search', $vars);
					$this->load_view('box/listing-my-videos', $vars);
					$this->load_view('box/listing-favorites', $vars);
					$this->load_view('box/listing-playlists', $vars);			
				}
			}
		?>

		<div class="videoplayer-old-listings">
			<div class="top">
	
				<div class="splitter-top-bottom">
					<div class="splitter-top-left">
						<div class="splitter-top-right">
							<!-- .videoplayer-search -->
	
							<div class="videoplayer-search">
								<div class="search-bg">
									<div class="search-left">
										<div class="search-right">
											<input type="text" placeholder="<?php echo $this->lang_line('search_videos'); ?>" />
											<div class="videoplayer-search-reset">
	
											</div>
										</div>
									</div>
								</div>
	
							</div>
	
							<!-- /.videoplayer-search -->
	
							<!-- .videoplayer-title -->
	
							<h2 class="videoplayer-title-videos"><?php echo $this->lang_line('my_videos'); ?></h2>
	
							<h2 class="videoplayer-title-favorites"><?php echo $this->lang_line('favorites'); ?></h2>
							<h2 class="videoplayer-title-playlists"><?php echo $this->lang_line('playlists'); ?></h2>
	
							<!-- /.videoplayer-title -->
						</div>
					</div>
				</div>
			</div>
	
			<!-- .videoplayer-videos -->
	
			<div class="videoplayer-videos">
				<div class="videoplayer-videos-inject">
					<p class="videoplayer-videos-empty"><?php echo $this->lang_line('search_video'); ?></p>
				</div>
			</div>
	
			<!-- /.videoplayer-videos -->
	
			<div class="bottom">
				<div class="splitter-bottom">
					<div class="splitter-bottom-left">
						<div class="splitter-bottom-right">
						</div>
					</div>
				</div>
			</div>
		
		</div><!-- .old listings -->


	</div>

	<!-- /.videoplayer-listings -->



	<!-- .videoplayer-preview -->

	<div class="videoplayer-preview">

		<div class="top">
			<div class="splitter-top-bottom">
				<div class="splitter-top-left"></div>
			</div>
		</div>

		<div class="videoplayer-preview-inject"></div>


		<div class="bottom">
			<div class="splitter-bottom">
					<div class="splitter-bottom-left">

						<div class="videoplayer-controls">
							<div class="videoplayer-controls-in">
								<a class="videoplayer-submit videoplayer-btn"><?php echo $this->lang_line('select_video'); ?></a>
								<a class="videoplayer-cancel videoplayer-btn"><?php echo $this->lang_line('cancel'); ?></a>
								<div class="clear"></div>
							</div>
						</div>

					</div>

			</div>
		</div>
	</div>

	<!-- /.videoplayer-preview -->

</div>

<!-- /.videoplayer-box -->

</div>