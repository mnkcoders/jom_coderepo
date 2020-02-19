<?php
/**
 * @package     CODERS.Repository
 * @subpackage  com_coders_repository
 *
 */

defined('_JEXEC') or die;

/**
 * 
 */
class CodersRepoController extends JControllerLegacy
{
	/**
	 * @var    string  The default view.
	 */
	protected $default_view = 'summary';

	/**
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  CodersRepoController  This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', $this->default_view );

                print $vName; 
                
                return $this;
	}
}


