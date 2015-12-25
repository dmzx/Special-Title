<?php
/**
*
* @package phpBB Extension - Special Title
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\specialtitle\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\request\request				$request		Request object
	* @param \phpbb\template\template			$template		Template object
	* @param \phpbb\user						$user			User object
	* @param \phpbb\auth\auth					$auth			Auth object
	* @param \phpbb\db\driver\driver			$db				Database object
	* @param string							 	$phpbb_root_path		phpBB root path
	* @param string							 	$php_ext		phpEx
	*
	*/
	public function __construct(\phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext)
	{
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'							=> 'load_language_on_setup',
			'core.permissions'							=> 'add_permission',
			'core.acp_users_modify_profile'				=> 'acp_user_title_profile',
			'core.acp_users_profile_modify_sql_ary'		=> 'info_modify_sql_ary',
			'core.modify_username_string'				=> 'modify_username_string',
			'core.ucp_profile_modify_profile_info'		=> 'modify_profile_info',
			'core.ucp_profile_validate_profile_info'	=> 'validate_profile_info',
			'core.ucp_profile_info_modify_sql_ary'		=> 'info_modify_sql_ary',
		);
	}

	/**
	 * Load language on setup for special title
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'dmzx/specialtitle',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
	
	/**
	* Add permissions
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_specialtitle_use'] = array('lang' => 'ACL_U_SPECIALTITLE_USE', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}

	/**
	 * Allow admins to change special title
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function acp_user_title_profile($event)
	{
		// Request the user option vars and add them to the data array
		$event['data'] = array_merge($event['data'], array(
			'user_special_title'	=> $this->request->variable('user_special_title', $event['user_row']['user_special_title'], true),
			'user_special_title_colour'	=> $this->request->variable('user_special_title_colour', $event['user_row']['user_special_title_colour'], true),
		));

		$this->template->assign_vars(array(
			'SPECIAL_TITLE'			=> $event['data']['user_special_title'],
			'SPECIAL_TITLE_COLOUR'	=> $event['data']['user_special_title_colour'],
		));
	}

	public function modify_username_string($event)
	{
		$modes = array('profile' => 1, 'full' => 1);
		$mode = $event['mode'];
		if (!isset($modes[$mode]))
		{
			return;
		}
		$user_id = (int) $event['user_id'];
		if (
			!$user_id ||
			$user_id == ANONYMOUS ||
			($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'))
		)
		{
			return;
		}
		$username = $event['username'];

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$specialtitle =	$row['user_special_title'];
			$specialtitle_colour =	$row['user_special_title_colour'];

		}
		$this->db->sql_freeresult($result);

		$profile_url = append_sid("{$this->phpbb_root_path}memberlist.{$this->php_ext}", 'mode=viewprofile&amp;u=' . (int) $user_id);

		// Return profile
		if ($mode == 'profile')
		{
			$event['username_string'] = $profile_url;
			return;
		}

		$_profile_cache_special_title = '<a href="{PROFILE_URL}" style="color: {USERNAME_COLOUR};" class="username-coloured">{USERNAME}' . '</a><span class="specialtitle" style=" color: #' . $specialtitle_colour . ';" > ' . $specialtitle . '</span>';

		$event['username_string'] = str_replace(array('{PROFILE_URL}', '{USERNAME_COLOUR}', '{USERNAME}'), array($profile_url, $event['username_colour'], $event['username']), (!$event['username_colour']) ? $event['_profile_cache']['tpl_profile'] : $_profile_cache_special_title);
	}

	/**
	* Allow to change their special title
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_profile_info($event)
	{
		// Request the user option vars and add them to the data array
		$event['data'] = array_merge($event['data'], array(
			'user_special_title'		=> $this->request->variable('user_special_title', $this->user->data['user_special_title'], true),
			'user_special_title_colour'	=> $this->request->variable('user_special_title_colour', $this->user->data['user_special_title_colour'], true),
		));

		$this->template->assign_vars(array(
			'SPECIAL_TITLE'			=> $event['data']['user_special_title'],
			'USE_SPECIALTITLE'	 	=> $this->auth->acl_get('u_specialtitle_use'),
			'SPECIAL_TITLE_COLOUR'	=> $event['data']['user_special_title_colour'],
		));
	}

	/**
	* Validate changes to their special title
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function validate_profile_info($event)
	{
		$array = $event['error'];

		if (!function_exists('validate_data'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}
		$validate_array = array(
			'user_special_title'		=> array('string', true, 2, 8),
			'user_special_title_colour'	=> array('string', true, 3, 6),
		);
		$error = validate_data($event['data'], $validate_array);
		$event['error'] = array_merge($array, $error);
	}

	/**
	* Changed their title so update the database
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function info_modify_sql_ary($event)
	{
		$event['sql_ary'] = array_merge($event['sql_ary'], array(
			'user_special_title' 		=> $event['data']['user_special_title'],
			'user_special_title_colour' => $event['data']['user_special_title_colour'],
		));
	}
}
