<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if (in_array($this->juser->get('id'), $this->members)) : ?>
	<ul id="page_options">
		<li>
			<a class="icon-add btn add" title="<?php echo JText::_('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT'); ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=add'); ?>">
				<?php echo JText::_('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT'); ?>
			</a>
			<?php if ($this->authorized == 'manager') : ?>
				<a class="icon-date btn date" title="<?php echo JText::_('Manage Calendars'); ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=calendars'); ?>">
					<?php echo JText::_('Manage Calendars'); ?>
				</a>
			<?php endif; ?>
		</li>
	</ul>
<?php endif; ?>

<div id="calendar"></div>

<?php
	if ($this->params->get('allow_subscriptions', 1))
	{
		$this->view('subscribe')
			->set('calendar', $this->calendar)
			->set('calendars', $this->calendars)
			->set('group', $this->group)
			->display();
	}
?>
