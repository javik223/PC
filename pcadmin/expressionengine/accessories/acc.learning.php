<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2013, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ExpressionEngine Learning Accessory
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Accessories
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Learning_acc {
	
	var $name			= 'Learning EE';
	var $id				= 'learningEE';
	var $version		= '1.0';
	var $description	= 'Educational Resources for ExpressionEngine';
	var $sections		= array();
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Sections
	 *
	 * Set content for the accessory
	 *
	 * @access	public
	 * @return	void
	 */
	function set_sections()
	{
		$this->sections = array(
			
			ee()->lang->line('community_tutorials')	 => 	'<ul>
						<li>'.ee()->lang->line('train_ee').'</li>
						<li>'.ee()->lang->line('ee_screencasts').'</li>
						<li>'.ee()->lang->line('ee_seach_bookmarklet').'</li>
					</ul>'
						,
						
			ee()->lang->line('community_resources') => '<ul>
						<li>'.ee()->lang->line('ee_insider').'</li>
						<li>'.ee()->lang->line('devot_ee').'</li>
						<li>'.ee()->lang->line('ee_podcast').'</li>
						<li>Show-EE</li>
					</ul>
			',
			ee()->lang->line('support') => '<ul>
						<li><a href="'.ee()->cp->masked_url(ee()->config->item('doc_url')).'" title="'.ee()->lang->line('documentation').'">'.ee()->lang->line('documentation').'</a></li>
						<li><a href="'.ee()->cp->masked_url('http://www.dereferer.com/?http://ellislab.com/forums/').'" title="'.ee()->lang->line('support_forums').'">'.ee()->lang->line('support_forums').'</a></li>
					</ul>'			
		);
	}

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file acc.learning.php */
/* Location: ./system/expressionengine/accessories/acc.learning.php */