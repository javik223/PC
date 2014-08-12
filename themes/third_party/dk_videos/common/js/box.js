/**
 * DK Videos
 *
 * @package		DK Videos
 * @version		Version 1.0b1
 * @author		Benjamin David
 * @copyright	Copyright (c) 2012 - DUKT
 * @link		http://dukt.net/dk-videos/
 *
 */

/*
var Dukt_video = {
	site_id : 0,
	ajax_endpoint : ajaxurl
};
*/


$.fn.spin = function(opts) {
  this.each(function() {
    var $this = $(this),
        data = $this.data();

    if (data.spinner) {
      data.spinner.stop();
      delete data.spinner;
    }
    if (opts !== false) {
      data.spinner = new Spinner($.extend({color: $this.css('color')}, opts)).spin(this);
    }
  });
  return this;
};


$(document).ready(function() {

	var opts = {
	  lines: 13, // The number of lines to draw
	  length: 4, // The length of each line
	  width: 2, // The line thickness
	  radius: 4, // The radius of the inner circle
	  corners: 1, // Corner roundness (0..1)
	  rotate: 0, // The rotation offset
	  color: '#000', // #rgb or #rrggbb
	  speed: 2.2, // Rounds per second
	  trail: 60, // Afterglow percentage
	  shadow: false, // Whether to render a shadow
	  hwaccel: false, // Whether to use hardware acceleration
	  className: 'spinner', // The CSS class to assign to the spinner
	  zIndex: 2e9, // The z-index (defaults to 2000000000)
	  top: '7px', // Top position relative to parent in px
	  left: '6px' // Left position relative to parent in px
	};

	$('.dk-videos-status .spin').spin(opts);
	
	var opts = {
	  lines: 13, // The number of lines to draw
	  length: 3, // The length of each line
	  width: 2, // The line thickness
	  radius: 4, // The radius of the inner circle
	  corners: 1, // Corner roundness (0..1)
	  rotate: 0, // The rotation offset
	  color: '#000', // #rgb or #rrggbb
	  speed: 2.2, // Rounds per second
	  trail: 60, // Afterglow percentage
	  shadow: false, // Whether to render a shadow
	  hwaccel: false, // Whether to use hardware acceleration
	  className: 'spinner', // The CSS class to assign to the spinner
	  zIndex: 2e9, // The z-index (defaults to 2000000000)
	  top: '0', // Top position relative to parent in px
	  left: '0' // Left position relative to parent in px
	};
	
	$('.dkv-search .spin').spin(opts);
/* 	console.log('spinner', $('.dkv-search .spin')); */
});


var dk_videos_box = {};

var dk_videos_ajax_stack = [];



jQuery(document).ready(function($) {
    
    // $() will work as an alias for jQuery() inside of this function

	
	dk_videos_box = {
		init: function()
		{
			console.log('boo');
			//dk_videos_box.box.init();
			dk_videos_box.lightbox.init();
		},
		
		box: false,
		
		utils: {
			current_service: function() {
				return $($('.videoplayer-accounts > ul > li.selected > a').get(0)).data('service');
			}
		}
	};
	
	dk_videos_box.lightbox = {
		init: function()
		{
			var overlay = $('<div class="videoplayer-overlay loading"><div class="spin"></div></div>');
			
			$('body').append(overlay);
			
			$('.videoplayer-overlay').live('click', function(){
				dk_videos_box.lightbox.hide();
				console.log('test');
			});
			
			var opts = {
			  lines: 13, // The number of lines to draw
			  length: 5, // The length of each line
			  width: 2, // The line thickness
			  radius: 5, // The radius of the inner circle
			  corners: 1, // Corner roundness (0..1)
			  rotate: 0, // The rotation offset
			  color: '#fff', // #rgb or #rrggbb
			  speed: 2.2, // Rounds per second
			  trail: 60, // Afterglow percentage
			  shadow: false, // Whether to render a shadow
			  hwaccel: false, // Whether to use hardware acceleration
			  className: 'spinner', // The CSS class to assign to the spinner
			  zIndex: 2e9, // The z-index (defaults to 2000000000)
			  top: '0', // Top position relative to parent in px
			  left: '0' // Left position relative to parent in px
			};
		
			$('.videoplayer-overlay .spin').spin(opts);
			
			dk_videos_box.lightbox.resize();
			
			$(window).resize(function() {		
				dk_videos_box.lightbox.resize();	
			});
		},
		
		show: function()
		{
			$('.videoplayer-overlay').css('display', 'block');
			$('.dukt-videos-wrapper').css('display', 'block');
			
			$('.videoplayer-videos li').each(function() {
				if($(this).hasClass('selected'))
				{
					var data = {
						method : 'box_preview',
						service : dk_videos_box.utils.current_service(),
						site_id : Dk_videos.site_id,
						video_page : $(this).data('video-page'),
						autoplay : 0,
						rel : 0
					}
			
					$('.videoplayer-preview').data('video-page', $(this).data('video-page'));
			
					$('.videoplayer-controls').css('display', 'block');
			
					dk_videos_box.browser.go(data, 'preview', function() {
			
						// init favorite button
						
						$('.videoplayer-controls').css('display', 'block');
						
						dk_videos_box.box.resize();
					});
				}
			});
		},
		
		hide: function()
		{
			$('.videoplayer-overlay').css('display', 'none');
			$('.dukt-videos-wrapper').css('display', 'none');
			$('.videoplayer-preview-inject').html('');
			
			dk_videos_box.browser.abort('preview');
			
		},
		
		resize: function() {
		
			var winW = $(window).width();
			var winH = $(window).height();
			
			var boxW = $('.dukt-videos-wrapper').outerWidth();
			var boxH = $('.dukt-videos-wrapper').outerHeight();
			
			var boxX = (winW - boxW) / 2;
			var boxY = (winH - boxH) / 2;
	
			$('.dukt-videos-wrapper').css({
				'left': boxX,
				'top': boxY
			});
		
			$('.videoplayer-overlay').css({
				'width' : winW,
				'height' : winH
			});
			
		}
	};
	
	dk_videos_box.ajax_stack = {
		init: function() {
			$('.dk-videos-status .reload').live('click', function() {
				console.log('reload all');
				
				$('.videoplayer-accounts ul ul > li > a').each(function(i, el)
				{
					
					var listing = $(el).data('listing');
					var service = $(el).data('service');
					var method = $(el).data('method');
							
					data = {
						'method': 	method,
						'site_id': 	Dk_videos.site_id,
						'service':	service
					};
				
					var listing_view = false;
				
					$('.dkv-listing-view').each(function(i, el) {
						if($(el).data('listing') == listing && $(el).data('service') == service)
						{
							listing_view = $(el);
						}
					});
					
					// add request to stack
					
					var k = $(el).data('service') + $(el).data('listing');
				
					dk_videos_box.ajax_stack.add(k, function() {
						return $.ajax({
							url: Dk_videos.ajax_endpoint,
							type:"post",
							data : data,
							
							beforeSend:function()
							{
								$('.dkv-videos', listing_view).addClass('dkv-loading');
							},
							
							success: function( data ) {
								
								// remove request from stack
				
								dk_videos_box.ajax_stack.remove(k);
								
								$('.dkv-videos-inject', listing_view).html(data);
								
								// $('.dkv-videos-empty', listing_view).css('display', 'none');
							}
						});
					});
					
				});
			});
		},
		
		add: function(k, callback)
		{
			console.log('add', k);	
			dk_videos_ajax_stack[k] = callback();
			
			dk_videos_box.ajax_stack.updateStatus();
		},
		
		remove: function(k)
		{
			console.log('remove', k);
			
			//dk_videos_ajax_stack.splice(k, 1);
			
			for(key in dk_videos_ajax_stack)
			{
				if(key == k)
				{
					delete dk_videos_ajax_stack[key];	
				}
			}
			
			dk_videos_box.ajax_stack.updateStatus();
		},
		
		updateStatus: function()
		{
			if(dk_videos_box.ajax_stack.count() > 0)
			{
				$('.dk-videos-status').addClass('loading');
			}
			else
			{
				$('.dk-videos-status').removeClass('loading');
			}
		},
		
		count: function()
		{
			var i = 0;
			
			for(key in dk_videos_ajax_stack)
			{
				i++;
			}
			
			return i;
		}
	};
	
	dk_videos_box.listings = {
		reload: function(listing, service, method)
		{
			console.log('reloadx', listing, service, method);	
			
			var k = service + listing;

			dk_videos_box.ajax_stack.add(k, function() {
				data = {
					'method': 	method,
					'site_id': 	Dk_videos.site_id,
					'service':	service
				};
				
				var listing_view = false;
		
				$('.dkv-listing-view').each(function(i, el) {
					if($(el).data('listing') == listing && $(el).data('service') == service)
					{
						listing_view = $(el);
					}
				});

			
				return $.ajax({
					url: Dk_videos.ajax_endpoint,
					type:"post",
					data : data,
					
					beforeSend:function()
					{
/*
						$('.dkv-videos', listing_view).addClass('dkv-loading');
						$('.dkv-videos-empty', listing_view).css('display', 'none');
*/
					},
					
					success: function( data ) {
						
						// remove request from stack
	
						dk_videos_box.ajax_stack.remove(k);
						
						$('.dkv-videos-inject', listing_view).html(data);
					}
				});
			});
		}
	};
	
	dk_videos_box.box = {
		
		init: function(callback)
		{
			dk_videos_box.box.resize();
			dk_videos_box.box.accounts();
			dk_videos_box.box.videos();
			dk_videos_box.search.init();
			dk_videos_box.ajax_stack.init();
	
			$(window).resize(function() {
				dk_videos_box.box.resize();
			});
					
			var opts = {
			  lines: 13, // The number of lines to draw
			  length: 4, // The length of each line
			  width: 2, // The line thickness
			  radius: 4, // The radius of the inner circle
			  corners: 1, // Corner roundness (0..1)
			  rotate: 0, // The rotation offset
			  color: '#000', // #rgb or #rrggbb
			  speed: 2.2, // Rounds per second
			  trail: 60, // Afterglow percentage
			  shadow: false, // Whether to render a shadow
			  hwaccel: false, // Whether to use hardware acceleration
			  className: 'spinner', // The CSS class to assign to the spinner
			  zIndex: 2e9, // The z-index (defaults to 2000000000)
			  top: '7px', // Top position relative to parent in px
			  left: '6px' // Left position relative to parent in px
			};
		
			$('.dk-videos-status .spin').spin(opts);
			
			if(typeof(callback) !== 'undefined')
			{
				callback();
			}
		},
	
		
		resize : function()
		{
			var winH = $('.videoplayer-box').parent().outerHeight();
			var winW = $(window).width();
			
			// var headH = $('#head').outerHeight();
			var headH = 0;
			
			var topH = $('.splitter-top-left').outerHeight();
			var bottomH = $('.splitter-bottom-left').outerHeight();
			
			// var previewPlayerH = $('.videoplayer-preview-video').outerHeight();
			
			var previewPlayerW = $('.videoplayer-preview-video').outerWidth();
			
			var hdW = 1280;
			var hdH = 720;
			
			var previewPlayerH = previewPlayerW * hdH / hdW; // hard set because the div doesn't exists before a video is launched
			
			previewPlayerH = Math.round(previewPlayerH);
			
			
			
			var commonH = winH - headH;
			console.log(commonH);
			
			var previewInjectH = commonH - topH - bottomH;
			var previewDescriptionH = commonH - topH - previewPlayerH - bottomH; 
			
			var previewPlayerPercentH =  previewPlayerH * 100 / (previewPlayerH + previewDescriptionH);
			
			console.log('percent', previewPlayerPercentH);
			
			if(previewPlayerPercentH > 60)
			{
				previewPlayerH = 60 * (previewPlayerH + previewDescriptionH) / 100;
				previewPlayerH = Math.round(previewPlayerH);
			}
			
			// recalculate description
			
			previewDescriptionH = commonH - topH - previewPlayerH - bottomH; 
			


			var fullscreenH = commonH - topH - bottomH;
			
			
			console.log(commonH, topH, previewPlayerH, bottomH);
			console.log(previewDescriptionH);
	
			$('.videoplayer-box').css('height', commonH);
			$('.videoplayer-accounts').css('height', commonH);
			$('.videoplayer-listings').css('height', commonH);
			$('.videoplayer-preview').css('height', commonH);
			$('.videoplayer-preview-video, .videoplayer-preview-video iframe').css('height', previewPlayerH);
			
			$('.videoplayer-fullscreen .videoplayer-preview-video, .videoplayer-fullscreen .videoplayer-preview-video iframe').css('height', fullscreenH);
			
			$('.videoplayer-preview-inject').css('height', previewInjectH);
			$('.videoplayer-preview-description').css('height', previewDescriptionH);
			$('.dkv-videos').css('height', previewInjectH);


			dk_videos_box.lightbox.resize();
		}
	};
	
	
	
	
	dk_videos_box.box.accounts = function()
	{

		$('.videoplayer-close').live('click', function(){
	
			dk_videos_box.lightbox.hide();
	
		});
	
		// services
	
		$('.videoplayer-accounts > ul > li > a').live('click', function() {
	
			if($(this).parent().hasClass('selected'))
			{
				return false;
			}
	
	
			var el = $(this);
	
			current_method = $('.videoplayer-accounts ul ul > li a.selected').data('method');
	
			$('.videoplayer-accounts > ul > li').removeClass('selected');
			$('.videoplayer-accounts ul ul > li a').removeClass('selected');
	
			$(this).parent().addClass('selected');
	
			method_found = false;
	
			$(this).parent().find('ul > li a').each(function() {
				if($(this).data('method') == current_method)
				{
					method_found = true;
					$(this).trigger('click');
	
				}
			});
	
			if(!method_found)
			{
				$(this).parent().find('ul > li:first-child a').addClass('selected');
	
				q = $($('.dkv-search input')[0]).attr('value');
	
	
				// ajax browse to account
				
				var data = {
					method: 'service_search',
					service:dk_videos_box.utils.current_service(),
					site_id: Dk_videos.site_id,
					q: q
				}
	
				dk_videos_box.browser.go(data, 'videos');
			}
	
			$('.videoplayer-accounts > ul > li').each(function() {
				if(!el.hasClass('selected'))
				{
					$(this).parent().find('ul').slideUp({easing:'easeOutCubic', duration:400});
				}
			});
	
			$(this).parent().find('ul').slideDown({easing:'easeOutCubic', duration:400});
		});
	
	
		// services listings init ajax calls
		
		$('.videoplayer-accounts ul ul > li > a').each(function(i, el)
		{
			
			var listing = $(el).data('listing');
			var service = $(el).data('service');
			var method = $(el).data('method');
					
			data = {
				'method': 	method,
				'site_id': 	Dk_videos.site_id,
				'service':	service
			};
	
			var listing_view = false;
	
			$('.dkv-listing-view').each(function(i, el) {
				if($(el).data('listing') == listing && $(el).data('service') == service)
				{
					listing_view = $(el);
				}
			});
			
			// add request to stack
			
			var k = $(el).data('service') + $(el).data('listing');

			dk_videos_box.ajax_stack.add(k, function() {
				return $.ajax({
					url: Dk_videos.ajax_endpoint,
					type:"post",
					data : data,
					
					beforeSend:function()
					{
						$('.dkv-videos', listing_view).addClass('dkv-loading');
						// $('.dkv-videos-empty', listing_view).css('display', 'none');
					},
					
					success: function( data ) {
						
						// remove request from stack
	
						dk_videos_box.ajax_stack.remove(k);
						
						$('.dkv-videos-inject', listing_view).html(data);
					}
				});
			});
			
		});
		
		
		// clicking a listing option
	
		$('.videoplayer-accounts ul ul > li > a').live('click', function() {
			
			// selected button
			
			if($(this).hasClass('selected'))
			{
				var listing = $(this).data('listing');
				var service = $(this).data('service');
				var method = $(this).data('method');
				
				dk_videos_box.listings.reload(listing, service, method);
			}
			
			$('.videoplayer-accounts ul ul > li > a').removeClass('selected');
			$(this).addClass('selected');
			
			
			var listing = $(this).data('listing');
			var service = $(this).data('service');
			var method = $(this).data('method');
			
			var listing_view = false;
			
			$('.dkv-listing-view').css('display', 'none');
			
			$('.dkv-listing-view').each(function(i, el) {
				if($(el).data('listing') == listing && $(el).data('service') == service)
				{
					listing_view = $(el);
					listing_view.css('display', 'block');
				}
			});
			
			/*
			$('.videoplayer-accounts ul ul > li > a').removeClass('selected');
			
			$(this).addClass('selected');
			
			if(!$(this).hasClass('videoplayer-service-search')) {
				dk_videos_box.search.textFocus = false;
				// console.log('focusout');
			}
			
			if($(this).hasClass('videoplayer-service-search'))
			{
				$('.videoplayer-title-videos').css('display', 'none');
				$('.videoplayer-title-favorites').css('display', 'none');
			
				$('.videoplayer-search').css('display', 'block');
				
				$($('.videoplayer-search input')[0]).focus();
				
				dk_videos_box.search.textFocus = true;
				
				// console.log('focusin3');
			}
			else if($(this).hasClass('videoplayer-service-videos'))
			{
				$('.videoplayer-search').css('display', 'none');
				$('.videoplayer-title-favorites').css('display', 'none');
			
				$('.videoplayer-title-videos').css('display', 'block');
			}
			else if($(this).hasClass('videoplayer-service-favorites'))
			{
				$('.videoplayer-search').css('display', 'none');
				$('.videoplayer-title-videos').css('display', 'none');
			
				$('.videoplayer-title-favorites').css('display', 'block');
			}
			
			q = $('.videoplayer-search input')[0];
			q = $(q).attr('value');
			
			//q = q.trim();
			
			method = $(this).data('method');
			
			//console.log(q, method);
			
			data = {
				'method': method,
				'q':  q,
				'site_id': Dk_videos.site_id,
				'service':$(this).parent().parent().parent().find('> a').data('service')
			};
			
			dk_videos_box.browser.go(data, 'videos');
			*/
		});
	
	
		// fire out some stuff at init
		
		// hide submenus
		
		$('.videoplayer-accounts li ul').css('display', 'none');
		
		$('.videoplayer-services > li:first-child > a').trigger('click');
	
		$('.videoplayer-accounts > ul > li').each(function() {
			if($(this).hasClass('selected'))
			{
				$(this).find('ul').slideDown({easing:'easeOutCubic', duration:400});
				$(this).find('ul li:first-child a').trigger('click');
			}
		});
	
	};
	
	
	
	
	dk_videos_box.box.videos = function() {
		// clickable video list
	
		$('.dkv-videos li').live('click', function() {
	
			if($(this).hasClass('videoplayer-videos-more'))
			{
				return false;
			}
	
			$('.videoplayer-preview-inject').html('');
	
			$('.dkv-videos li').removeClass('selected');
	
			$(this).addClass('selected');
	
	
			// ajax
	
			var data = {
				method : 'box_preview',
				service : dk_videos_box.utils.current_service(),
				site_id : Dk_videos.site_id,
				video_page : $(this).data('video-page'),
				autoplay : 1,
				rel : 0
			}
	
			$('.videoplayer-preview').data('video-page', $(this).data('video-page'));
	
			$('.videoplayer-controls').css('display', 'block');
	
			dk_videos_box.browser.go(data, 'preview', function() {
	
				// init favorite button
				
				$('.videoplayer-controls').css('display', 'block');
				
				dk_videos_box.box.resize();
			});
		});
	
	
		// set as favorite

		$('.videoplayer-preview-favorite').live('click', function() {

			favorite_enabled = false;
			if($(this).hasClass('videoplayer-preview-favorite-selected'))
			{
			  	$(this).removeClass('videoplayer-preview-favorite-selected');
			}
			else
			{
			  	$(this).addClass('videoplayer-preview-favorite-selected');
			  	favorite_enabled = true;
			}
			
			var service = $(this).data('service');
			console.log('--service', service);	
			// ajax browse to account
			var data = {
				action: 'dukt_videos',
				method: 'favorite',
				service:service,
				site_id: Dk_videos.site_id,
				video_page:$('.videoplayer-preview').data('video-page'),
				favorite_enabled:favorite_enabled
			}
	

			
			var method = 'service_favorites';
			
			$.ajax({
			  url: Dk_videos.ajax_endpoint,
			  type:"post",
			  data : data,
			  success: function( data ) {
			  	//console.log('favorite success');
			  	dk_videos_box.listings.reload('favorites', service, method);
	
			  }
			});
	
		});
	
	
		// fullscreen mode
	
		$('.videoplayer-preview-fullscreen').live('click', function() {
			if($('.videoplayer-box').hasClass('videoplayer-fullscreen'))
			{
				$('.videoplayer-box').removeClass('videoplayer-fullscreen');

				dk_videos_box.box.resize();
			}
			else
			{
				$('.videoplayer-box').addClass('videoplayer-fullscreen');
				
				dk_videos_box.box.resize();
			}
		});
	
	
		// load more video when scrolled to absolute bottom
	
		$('.videoplayer-videos-more').live('click', function() {
	
			if($(this).find('.videoplayer-videos-more-btn').css('display') != "none")
			{
				$(this).find('.videoplayer-videos-more-btn').css('display', 'none');
				$(this).find('.videoplayer-videos-more-loading').css('display', 'inline');
	
				var q = $('.dkv-search input').attr('value');
	
				var data = {
					action: 'dukt_videos',
					method: $('.videoplayer-services > li.selected li a.selected').data('method'),
					service:dk_videos_box.utils.current_service(),
					site_id: Dk_videos.site_id,
					q: q,
					page: $(this).data('next-page')
				};
	
	
				//$.fn.videoplayer.browser.go(data, 'videos');
	
				$.ajax({
				  url: Dk_videos.ajax_endpoint,
				  type:"post",
				  data : data,
				  beforeSend:function()
				  {
				  },
				  success: function( data ) {
	
					$('.videoplayer-videos-more').remove();
	
					var html_before = $('.videoplayer-videos ul').html();
	
					var html = html_before + data;
	
					$('.videoplayer-videos ul').html(html);
					//$('.videoplayer-videos ul').append('<li>Next page loaded</li>');
				  }
				});
			}
	
			//console.log('load more videos');
		});
	
		$($('.videoplayer-videos').get(0)).scroll(function(eventData) {
			scrollDifference = $(this).get(0).scrollHeight - $(this).scrollTop();
	
			if(scrollDifference == $(this).height())
			{
				$('.videoplayer-videos-more').trigger('click');
			}
		});
	};
	
	
	
	
	
	
	
	
	
	
	
	
	
	dk_videos_box.browser = {
	
		current_request : [],
	
		go:function(data, frame, callback)
		{
			/*
			if(typeof(dk_videos_box.browser.current_request[frame]) != "undefined")
			{
				if(dk_videos_box.browser.current_request[frame] != false)
				{
					dk_videos_box.browser.current_request[frame].abort();
			
					dk_videos_box.browser.current_request[frame] = false;
				}
			}
			else
			{
				dk_videos_box.browser.current_request[frame] = false;
			}
			*/
		
			data['action'] = 'dukt_videos';
			
			dk_videos_box.browser.current_request[frame] = $.ajax({
			  url: Dk_videos.ajax_endpoint,
			  type:"post",
			  data : data,
			  beforeSend:function()
			  {
			  	$('.videoplayer-'+frame).addClass('videoplayer-frame-loading');
				// $('.videoplayer-videos-empty').css('display', 'none');
			  },
			  success: function( data ) {

				  	dk_videos_box.browser.current_request[frame] = false;
			
					$('.videoplayer-'+frame+'-inject').html(data);
				  	$('.videoplayer-'+frame).removeClass('videoplayer-frame-loading');
	  				$('.dkv-search').removeClass('loading');
			
				  	//console.log(dk_videos_box.search.timer);
					if(dk_videos_box.search.timer){
						clearTimeout(dk_videos_box.search.timer);
						dk_videos_box.search.timer = false;
				  		//console.log("timer clear");
					}
			
			  		if(typeof(callback) == "function")
			  		{
			  			callback();
			  		}
			  }
			});
		},
		
		abort:function(frame)
		{
			console.log("abort");
			
			if(typeof(dk_videos_box.browser.current_request[frame]) != "undefined")
			{
				if(dk_videos_box.browser.current_request[frame] != false)
				{
					dk_videos_box.browser.current_request[frame].abort();
	
					dk_videos_box.browser.current_request[frame] = false;
				}
			}
			else
			{
				dk_videos_box.browser.current_request[frame] = false;
			}
		}
	};
	
	
	/**
	* Search
	*
	*/
	dk_videos_box.search = {
	
		textFocus: false,
	
		init:function()
		{
			var opts = {
			  lines: 13, // The number of lines to draw
			  length: 3, // The length of each line
			  width: 2, // The line thickness
			  radius: 4, // The radius of the inner circle
			  corners: 1, // Corner roundness (0..1)
			  rotate: 0, // The rotation offset
			  color: '#000', // #rgb or #rrggbb
			  speed: 2.2, // Rounds per second
			  trail: 60, // Afterglow percentage
			  shadow: false, // Whether to render a shadow
			  hwaccel: false, // Whether to use hardware acceleration
			  className: 'spinner', // The CSS class to assign to the spinner
			  zIndex: 2e9, // The z-index (defaults to 2000000000)
			  top: '0', // Top position relative to parent in px
			  left: '0' // Left position relative to parent in px
			};
			
			$('.dkv-search .spin').spin(opts);

			// search reset
	
			$('.videoplayer-search-reset').live('click', function(){
				$('.dkv-search input').attr('value', '');
				$(this).css('display', 'none');
				$('.dkv-search input').trigger('keyup');
			});
	
	
			// live key watcher
		
			dk_videos_box.search.timer = false;
		
			var abort = false;
	
			$('.dkv-search input').live('keydown', function(e) {
	
			//console.log('down'+ e.keyCode);
	
				if(e.keyCode == 91) //command
				{
					abort=true;
				}
	
			}).live('keyup',
			
			function(e) {

				$('.videoplayer-accounts > ul > li.selected ul li a').removeClass('selected');
				$('.videoplayer-accounts > ul > li.selected ul li:first-child a').addClass('selected');
	
				//console.log($.fn.videoplayer.search.timer);
	
				var el = $(this);
				var q = el.attr('value');
	
				if(q !== "")
				{
					$('.videoplayer-search-reset').css('display', 'block');
				}
				else
				{
					$('.videoplayer-search-reset').css('display', 'none');
				}
	
				//console.log('up'+ e.keyCode);
	
				if(abort == true && e.keyCode != 91)
				{
					abort = false;
					return false;
				}
	
	
				switch(e.keyCode)
				{
					case 91: // command
						abort = false;
						return false;
					break;
	
					case 18: // alt
					case 16: // shift
					case 37:
					case 38:
					case 39:
					case 40:
						return false;
					break;
				}
	
	
				if(dk_videos_box.search.timer)
				{
					clearTimeout(dk_videos_box.search.timer);
				}
	
				if(!dk_videos_box.search.timer)
				{
	  				$('.dkv-search').addClass('loading');
/* 	  				$('.videoplayer-videos-inject').addClass('videoplayer-frame-loading'); */
					/* $('.videoplayer-videos-empty').css('display', 'none'); */
				}
	
				// listing view
					
				var listing = $(this).data('listing');
				var service = $(this).data('service');
		
				var listing_view = false;
		
				$('.dkv-listing-view').each(function(i, el) {
					if($(el).data('listing') == listing && $(el).data('service') == service)
					{
						listing_view = $(el);
					}
				});
				
				console.log(this, listing, service);
	
				dk_videos_box.search.timer = setTimeout(function() {
	
					var data = {
						method: 'service_search',
						service:dk_videos_box.utils.current_service(),
						site_id: Dk_videos.site_id,
						q: q
					};
	
					// dk_videos_box.browser.go(data, 'videos');
							
					$.ajax({
						url: Dk_videos.ajax_endpoint,
						type:"post",
						data : data,
						
						beforeSend:function()
						{
			  				$('.dkv-search').addClass('loading');
							$('.dkv-videos', listing_view).addClass('dkv-loading');
							// $('.dkv-videos-empty', listing_view).css('display', 'none');
						},
						
						success: function( data ) {
							$('.dkv-videos-inject', listing_view).html(data);
							$('.dkv-search').removeClass('loading');							
						}
					});
	
				}, 500);
			});
		}
	};
	
		$(document).ready(function() {
		dk_videos_box.init();
	});
	

});