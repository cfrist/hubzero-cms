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
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->cn.'&active=blog&task=savesettings'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p>Privacy settings can be set for individual posts when creating/editing them.</p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('Blog Settings'); ?></legend>

			<label>
				Who can post to this blog?
				<select name="params[posting]">
					<option value="0"<?php if (!$this->config->get('posting')) { echo ' selected="selected"'; }?>>All course members</option>
					<option value="1"<?php if ($this->config->get('posting') == 1) { echo ' selected="selected"'; }?>>Course managers only</option>
				</select>
			</label>
			
			<p class="help">
				Course managers can add/edit/delete any post. Standard course members can only edit/delete <strong>their own</strong> posts.
			</p>
			
			<label>
				RSS Feed of entries
				<select name="params[feeds_enabled]">
					<option value="0"<?php if (!$this->config->get('feeds_enabled')) { echo ' selected="selected"'; }?>>Disabled</option>
					<option value="1"<?php if ($this->config->get('feeds_enabled') == 1) { echo ' selected="selected"'; }?>>Enabled</option>
				</select>
			</label>
			
			<label>
				The length of RSS feed entries
				<select name="params[feed_entries]">
					<option value="full"<?php if ($this->config->get('feed_entries') == 'full') { echo ' selected="selected"'; }?>>Full</option>
					<option value="partial"<?php if ($this->config->get('feed_entries') == 'partial') { echo ' selected="selected"'; }?>>Partial</option>
				</select>
			</label>
			
			<p class="help">
				<strong>Note:</strong> Feeds will only contain content marked as <strong>public</strong>. This is because there is currently no way to generate secure or private feeds.
			</p>
			
			<input type="hidden" name="settings[id]" value="<?php echo $this->settings->id; ?>" />
			<input type="hidden" name="settings[object_id]" value="<?php echo $this->course->gidNumber; ?>" />
			<input type="hidden" name="settings[folder]" value="courses" />
			<input type="hidden" name="settings[element]" value="blog" />
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="gid" value="<?php echo $this->course->cn; ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="task" value="savesettings" />
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('PLG_COURSES_BLOG_SAVE'); ?>" />
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->cn.'&active=blog'); ?>">Cancel</a>
		</p>
	</form>
