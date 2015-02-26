<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Answers;

if (!\JFactory::getUser()->authorise('core.manage', 'com_answers'))
{
	return \JError::raiseWarning(404, \JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'economy.php');
require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'question.php');
require_once(__DIR__ . DS . 'helpers' . DS . 'permissions.php');

$controllerName = \JRequest::getCmd('controller', 'questions');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'questions';
}

\JSubMenuHelper::addEntry(
	\JText::_('COM_ANSWERS_QUESTIONS'),
	\JRoute::_('index.php?option=com_answers'),
	($controllerName == 'questions')
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_ANSWERS_RESPONSES'),
	\JRoute::_('index.php?option=com_answers&controller=answers&qid=0'),
	($controllerName == 'answers')
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
