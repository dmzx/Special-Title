<?php
/**
*
* @package phpBB Extension - Special Title
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACL_U_SPECIALTITLE_USE'		=> 'Kan speciale titel gebruiken',
	'SPECIAL_TITLE'				 	=> 'Speciale titel',
	'SPECIAL_TITLE_EXPLAIN'		 	=> '<em>korte titel naast gebruikersnaam (max. 8 karakters)</em>',
	'SPECIAL_TITLE_COLOUR'			=> 'Speciale titel kleur',
	'SPECIAL_TITLE_COLOUR_EXPLAIN'	=> '<em>Selecteer een kleur uit het palet, of voer direct een kleurcode in.</em>',
));