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
 
class Dk_videos_ajax_expressionengine extends Dk_videos_ajax {

	public function __construct()
	{
		parent::__construct();
		
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine/libraries/dukt_videos.php');
		
		$this->dukt_videos = new Dukt_videos;
		
		$this->services = $this->dukt_videos->get_services();
	}
	
	// --------------------------------------------------------------------
	
	public function field_preview()
	{
		$services = $this->services;
		
		$video_page = $this->dukt_lib->input_post('video_page');
		
		$video_opts = array(
			'url' => $video_page,
		);
		
		$embed_opts = array(
			'width' => 300,
			'height' => 169,
			'autohide' => true
		);
		
		$vars['video'] = $this->dukt_videos->get_video($video_opts, $embed_opts);

		echo $this->dukt_lib->load_view('field/preview', $vars, true, 'expressionengine');
		
		exit;
	}
}