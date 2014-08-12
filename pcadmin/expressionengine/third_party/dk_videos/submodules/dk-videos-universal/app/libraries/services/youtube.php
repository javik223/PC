<?

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

class Dukt_video_youtube extends Dukt_video_service {

	var $enabled						= true;
	var $is_authenticated 				= true;
	var $service_key					= "youtube";
	var $service_name 					= "YouTube";

	var $client_id 						= false;
	var $client_secret 					= false;
	var $universal_url 					= "http://www.youtube.com/embed/%s?wmode=transparent";
	var $oauth_redirect_uri 			= false;
	var $oauth_authorization_endpoint 	= 'https://accounts.google.com/o/oauth2/auth';
	var $oauth_token_endpoint 			= 'https://accounts.google.com/o/oauth2/token';

	var $api_options = array(
		'client_id' => false,
		'client_secret' => false,
		'developer_key' => false
	);
	
	var $token_options = array(
		'token' => false
	);
	
	var $embed_options 		= array(
		'width'				=> 500,
		'height'			=> 300,
		'autoplay' 			=> 0,
		'autohide' 			=> 0,
		'cc_load_policy' 	=> 0,
		'color'				=> 'red',
		'controls'			=> 1,
		'disablekb'			=> 0,
		'enablejsapi'		=> 0,
		'end'				=> false,
		'fs'				=> 0,
		'iv_load_policy'	=> 3,
		'loop'				=> 0,
		'modestbranding'	=> 0,
		'playerapiid'		=> false,
		'rel'				=> 1,
		'showinfo'			=> 1,
		'start'				=> false,
		'theme'				=> 'dark'
	);
	
	// --------------------------------------------------------------------
	
	function _getApiObject()
	{		
		$client_id 		= $this->options['client_id'];
		$client_secret 	= $this->options['client_secret'];
		$refresh_token 	= $this->options['token'];
		
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/Client.php');
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/GrantType/IGrantType.php');
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/GrantType/AuthorizationCode.php');
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/GrantType/RefreshToken.php');
		
		$client = new OAuth2\Client($client_id, $client_secret);
	
		$params = array('refresh_token' => $refresh_token);
		
	    $response = $client->getAccessToken($this->oauth_token_endpoint, 'refresh_token', $params);
	    
	    $info = $response['result'];
	    
	    if(!isset($info['access_token']))
	    {
		    return false;
	    }

	    $client->setAccessToken($info['access_token']);
	    
	    return $client;
	}
	
	// --------------------------------------------------------------------
	
	function is_authenticated()
	{
		if($this->options['token'] == false)
		{
			return false;
		}
		
		try {
			$api = $this->_getApiObject();
			
			if(!$api)
			{
				return false;
			}
			
			$url = 'https://gdata.youtube.com/feeds/api/users/default/favorites';
			
		    $response = $api->fetch($url);
		    
		    $result = $response['result'];
		    
		    $xml_obj = simplexml_load_string($result);
		    
		    return true;
	    }
		catch(Exception $e)
		{
			return false;
		}	
	}
	
	// --------------------------------------------------------------------
	
	function search($q, $page, $per_page)
	{	
		$api = $this->_getApiObject();
		
		$url = 'http://gdata.youtube.com/feeds/api/videos';
		
		$start_index = (($page - 1) * $per_page) + 1;
		$max_results = $per_page;
		
		$query = array(
			'q' => $q,
			'start-index' => $start_index,
			'max-results' => $max_results,
			'v' => 2
		);
		
		$query = "?".http_build_query($query);
	    
	    $xml_obj = simplexml_load_file($url.$query);
    
	    $videos = array();
	    
	    foreach($xml_obj->entry as $v)
	    {
			$yt = $v->children('http://gdata.youtube.com/schemas/2007');
			$media = $v->children('http://search.yahoo.com/mrss/');
			$statistics = $yt->statistics->attributes();
			$player = $media->group->player->attributes();
			
			$statistics_view_count =  0;
			
			if(isset($statistics['viewCount']))
			{
				$statistics_view_count = $statistics['viewCount'];
			}
			
			
		    $video = array(
		    	'title' => $v->title,
		    	'username' => $v->author->name,
		    	'plays' => $statistics_view_count,
		    	'date' => strftime("%d/%m/%Y", strtotime($v->published)),
		    	'video_page' => $player['url'],
		    	'thumbnail' => $media->group->thumbnail[0]->attributes()
		    );
		    
		    array_push($videos, $video);
	    }
	    
	    return $videos;
	}
	
	// --------------------------------------------------------------------
	
	function get_videos($page, $per_page)
	{
		$api = $this->_getApiObject();
		
		$url = 'https://gdata.youtube.com/feeds/api/users/default/uploads';
		
	    $response = $api->fetch($url);
	    
	    $result = $response['result'];
	    
	    $xml_obj = simplexml_load_string($result);
    
	    $videos = array();
	    
	    foreach($xml_obj->entry as $v)
	    {
			$yt = $v->children('http://gdata.youtube.com/schemas/2007');
			$media = $v->children('http://search.yahoo.com/mrss/');
			$statistics = $yt->statistics->attributes();
			$player = $media->group->player->attributes();
			
		    $video = array(
		    	'title' => $v->title,
		    	'username' => $v->author->name,
		    	'plays' => $statistics['viewCount'],
		    	'date' => strftime("%d/%m/%Y", strtotime($v->published)),
		    	'video_page' => $player['url'],
		    	'thumbnail' => $media->group->thumbnail[0]->attributes()
		    );
		    
		    array_push($videos, $video);
	    }
	    
	    return $videos;
	}
	
	// --------------------------------------------------------------------
	
	function get_favorites($page, $per_page)
	{
		try {
		
			$api = $this->_getApiObject();
			
			$url = 'https://gdata.youtube.com/feeds/api/users/default/favorites';
			
		    $response = $api->fetch($url);
		    
		    $result = $response['result'];
		    
		    $xml_obj = simplexml_load_string($result);
	    
		    $videos = array();
		    
		    foreach($xml_obj->entry as $v)
		    {
				$yt = $v->children('http://gdata.youtube.com/schemas/2007');
				$media = $v->children('http://search.yahoo.com/mrss/');
				$statistics = $yt->statistics->attributes();
				$player = $media->group->player->attributes();
				
				// extract video id from video feed url
				
				$video_id = substr($v->id, strrpos($v->id, "/") + 1);
				
			    $video = array(
			    	'id' => $video_id,
			    	'favorite_id' => $yt->favoriteId,
			    	'title' => $v->title,
			    	'username' => $v->author->name,
			    	'plays' => $statistics['viewCount'],
			    	'date' => strftime("%d/%m/%Y", strtotime($v->published)),
			    	'video_page' => $player['url'],
			    	'thumbnail' => $media->group->thumbnail[0]->attributes()
			    );
			    
			    array_push($videos, $video);
		    }
	
		    return $videos;
	    }
		catch(Exception $e)
		{
			$this->throw_exception($e, __CLASS__, __FUNCTION__);
		}
	}
	
	// --------------------------------------------------------------------
	
	function is_favorite($video_id)
	{
		$videos = $this->get_favorites(0,0);
		
		foreach($videos as $v)
		{

			if($v['id'] == $video_id)
			{
				return true;
			}
		}

		return false;
	}
	
	// --------------------------------------------------------------------
	
	function add_favorite($video_id)
	{
		$method = 'POST';
		
		$url = 'https://gdata.youtube.com/feeds/api/users/default/favorites';
		
		$query = '<?xml version="1.0" encoding="UTF-8"?><entry xmlns="http://www.w3.org/2005/Atom"><id>'.$video_id.'</id></entry>';
				
		$api = $this->_getApiObject();
		
		$header = array(
			'Content-Type' => 'application/atom+xml',
			'X-GData-Key' => 'key='.$this->options['developer_key']
		);
		
		$api->setAccessTokenType(2);
		
		$response = $api->fetch($url, $query, $method, $header);
	}
	
	// --------------------------------------------------------------------
	
	function remove_favorite($video_id)
	{
		$videos = $this->get_favorites(0,0);
		
		foreach($videos as $v)
		{
			if($v['id'] == $video_id)
			{
				$favorite_id = $v['favorite_id'];
			}
		}
		
		if($favorite_id)
		{
			$method = 'DELETE';
			
			$url = 'https://gdata.youtube.com/feeds/api/users/default/favorites/'.$favorite_id;
			
			$query = '';

			$api = $this->_getApiObject();
			
			$header = array(
				'Content-Type' => 'application/atom+xml',
				'X-GData-Key' => 'key='.$this->options['developer_key']
			);
			
			$api->setAccessTokenType(2);
			
			$response = $api->fetch($url, $query, $method, $header);
		}
	}
	
	// --------------------------------------------------------------------
	
	function metadata($video)
	{
		$api = $this->_getApiObject();
		
		$url = 'https://gdata.youtube.com/feeds/api/videos/'.$video['id'];
		
	    $response = $api->fetch($url);
	    
	    $result = $response['result'];

	    $xml_obj = simplexml_load_string($result);
	   
	    // statistics
	    
	    $yt = $xml_obj->children('http://gdata.youtube.com/schemas/2007');
	    
		$statistics = $yt->statistics->attributes();
		
		$statistics_view_count =  0;
		
		if(isset($statistics['viewCount']))
		{
			$statistics_view_count = $statistics['viewCount'];
		}
		

	    // duration
	    
		$media = $xml_obj->children('http://search.yahoo.com/mrss/');    	    
	    
	    $yt = $media->children('http://gdata.youtube.com/schemas/2007');
	    
		$duration = $yt->duration->attributes();


		// author
		
		$author = $xml_obj->author;
		
		
		// variables

	    $video['url'] 					= 'http://youtu.be/'.$video['id'];
	    $video['title'] 				= $xml_obj->title;
	    $video['description'] 			= nl2br($media->group->description[0]);
	    $video['date'] 					= strftime("%d/%m/%Y", strtotime($xml_obj->published));
	    $video['plays'] 				= $statistics_view_count;
		$video['duration'] 				= $duration['seconds'];

		$video['author_name'] 			= $author->name;
		$video['author_link'] 			= $author->uri;

	    $video['thumbnail'] 			= $media->group->thumbnail[1]->attributes();
	    $video['thumbnail_large'] 		= $media->group->thumbnail[0]->attributes();
	    
	    $video['youtube_thumbnail_1'] 	= $media->group->thumbnail[0]->attributes();
	    $video['youtube_thumbnail_2'] 	= $media->group->thumbnail[1]->attributes();
	    $video['youtube_thumbnail_3'] 	= $media->group->thumbnail[2]->attributes();
	    $video['youtube_thumbnail_4'] 	= $media->group->thumbnail[3]->attributes(); 

	    return $video;
	}
	
	// --------------------------------------------------------------------
	
	function get_video_id($url)
	{
		// check if url works with this service and extract video_id
		$video_id = false;

		$regexp = array('/^https?:\/\/(www\.youtube\.com|youtube\.com|youtu\.be).*\/(watch\?v=)?(.*)/', 3);
		


		if(preg_match($regexp[0], $url, $matches, PREG_OFFSET_CAPTURE) > 0)
		{

			// regexp match key

			$match_key = $regexp[1];


			// define video id

			$video_id = $matches[$match_key][0];


			// Fixes the youtube &feature_gdata bug

			if(strpos($video_id, "&"))
			{
				$video_id = substr($video_id, 0, strpos($video_id, "&"));
			}
		}

		// here we should have a valid video_id or false if service not matching

		return $video_id;

	}
	
	// --------------------------------------------------------------------
	
	function get_profile()
	{
		$api = $this->_getApiObject();
		
	    $response = $api->fetch('https://gdata.youtube.com/feeds/api/users/default/favorites');
	    
	    return $response;
	}
	
	// --------------------------------------------------------------------
	
	public function connect($dukt_lib, $dukt_videos)
	{
		$service_key = $this->service_key;
		$service = $this;
		
		$client_id 		= $this->options['client_id'];
		$client_secret 	= $this->options['client_secret'];
		
		$refresh_token 	= $dukt_videos->get_option($service_key, 'token');
		

		// redirect uri
		
		$oauth_redirect_uri = $this->oauth_redirect_uri;
		

		require(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/Client.php');
		require(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/GrantType/IGrantType.php');
		require(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/GrantType/AuthorizationCode.php');
		require(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/OAuth2/GrantType/RefreshToken.php');
		
		$state = array();
		$state['oauth2_admin_redirect'] = $_SERVER['REQUEST_URI'];
		$state = json_encode($state);

		
		$state = strtr(base64_encode($state), '+/=', '-_,');
		
		$extra_parameters = array(
			'scope' => "https://gdata.youtube.com",
			'state' => $state,
			'approval_prompt' => 'force',
			'access_type' => 'offline'
		);
	
		$client = new OAuth2\Client($client_id, $client_secret);
	
		if($refresh_token)
		{   
		    //$redirect = $dukt_videos->cp_link();
		    
		    $redirect = $this->oauth_success_url;
		    
		    $dukt_videos->redirect($redirect);
		}
		elseif (!isset($_GET['code']))
		{
		    $auth_url = $client->getAuthenticationUrl($service->oauth_authorization_endpoint, $oauth_redirect_uri, $extra_parameters);
		   
		    $dukt_videos->redirect($auth_url);
		}
		else
		{
		    $params = array('code' => $_GET['code'], 'redirect_uri' => $oauth_redirect_uri);
		    
		    $response = $client->getAccessToken($service->oauth_token_endpoint, 'authorization_code', $params);
		    
		    $info = $response['result'];
		    
		    $dukt_videos->set_option($service_key, 'token', $info['refresh_token']);

			// $redirect = $dukt_videos->cp_link('method=connect&service='.$service->service_key);
			
			$redirect = $this->connect_url;
		    
		    $dukt_videos->redirect($redirect);
		}
	}
	
	// --------------------------------------------------------------------
	
	public function connect_callback($dukt_lib, $dukt_videos)
	{
		if(isset($_GET['youtube']))
		{
			@ini_set('display_errors', 'on');
			
			$state = $_GET['state'];
			$code = $_GET['code'];
			
			$state = base64_decode(strtr($state, '-_,', '+/='));
			$state = json_decode($state);
			
			if(is_object($state))
			{
				$redirect  = $state->oauth2_admin_redirect;
			
				$redirect_parsed = parse_url($redirect);
				
				$query = array();
				
				if(isset($redirect_parsed['query']))
				{	
					$query = parse_str($redirect_parsed['query'], $query_output);
					$query = $query_output;
				}
			
				$query['code'] = $code;
				
				$query = "?".http_build_query($query);
				
				$redirect = $redirect_parsed['path'].$query;
				
				// header("Location: ".  $redirect);		
				
				$dukt_videos->redirect($redirect);
			}
			else
			{
				echo "error with state";
			}
		}
	}
}
?>