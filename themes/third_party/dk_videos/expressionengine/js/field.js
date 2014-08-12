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

(function($) {

  	// plugin definition

	$.fn.dk_videos_field = function(options)
	{		
		// build main options before element iteration
		// iterate and reformat each matched element

		return this.each(
			function()
			{
				field = $(this);

				$.fn.dk_videos_field.init_field(field);
			}
		);
	};
	
	$.fn.dk_videos_field.current_field = false;
	
	$.fn.dk_videos_field.init = function()
	{
		console.log('track initx');
		
		// load box
	
		var data = {
			method: 'box',
			site_id: Dk_videos.site_id,
		};
		
		$.ajax({
			url: Dk_videos.ajax_endpoint,
			type:"post",
			data : data,
			success: function(data)
			{
				$('body').append(data);
			
				// init dk videos box
				
				dk_videos_box.box.init(function() {
					if($('.videoplayer-overlay').css('display') != 'none')
					{
						dk_videos_box.lightbox.show();
					}
				});
			}
		});
		

		// cancel
		
		$('.videoplayer-cancel').live('click', function() {
			dk_videos_box.lightbox.hide();
		});
		
		
		// submit
		
		$('.videoplayer-submit').live('click', function() {
			var field = $.fn.dk_videos_field.current_field;
			var video_url = $('.dukt-videos-current').data('video-url');
			
			$('input', field).attr('value', video_url);
			
			dk_videos_box.lightbox.hide();
			
			$.fn.dk_videos_field.callback_add();
		});
		
		
  		// matrix compatibility

  		if(typeof(Matrix) != "undefined")
  		{
			Matrix.bind("dk_videos", "display", function(cell) {

				// we remove event triggers because they are all going to be redefined
				// will be improved with single field initialization

				if (cell.row.isNew)
				{
					var field = $('> .videoplayer-field', cell.dom.$td);

					$.fn.dk_videos_field.init_field(field);
				}
			});
		}		
	};
	
	
	$.fn.dk_videos_field.init_field = function(field)
	{
		inputValue = $('input', field).attr('value');

		if(inputValue != "")
		{
			field.find('.preview').html('');
			field.find('.preview').css('display', 'block');
			field.find('.preview').addClass('videoplayer-field-preview-loading');

			video_page = inputValue;

			data = {
				'method': 'field_preview',
				'video_page': video_page,
				'site_id': Dk_videos.site_id
			};

			$('input[type="hidden"]', field).attr('value', video_page);

			$.ajax({
			  url: Dk_videos.ajax_endpoint,
			  type:"post",
			  data : data,
			  success: function(data)
			  {
		  		field.find('.preview').html(data);
				field.find('.preview').removeClass('videoplayer-field-preview-loading');
			  }
			});

			$('.change', field).css('display', 'inline-block');
			$('.remove', field).css('display', 'inline-block');
		}
		else
		{
			$('.add', field).css('display', 'inline-block');
		}

		$('.add', field).click(function(){
			$.fn.dk_videos_field.add(field);
		});
	
	
		$('.change', field).click(function(){
			$.fn.dk_videos_field.change(field);
		});
	
		$('.remove', field).click(function(){
			$.fn.dk_videos_field.remove(field);
		});
	
		$('.videoplayer-field-embed-btn').live('click', function() {
			$('.videoplayer-overlay').css('display', 'block');
			$('.videoplayer-overlay').addClass('videoplayer-overlay-loading');
	
			data = {
				'method': $(this).data('method'),
				'video_page': $(this).data('video-page'),
				'site_id': VideoPlayer.site_id
			};
	
			$.ajax({
			  url: VideoPlayer.ajax_endpoint,
			  type:"post",
			  data : data,
			  success: function( data ) {
	
		  		$('body').append(data);
	
				$('.videoplayer-overlay').removeClass('videoplayer-overlay-loading');
				$.fn.dk_videos_field.lightbox.resize();
	
			  }
			});
		});
	
	};
	
	$.fn.dk_videos_field.callback_add = function()
	{
		field = $.fn.dk_videos_field.current_field;
	
		field.find('.add').css('display', 'none');
		field.find('.change').css('display', 'inline-block');
		field.find('.remove').css('display', 'inline-block');
		field.find('.preview').html('');
		field.find('.preview').css('display', 'block');
		field.find('.preview').addClass('videoplayer-field-preview-loading');
	
			video_page = $('.videoplayer-preview').data('video-page');
	
		data = {
			'method': 'field_preview',
			'video_page': video_page,
			'site_id': Dk_videos.site_id
		};
	
			$('input[type="hidden"]', field).attr('value', video_page);
	
		$.ajax({
		  url: Dk_videos.ajax_endpoint,
		  type:"post",
		  data : data,
		  success: function( data ) {
	
			//console.log('after ajax');
	
	  		field.find('.preview').html(data);
			field.find('.preview').removeClass('videoplayer-field-preview-loading');
		  }
		});
	};
	
		
	$.fn.dk_videos_field.add = function(field)
	{
		$.fn.dk_videos_field.current_field = field;
		
		dk_videos_box.lightbox.show();
		
		//$.fn.dk_videos_field.open();
	};
	
	$.fn.dk_videos_field.change = function(field)
	{
		$.fn.dk_videos_field.current_field = field;
		dk_videos_box.lightbox.show();
		
		// video page
		
		var video_page = field.find('input').attr('value');
		var current_service = $('.videoplayer-services li.selected a.videoplayer-service').data('service');
		
		// ajax browse to account
		
		var data = {
			method: 'box_preview',
			service: current_service,
			site_id: Dk_videos.site_id,
			video_page: video_page,
			autoplay: 0
		}
		
		$('.videoplayer-preview').data('video-page', video_page);
		
		
		dk_videos_box.browser.go(data, 'preview', function() {
		$('.videoplayer-controls').css('display', 'block');
		});
	};
	
	$.fn.dk_videos_field.remove = function(field)
	{
		dk_videos_box.lightbox.hide();
		
		field.find('input').attr('value', '');
		
		field.find('.add').css('display', 'inline-block');
		field.find('.change').css('display', 'none');
		field.find('.remove').css('display', 'none');
		field.find('.preview').css('display', 'none');
	};


	// Initialization

	$(document).ready(function() {
		$.fn.dk_videos_field.init();
	});


})(jQuery);

$().ready(function()
{
	$('.videoplayer-field').dk_videos_field();
});

/* End of file videoplayer.field.js */
/* Location: ./themes/third_party/videoplayer/js/videoplayer.field.js */