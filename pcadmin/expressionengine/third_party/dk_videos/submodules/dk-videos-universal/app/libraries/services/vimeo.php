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

class Dukt_video_vimeo extends Dukt_video_service {

	var $enabled 			= true;
	var $is_authenticated 	= true;
	var $service_key 		= "vimeo";
	var $service_name 		= "Vimeo";
	var $api_key 			= false;
	var $api_secret 		= false;
	var $universal_url 		= "http://player.vimeo.com/video/%s";
	
	var $api_options = array(
		'api_key' => false,
		'api_secret' => false
	);

	var $token_options = array(
		'access_token' => false,
		'access_token_secret' => false
	);
		
	var $embed_options = array(
		'width'				=> 500,
		'height'			=> 300,
		'autoplay'			=> 0,
		'byline'			=> 1,
		'color'				=> false,
		'loop'				=> 0,
		'portrait'			=> 1,
		'title'				=> 1
	);
	
	// --------------------------------------------------------------------

	function _getApiObject()
	{

		$vimeo = false;
		
		if (class_exists('phpVimeo') === false)
		{
			require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/sdks/vimeo/vimeo.php');
		}

		$api_key 				= $this->options['api_key'];
		$api_secret 			= $this->options['api_secret'];
		$oauth_request_token	= $this->options['oauth_request_token'];
		$oauth_request_token_secret 	= $this->options['oauth_request_token_secret'];
		$access_token 			= $this->options['access_token'];
		$access_token_secret 	= $this->options['access_token_secret'];
		
		
/*
		$access_token 			= 'a7e4b1f80eff539c56beca25b8e2fbb5';
		$access_token_secret 	= '35fad401716bd260f606bda636fed11576e54f18';
*/

/*
		$access_token 			= 'cf12b5d21085c2a1d06a73381f81f80e';
		$access_token_secret 	= '486f34bb0e8a39404f164be6f60d67241ab06469';
*/

/* 		var_dump($this->options); */




/*
  ["oauth_request_token"]=>
  string(32) "20b0f388066a358b15b0fa91f63110ba"
  ["oauth_request_token_secret"]=>
  string(40) "d2998e5ab6208926d1fd6f3289ce255cb91dad6f"
  ["vimeo_state"]=>
  string(8) "returned"
  ["oauth_access_token"]=>
  NULL
  ["oauth_access_token_secret"]=>
  NULL
  ["redirect"]=>
  bool(false)
  ["admin_redirect"]=>
  string(47) "http://videos.dukt.net.dk/account/connect/vimeo"
  ["oauth_state"]=>
  string(4) "done"
  ["vimeo_access_token"]=>
  string(32) "2de1a7a58c9f5fb428985dfdc178654a"
  ["vimeo_access_token_secret"]=>
  string(40) "26473493a241496b25d9479660a9d6c34f48bd60"
  ["vimeo_oauth_token"]=>
  string(32) "f45fe31ea540e63ae6048d5ead92deb1"
*/


		
		$vimeo = new phpVimeo($api_key, $api_secret);

		$vimeo->setToken($access_token, $access_token_secret);
        
        return $vimeo;
	}
	
	// --------------------------------------------------------------------
	
	function is_authenticated()
	{
		try {
			$vimeo = $this->_getApiObject();
			
			$method = 'vimeo.test.login';

			$params = array(

			);

			$r = $vimeo->call($method, $params);

			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	
	function get_profile()
	{
		$profile = array(
			'display_name' => "",
			'username' => ""
		);	
		
		try {

			$videos = array();

			$vimeo = $this->_getApiObject();
			
			$method = 'vimeo.people.getInfo';

			$params = array();


			//$vimeo->enableCache('file', $this->cache_path(), 5);

			$r = $vimeo->call($method, $params);

			if($r)
			{
				$profile['display_name'] = $r->person->display_name;
				$profile['username'] = $r->person->username;
			}
			else
			{
				$error = $method;
				$error .= print_r($r, true);
				
				throw new Exception($error);
			}
		}
		catch(Exception $e)
		{
			$this->throw_exception($e, __CLASS__, __FUNCTION__);
		}
		
		return $profile;
	}

	// --------------------------------------------------------------------	

	/**
	 * Search videos
	 *
	 * @access	public
	 * @param	string
	 * @param	integer
	 * @param	integer
	 * @return	array
	 */
	function search($q, $page, $per_page)
	{
		try {
			$videos = array();

			$vimeo = $this->_getApiObject();

			// $vimeo->enableCache('file', $this->cache_path(), 300);

			$params = array(
				'query' 		=> $q,
				'full_response'	=> 1,
				'page' 			=> $page,
				'per_page'		=> $per_page,
				'format' 		=> 'php'
			);

			$r = $vimeo->call('vimeo.videos.search', $params);

			if($r)
			{
				if(isset($r->videos->video))
				{
					foreach($r->videos->video as $v)
					{
						$video = $this->developerdata($v);
						array_push($videos, $video);
					}

					return $videos;
				}
			}
		}
		catch(Exception $e)
		{
			$this->throw_exception($e, __CLASS__, __FUNCTION__);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get my videos
	 *
	 * @access	public
	 * @param	integer
	 * @param	integer
	 * @return	array
	 */
	function get_videos()
	{

		try {
			$videos = array();

			$vimeo = $this->_getApiObject();

			$params = array(
				'full_response'	=> true,
				'per_page' 		=> 20,
				'format' 		=> 'php'
			);

			// $vimeo->enableCache('file', $this->cache_path(), 5);

			$r = $vimeo->call('vimeo.videos.getUploaded', $params);

			if($r)
			{
				if(isset($r->videos->video))
				{
					foreach($r->videos->video as $v)
					{
						$video = $this->developerdata($v);

						array_push($videos, $video);
					}

					return $videos;
				}
			}
		}
		catch(Exception $e)
		{
			$this->throw_exception($e, __CLASS__, __FUNCTION__);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get favorite videos
	 *
	 * @access	public
	 * @param	integer
	 * @param	integer
	 * @return	array
	 */
	function get_favorites($page, $per_page)
	{
		try {

			$videos = array();

			$vimeo = $this->_getApiObject();

			$params = array(
				'full_response'	=> 1,
				'format' 		=> 'php'
			);

			if($page > 0)
			{
				$params['page'] = $page;
			}

			if($per_page > 0)
			{
				$params['per_page'] = $per_page;
			}


			//$vimeo->enableCache('file', $this->cache_path(), 5);

			$r = $vimeo->call('vimeo.videos.getLikes', $params);

			if($r)
			{
				if(isset($r->videos->video))
				{
					foreach($r->videos->video as $v)
					{

						$video = $this->developerdata($v);

						array_push($videos, $video);
					}

					return $videos;
				}
			}
		}
		catch(Exception $e)
		{
			$this->throw_exception($e, __CLASS__, __FUNCTION__);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Is favorite ?
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function is_favorite($video_id)
	{
		$videos = $this->get_favorites(0,0);

		if($videos)
		{
			foreach($videos as $v)
			{

				if($v['id'] == $video_id)
				{
					return true;
				}
			}
		}

		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * Add favorite
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function add_favorite($video_id)
	{
		$vimeo = $this->_getApiObject();

		$params = array(
			'video_id' 		=> $video_id,
			'like'			=> 1
		);

		$vimeo->call('vimeo.videos.setLike', $params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Remove favorite
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function remove_favorite($video_id)
	{
		$vimeo = $this->_getApiObject();

		$params = array(
			'video_id' 		=> $video_id,
			'like'			=> 0
		);

		$vimeo->call('vimeo.videos.setLike', $params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get video developer data
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
	private function developerdata($item)
	{
		$video = array();
		$video['service_name'] 	= "Vimeo";
		$video['id'] 		= (string) $item->id;
		$video['title'] 		= (string) $item->title;
		$video['description'] 	= (string) $item->description;
		$video['username']		= (string) $item->owner->display_name;
		$video['author'] 		= (string) $item->owner->display_name;
		$video['date'] 			= (string) strftime("%d/%m/%Y", strtotime($item->upload_date));
		$video['plays']			= (string) $item->number_of_plays;
		$video['duration'] 		= (string) $item->duration;
		$video['thumbnail'] 	= (string) $item->thumbnails->thumbnail[0]->_content;


		// find the video url

		$v_urls = $item->urls->url;

		foreach($v_urls as $v_url)
		{
			if($v_url->type == "video")
			{
				$video['video_page'] = (string) $v_url->_content;
			}
		}

		return $video;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get video meta data
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
	function metadata($video)
	{
		// api call

		try {

			$videos = array();

			$vimeo = $this->_getApiObject();
			
			$method = 'vimeo.videos.getInfo';

			$params = array(
				'video_id' => $video['id']
			);
			
			// $vimeo->enableCache(phpVimeo::CACHE_FILE, $this->cache_path(), 600);

			$r = $vimeo->call($method, $params);

			if($r)
			{
				$v = $r->video[0];

				// assign new variables
				$video['url'] = 'http://vimeo.com/'.$video['id'];
				$video['title'] 			= (string) $v->title;
				$video['description'] 		= (string) $v->description;
				$video['username'] 			= (string) $v->owner->username;
				$video['author'] 			= (string) $v->owner->display_name;
				$video['date'] 				= (string) $v->upload_date;
				$video['plays'] 			= (string) $v->number_of_plays;
				$video['duration'] 			= (string) $v->duration;
				$video['thumbnail'] 		= (string) $v->thumbnails->thumbnail[0]->_content;
				
				
				// try to get XL thubmnail
				
/*
				if(isset($v->thumbnails->thumbnail[3]->_content))
				{					
					if($v->thumbnails->thumbnail[3]->_content !== false)
					{
						$video['thumbnail_large'] 	= (string) $v->thumbnails->thumbnail[3]->_content;	
					}
				}
				
				if($video['thumbnail_large'] == false && isset($v->thumbnails->thumbnail[2]->_content))
				{
					// Fallback to L size if XL doesn't exists
					
					$video['thumbnail_large'] 	= (string) $v->thumbnails->thumbnail[2]->_content;	
				}
				
				$video['vimeo_thumbnail_0'] 		= (string) $v->thumbnails->thumbnail[0]->_content;
				
				if(isset($v->thumbnails->thumbnail[1]->_content))
				{
					$video['vimeo_thumbnail_1'] = (string) $v->thumbnails->thumbnail[1]->_content;
				}
				if(isset($v->thumbnails->thumbnail[2]->_content))
				{
					$video['vimeo_thumbnail_2'] = (string) $v->thumbnails->thumbnail[2]->_content;
				}
				
				if(isset($v->thumbnails->thumbnail[3]->_content))
				{
					$video['vimeo_thumbnail_3'] = (string) $v->thumbnails->thumbnail[3]->_content;
				}
*/
				
				$video['date'] = strtotime($video['date']);
				
				return $video;
			}
		}
		catch(Exception $e)
		{
			$this->throw_exception($e, __CLASS__, __FUNCTION__);
		}

		return false;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get video id from url
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function get_video_id($url)
	{
		// check if url works with this service and extract video_id
		$video_id = false;

		$regexp = array('/^https?:\/\/(www\.)?vimeo\.com\/([0-9]*)/', 2);


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

	public function connect($dukt_lib, $dukt_videos)
	{		
		if(!isset($_GET['oauth_verifier']))
		{
			// STEP 1: Get a Request Token
	        // $callback_url = urlencode($callback_url);
			// $api_obj->setToken('', '');
			
			$dukt_lib->session_set_userdata('oauth_request_token', null);
			$dukt_lib->session_set_userdata('oauth_request_token_secret', null);

            $dukt_videos->set_option($this->service_key, 'access_token', null);
            $dukt_videos->set_option($this->service_key, 'access_token_secret', null);
			$this->options['access_token'] = '';
			$this->options['access_token_secret'] = '';
			
			$api_obj = $this->_getApiObject();
			$api_obj->setToken('', '');
			
			$admin_redirect = $this->admin_redirect;
			$callback_url = $this->oauth_redirect_uri;
			
			$dukt_lib->session_set_userdata('admin_redirect', $admin_redirect);
			
	        $token = $api_obj->getRequestToken($callback_url);
/* 	        var_dump($token); */
			$dukt_lib->session_set_userdata('oauth_request_token', $token['oauth_token']);
			$dukt_lib->session_set_userdata('oauth_request_token_secret', $token['oauth_token_secret']);

			
			// STEP 2: Authorize the Request Token
			
	        $authorize_link = $api_obj->getAuthorizeUrl($token['oauth_token'], 'write');
			
	        $dukt_videos->redirect($authorize_link);
		}
		else
		{
			// STEP 3 : Exchange the Authorized Request Token for a Long-Term Access Token.
			
			$api_obj = $this->_getApiObject();
			
			$success_url 			= $this->oauth_success_url;
			$request_token 			= $dukt_lib->session_userdata('oauth_request_token');	
			$request_token_secret 	= $dukt_lib->session_userdata('oauth_request_token_secret');

            $api_obj->setToken($request_token, $request_token_secret);
            
            $token = $api_obj->getAccessToken($_GET['oauth_verifier']);	            
            
            // var_dump($token);
            
            $dukt_videos->set_option($this->service_key, 'oauth_request_token', $request_token);
            $dukt_videos->set_option($this->service_key, 'oauth_request_token_secret', $request_token_secret);
            
            $dukt_videos->set_option($this->service_key, 'access_token', $token['oauth_token']);
            $dukt_videos->set_option($this->service_key, 'access_token_secret', $token['oauth_token_secret']);


/*
            $api_obj->setToken($token['oauth_token'], $token['oauth_token_secret']);
            
            $method = 'vimeo.test.login';

			$params = array();

			$r = $api_obj->call($method, $params);
			
			var_dump($r);
*/
            //echo $dukt_videos->get_option($this->service_key, 'oauth_token');
	        $dukt_videos->redirect($success_url); 
		}
	}
	
	public function connect_callback($dukt_lib, $dukt_videos)
	{
		if(isset($_GET['vimeo']))
		{
			$admin_redirect = $dukt_lib->session_userdata('admin_redirect');

			if($admin_redirect)
			{		
				// reassign vimeo callback variables
				
				$admin_redirect = parse_url($admin_redirect);
				
				$query = array();
				
				if(isset($admin_redirect['query']))
				{			
					parse_str($admin_redirect['query'], $query);	
				}
				
				$query['oauth_token'] = $_GET['oauth_token'];
				$query['oauth_verifier'] = $_GET['oauth_verifier'];

				$query = http_build_query($query);
				
				$admin_redirect = $admin_redirect['scheme'].'://'.$admin_redirect['host'].$admin_redirect['path'].'?'.$query;
				
				
				// redirect
/* 				echo "ya"; */

				$dukt_videos->redirect($admin_redirect);
			}
		}
	}
}
?>