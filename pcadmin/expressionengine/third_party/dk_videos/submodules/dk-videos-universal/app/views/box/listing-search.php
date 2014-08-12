<div class="dkv-listing-view" data-service="<?=$service->service_key?>" data-listing="search">

	<div class="top">
		<div class="splitter-top-bottom">
			<div class="splitter-top-left">
				<div class="splitter-top-right">
					<!-- .videoplayer-search -->
					<div class="dkv-search">
						<div class="search-bg">
							<div class="search-left">
								<div class="search-right">
									<input type="text" data-service="<?=$service->service_key?>" data-listing="search" placeholder="<?php echo $this->lang_line('search_videos'); ?>" />
									<div class="videoplayer-search-reset"></div>
									<div class="spin"></div>
								</div>
							</div>
						</div>
					</div>
					<!-- /.videoplayer-search -->
				</div>
			</div>
		</div>
	</div>

	<div class="dkv-videos">
		<div class="dkv-videos-inject">
			<p class="dkv-videos-empty"><?php echo $this->lang_line('no_videos'); ?></p>
		</div>
	</div>

	<div class="bottom">
		<div class="splitter-bottom">
			<div class="splitter-bottom-left">
				<div class="splitter-bottom-right">
				</div>
			</div>
		</div>
	</div>
</div>