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
 
class Dk_videos_ajax {

	public function __construct()
	{
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/dukt_lib.php');
			
		$this->dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
		
		$this->dukt_lib->load_helper('url');
	}
	
	// --------------------------------------------------------------------
	
	public function index()
	{	
		$service = $this->dukt_lib->input_post('service');
		$method = $this->dukt_lib->input_post('method');

		if($method && $service)
		{
			$this->{$method}();	
		}
	}
		
	// --------------------------------------------------------------------
	
	public function box()
	{
		$dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
		
		$vars = array();
		$vars['services'] = $this->services;
		$vars['manage_link'] = '#';
		
		$dukt_lib->load_view('box/box', $vars);
		
		exit;
	}
	
	// --------------------------------------------------------------------

	public function service_search()
	{	
		$service = $this->dukt_lib->input_post('service');
		$service = $this->services[$service];
		
		$q = $this->dukt_lib->input_post('q');
	
		try
		{
			$videos = array();
			
			$pagination = $this->pagination();
				
			$vars['videos'] = $videos;
			$vars['pagination'] = $pagination;
			$vars['service'] = $service;
			
			if(!empty($q))
			{
				$videos = $service->search($q, $pagination['page'], $pagination['per_page']);
			}
			
			$vars['videos'] = $videos;

			$dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
			
			echo $dukt_lib->load_view('box/videos', $vars, true);
			
			exit;
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}
	
	// --------------------------------------------------------------------
	
	public function service_favorites()
	{
		$service = $this->dukt_lib->input_post('service');
		$service = $this->services[$service];
			
		try
		{
		
			$videos = array();

			$pagination = $this->pagination();
			
			$videos = $service->get_favorites($pagination['page'], $pagination['per_page']);

			$vars['videos'] = $videos;
			$vars['pagination'] = $pagination;
			
			$dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
			echo $dukt_lib->load_view('box/videos', $vars, true);
			exit;
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}	
	}
	
	// --------------------------------------------------------------------
	
	public function favorite()
	{
		$service = $this->dukt_lib->input_post('service');
		$service = $this->services[$service];


		$video_page = $this->dukt_lib->input_post('video_page');

		$video_id = $service->get_video_id($video_page);


		// check if already a fav

		$already_fav = $service->is_favorite($video_id);


		if($already_fav)
		{
			// remove favorite if it is
			
			$service->remove_favorite($video_id);
		}
		else
		{
			// add favorite if it's not

			$service->add_favorite($video_id);
		}
	}
	
	// --------------------------------------------------------------------
	
	public function service_videos()
	{
		$service = $this->dukt_lib->input_post('service');
		$service = $this->services[$service];
	
		try {
			$videos = array();

			$pagination = $this->pagination();
			
			$videos = $service->get_videos($pagination['page'], $pagination['per_page']);

			$vars['videos'] = $videos;
			$vars['pagination'] = $pagination;
			
			$dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
			echo $dukt_lib->load_view('box/videos', $vars, true);
			exit;
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}
	
	// --------------------------------------------------------------------
	
	public function service_playlists()
	{
		echo "hello";
	}
	
	// --------------------------------------------------------------------
	
	public function box_preview()
	{
		$service = $this->dukt_lib->input_post('service');
		$service = $this->services[$service];

		$video_page = $this->dukt_lib->input_post('video_page');

		$video_opts = array(
			'url' => $video_page,
		);
		
		$embed_opts = array(
			'width' => false,
			'height' => false,
			'autohide' => 1
		);
		
		// get all possible options from post
		
		foreach($service->embed_options as $k => $v)
		{
			$post_v = $this->dukt_lib->input_post($k);
			
			if($post_v !== false)
			{		
				$embed_opts[$k] = $this->dukt_lib->input_post($k);	
			}
			else
			{
				if(!isset($embed_opts[$k]))
				{
					$embed_opts[$k] = $v;
				}
			}
		}
		
		$video = $service->get_video($video_opts, $embed_opts);
		
		
		// is_favorite ?

		$video['is_favorite'] = $service->is_favorite($video['id']);
		
		
		// load view

		$vars['video'] = $video;
		$vars['service'] = $service->service_key;

		$dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
		
		echo $dukt_lib->load_view('box/preview', $vars, true);	
		
		exit;
	}
	
	// --------------------------------------------------------------------
	
	private function pagination()
	{
		$pagination['per_page'] = 25;

		$pagination['page'] = $this->dukt_lib->input_post('page');

		if(!$pagination['page'])
		{
			$pagination['page'] = 1;
		}

		$pagination['next_page'] = $pagination['page'] + 1;

		return $pagination;
	}
	
	// --------------------------------------------------------------------
	
	public function error($msg)
	{
		echo $msg;
	}
}