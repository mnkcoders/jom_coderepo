<?php
/**
 * @package     Coders.Repository
 * @subpackage  com_coders_repository
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/*if (!JFactory::getUser()->authorise('core.manage', 'com_coderepo'))
{
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}*/

$controller = JControllerLegacy::getInstance('CodersRepo');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
