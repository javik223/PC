<?php

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

require_once PATH_THIRD.'dk_videos/config.php';

$lang = array(

	// videoplayer
	
	'dk_videos_module_name' 			=> DK_VIDEOS_NAME,
	'dk_videos_module_description' 		=> 'Manage YouTube and Vimeo videos', 
	'license_key' 						=> "License Key",
	
	
	// endpoint
	
	'error_occured' => "An error occurred",
	'cant_access_videoplayer' => "Can't access Video Player",
	
	
	// mcp.videoplayer
	
	'account_setup_success' => "Your account has been setup successfully",
	'api_success' => "API has been setup with success",
	'api_failure' => "Wrong API key or secret",
	
	
	// views
		
		// box/box
		
		'close' => "Close",
		'search' => "Search",
		'videos' => "Videos",
		'favorites' => "Favorites",
		'configure' => "Configure",
		'my_videos' => "My Videos",
		'search_video' => "Search a video",
		'select_video' => "Select",
		'cancel' => "Cancel",
		
		
		// box/preview
		
		'fullscreen' => "Fullscreen",
		'add_favorite' => "Add favorite",
		'no_description' => "No description",
		
		
		// box/videos
		
		'from' => "from",
		'plays' => "plays",
		'date' => "Date",
		'load_more_videos' => "Load more videos",
		'loading_videos' => "Loading videos",
		'search_vimeo_videos' => "Search Vimeo videos",
		'search_youtube_videos' => "Search YouTube videos",
		'no_videos' => "No videos",
		
		
		// field/field
		
		'add_video' => "Add video",
		'change_video' => "Change",
		'remove_video' => "Remove",
		'videoplayer_disabled' => "Video Player is disabled because no video service is setup.",
		
		
		// mcp/configure.api
		
		'configure_api' => "Configure API",
		'no_youtube_key' => "Don't have a YouTube Developer Key",
		'no_vimeo_key' => "Don't have a Vimeo API key",
		'register_one' => "Register One",
		'continue' => "Continue",
		
		
		// mcp/configure.authsub
		// mcp/configure.oauth
		
		'connect_your_ee_to' => "You need to connect your ExpressionEngine website to",
		'connect_to' => "Connect to",
		
		
		// mcp/configure
		
		'is_configured' => "is configured",
		'display_name' => "Display Name",
		'username' => "Username",
		'you_may_want_to' => "You may want to ",
		'disconnect' => "disconnect",
		'if_another_account' => "if you want to link your site to another account",
		'api_configuration' => "API configuration",
		
		
		// mcp/index
		
		'setup_video_services' => "Setup video services",
		'disable' => "Disable",
		'enable' => "Enable",	
);
