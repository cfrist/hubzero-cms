<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Object;

include_once(__DIR__ . DS . 'format.php');
include_once(__DIR__ . DS . 'block.php');

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'block.php');

/**
 * Publications blocks class
 *
 */
class Blocks extends Object
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	public $_db   			= NULL;

	/**
	 * Table class
	 *
	 * @var object
	 */
	public $_objBlock   	= NULL;

	/**
	* @var    array  Loaded elements
	*/
	protected $_blocks 		= array();

	/**
	* @var    array  Directories, where block types can be stored
	*/
	protected $_blockPath 	= array();

	/**
	 * Constructor
	 *
	 * @param      object  &$db      	 JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db 		 	= $db;
		$this->_blockPath[] = dirname(__FILE__) . DS . 'blocks';

		$this->_objBlock 	= new \Components\Publications\Tables\Block($db);
	}

	/**
	 * Get status for a block within publication
	 *
	 * @return object
	 */
	public function getStatus($name, $pub = NULL, $manifest = NULL, $blockId = 0, $elementId = NULL)
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$status = new \Components\Publications\Models\Status();
		}
		else
		{
			$status = $block->getStatus($pub, $manifest, $elementId);
		}

		// Return status
		return $status;

	}

	/**
	 * Loads a block
	 *
	 * @return  object
	 */
	public function loadBlock( $name, $blockId = 0, $new = false )
	{
		$signature = md5($name);

		if ((isset($this->_blocks[$signature])
			&& !($this->_blocks[$signature] instanceof __PHP_Incomplete_Class))
			&& $new === false)
		{
			return	$this->_blocks[$signature];
		}

		$elementClass = __NAMESPACE__ . '\\Block\\' . ucfirst($name);
		if (!class_exists($elementClass))
		{
			if (isset($this->_blockPath))
			{
				$dirs = $this->_blockPath;
			}
			else
			{
				$dirs = array();
			}

			$file = \JFilterInput::getInstance()->clean(str_replace('_', DS, $name).'.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = \JPath::find($dirs, $file))
			{
				include_once $elementFile;
			}
			else
			{
				$false = false;
				return $false;
			}
		}

		if (!class_exists($elementClass))
		{
			$false = false;
			return $false;
		}

		$this->_blocks[$signature] = new $elementClass($this);
		return $this->_blocks[$signature];
	}

	/**
	 * Get list of all available blocks
	 *
	 * @return  array  An array of all available blocks
	 */
	public function getBlocks( $select = '*', $where = '', $order = '')
	{
		return $this->_objBlock->getBlocks($select, $where, $order);
	}

	/**
	 * Get default block manifest
	 *
	 * @param   string  $name   	Name of block to render
	 * @return  object
	 */
	public function getManifest( $name, $new = false )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			$this->setError('Error loading block');
			return false;
		}
		else
		{
			return $block->getManifest($new);
		}
	}

	/**
	 * Get default element manifest for block
	 *
	 * @param   string  $name   	Name of block to render
	 * @return  object
	 */
	public function getElementManifest( $name )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			$this->setError('Error loading block');
			return false;
		}
		else
		{
			$manifest = $block->getManifest();
			if ($manifest->elements)
			{
				return $block->getElementManifest();
			}
		}
		return false;
	}

	/**
	 * Get block property
	 *
	 * @param   string  $property   	Name of property
	 * @return  object
	 */
	public function getBlockProperty( $name = NULL, $property = NULL )
	{
		if ($property === NULL)
		{
			return false;
		}

		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			return false;
		}
		else
		{
			return $block->getProperty($property);
		}
	}

	/**
	 * Transfers data
	 *
	 * @return  boolean
	 */
	public function transferData( $name, $manifest = NULL, $pub = NULL, $old, $new )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			return false;
		}
		else
		{
			return $block->transferData($manifest, $pub, $old, $new);
		}
	}

	/**
	 * Renders a block
	 *
	 * @param   string  $name   Name of block to render
	 * @param   string  $view   Name of rendering view (edit / curation / admin / review)
	 * @return  string HTML
	 */
	public function renderBlock( $name, $viewname = 'edit', $manifest = NULL, $pub = NULL, $blockId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		$html = '';
		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error rendering block');
			return false;
		}
		else
		{
			// Are we allowed to edit?
			$viewname = $viewname == 'edit'
						&& $this->checkFreeze($manifest->params, $pub)
						? 'freeze' : $viewname;

			// Render
			$html = $block->display($pub, $manifest, $viewname, $blockId);
		}

		return $html;
	}

	/**
	 * Check if changes are allowed
	 *
	 * @return  boolean
	 */
	public function checkFreeze($blockParams, $pub)
	{
		// Allow changes in non-draft version?
		$freeze 	= isset($blockParams->published_editing)
					 && $blockParams->published_editing == 0
					 && ($pub->state == 1 || $pub->state == 5 )
					? true : false;

		return $freeze;
	}

	/**
	 * Saves input in a block
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function saveBlock( $name, $manifest, $blockId, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->save($manifest, $blockId, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Reorders items in block/element
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function reorder( $name, $manifest, $blockId, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->reorder($manifest, $blockId, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Save block/element item
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function saveItem( $name, $manifest, $blockId, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->saveItem($manifest, $blockId, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Save block/element item
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function deleteItem( $name, $manifest, $blockId, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->deleteItem($manifest, $blockId, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Add item
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function addItem( $name, $manifest, $blockId, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->addItem($manifest, $blockId, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}
}
