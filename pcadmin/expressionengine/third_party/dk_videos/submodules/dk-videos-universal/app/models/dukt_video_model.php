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

class Dukt_video_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_option($k, $user_id)
    {
    	$this->db->where('user_id', $user_id);
    	$this->db->where('option_key', $k);
    	
	    $query = $this->db->get('options');
	    
		if ($query->num_rows() > 0)
		{
		   $row = $query->row(); 
		
		   return $row->option_value;
		}
		
		return false;
    }
    
    function set_option($k, $v, $user_id)
    {
	    if($this->get_option($k, $user_id) !== false)
	    {
		    // update

			$data = array(
				'user_id' => $user_id,
				'option_value' => $v
			);
			
			$this->db->where('option_key', $k);
			
			$this->db->update('options', $data); 

	    }
	    else
	    {
		    // insert
		    
			$data = array(
				'user_id' => $user_id,
				'option_key' => $k,
				'option_value' => $v
			);
		    
		    $this->db->insert('options', $data); 
	    }
    }
    
    function reset_options($prefix, $user_id)
    {
    	if(!empty($prefix))
    	{
		    $this->db->like('option_key', $prefix, 'after'); 
		    $this->db->delete('options'); 
	    }
    }
}