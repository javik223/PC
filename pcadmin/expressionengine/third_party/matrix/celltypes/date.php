<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Date Celltype Class for EE2
 *
 * @package   Matrix
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic, Inc
 */
class Matrix_date_ft {

	var $info = array(
		'name' => 'Date'
	);

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();

		// -------------------------------------------
		//  Prepare Cache
		// -------------------------------------------

		if (! isset($this->EE->session->cache['matrix']['celltypes']['date']))
		{
			$this->EE->session->cache['matrix']['celltypes']['date'] = array();
		}
		$this->cache =& $this->EE->session->cache['matrix']['celltypes']['date'];
	}

	// --------------------------------------------------------------------

	/**
	 * Modify exp_matrix_data Column Settings
	 */
	function settings_modify_matrix_column($data)
	{
		return array(
			'col_id_'.$data['col_id'] => array('type' => 'int', 'constraint' => 10, 'default' => 0)
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Display Cell
	 */
	function display_cell($data)
	{
		if (! isset($this->cache['displayed']))
		{
			// include matrix_text.js
			$theme_url = $this->EE->session->cache['matrix']['theme_url'];
			$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$theme_url.'scripts/matrix_date_ee2.js"></script>');
			$this->EE->cp->add_js_script(array('ui' => 'datepicker'));

			$this->cache['displayed'] = TRUE;
		}

		$r['class'] = 'matrix-date matrix-text';

		// quick save / validation error?
		$datestr = trim($data);
		$datestr = preg_replace('/\040+/', ' ', $datestr);
		if (preg_match('/^(?P<first_part>[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}\s)(?P<hours>[0-9]{1,2})(?P<second_part>:[0-9]{1,2}(:[0-9]{1,2})?)(?P<meridiem>\s[AP]M)?$/i', $datestr, $matches))
		{
			// Prevent a seemingly valid, yet totally invalid, date from crashing EE.
			if (isset($matches['meridiem']) && strtolower($matches['meridiem']) == ' pm' && $matches['hours'] === '0')
			{
				$datestr = $matches['first_part'].'12'.$matches['second_part'].$matches['meridiem'];
			}

			// convert human time to a unix timestamp
			$data = version_compare(APP_VER, '2.6', '<') ? $this->EE->localize->convert_human_date_to_gmt($datestr) : $this->EE->localize->string_to_timestamp($datestr);
		}

		// set the default date to the current time
		if (version_compare(APP_VER, '2.6', '<') )
		{
			$r['settings']['defaultDate'] = ($data ? $this->EE->localize->set_localized_time($data) : $this->EE->localize->set_localized_time()) * 1000;
		}
		else
		{
			$r['settings']['defaultDate'] = ($data ? $this->EE->localize->format_date("%U", $data) : $this->EE->localize->format_date("%U", $data)) * 1000;
		}

		// get the initial input value
		$formatted_date = $data ? $this->EE->localize->human_time($data) : '';

		$r['data'] = form_input(array(
			'name'  => $this->cell_name,
			'value' => $formatted_date,
			'class' => 'matrix-textarea'
		));

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Cell
	 */
	function validate_cell($data)
	{
		// is this a required column?
		if ($this->settings['col_required'] == 'y' && ! $data)
		{
			return lang('col_required');
		}

		return TRUE;
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		// convert the formatted date to a Unix timestamp
		return version_compare(APP_VER, '2.6', '<') ? $this->EE->localize->convert_human_date_to_gmt($data) : $this->EE->localize->string_to_timestamp($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Replace Tag
	 */
	function replace_tag($data, $params = array())
	{
		if (! $data) return '';

		if (isset($params['format']))
		{
			$data = $this->EE->localize->format_date($params['format'], $data);
		}

		return $data;
	}

}
