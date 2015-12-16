<?php
/**
*
* @package phpBB Extension - Special Title
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\specialtitle\migrations;

class specialtitle_schema extends \phpbb\db\migration\migration
{

	public function update_data()
	{
		return array(

			// Add permissions
			array('permission.add', array('u_specialtitle_use')),

			// Set permissions
			array('permission.permission_set', array('ADMINISTRATORS', 'u_specialtitle_use', 'group')),
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_special_title' 		=> array('VCHAR:8', ''),
					'user_special_title_colour' => array('VCHAR:6', '008000'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'users'	=> array(
					'user_special_title',
					'user_special_title_colour',
				),
			),
		);
	}
}
