<?php
/**
 * @package     CODERS.Repository
 * @subpackage  com_coderepo
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
/**
 * @since  0.0.1
 */
class CodeRepoViewCodeRepo extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	function display( $tpl = null )
	{
		// Assign data to the view
		$this->msg = 'Hello World';
		// Display the view
		parent::display($tpl);
	}
}


