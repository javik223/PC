<?php 

/*
=====================================================
 Author: MaxLazar
 http://www.wiseupstudio.com
=====================================================
 File: pi.zip.php
-----------------------------------------------------
 Purpose: Allows you to store session variables, ExpressionEngine2
=====================================================
*/




$plugin_info = array(
						'pi_name'			=> 'MX Ajax Detect',
						'pi_version'			=> '2.1',
						'pi_author'			=> 'Max Lazar',
						'pi_author_url'		=> 'http://wiseupstudio.com/',
						'pi_description'	=> 'Detect an AJAX Request',
						'pi_usage'			=> Ajax_Detect::usage()
					);


Class Ajax_Detect {

    var $return_data='';
    
    function Ajax_Detect ()
    {    
		$this->EE =& get_instance();
        $tagdata =  $this->EE->TMPL->tagdata;
        $conds['ajax'] =(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
        $tagdata =   $this->EE->functions->prep_conditionals($tagdata, $conds); 
        $tagdata =   $this->EE->TMPL->swap_var_single("ajax", $conds['ajax'], $tagdata); 
        $this->return_data = $tagdata;
    }
    
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{	
ob_start(); 
?>
Place the following tags in any of your templates:

{exp:ajax_detect}

{/exp:ajax_detect}

Place between these tags request detection code such as this:


{exp:ajax_detect}
{if ajax} 
 special  code for ajax
{if:else}
 for request without ajax
{/if}
{/exp:ajax_detect}

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
/* END */

}
?>