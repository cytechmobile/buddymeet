<?php
/**
 * BuddyMeet Component
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main BuddyMeet Component Class
 *
 * Inspired by BuddyPress skeleton component
 */
class BuddyMeet_Component extends BP_Component {
	/**
	 * Constructor method
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::start(
			buddymeet_get_slug(),
			buddymeet_get_name(),
			buddymeet_get_includes_dir()
		);

	 	$this->includes();
	 	$this->actions();
	}

	/**
	 * set some actions
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 */
	private function actions() {
		buddypress()->active_components[$this->id] = '1';
	}

	/**
	 * BuddyMeet needed files
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 *
	 * @uses bp_is_active() to check if group component is active
	 */
	public function includes( $includes = array() ) {
		// Files to include
		$includes = array();

		if ( bp_is_active( 'groups' ) ) {
			$includes[] = 'buddymeet-group-class.php';
		}

		parent::includes( $includes );
	}

	/**
	 * Set up BuddyMeet globals
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 *
	 * @global obj $bp BuddyPress's global object
	 * @uses buddypress() to get the instance data
	 * @uses buddymeet_get_slug() to get BuddyMeet root slug
	 */
	public function setup_globals( $args = array() ) {
		$bp = buddypress();

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'      => buddymeet_get_slug(),
			'root_slug' => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : buddymeet_get_slug(),
            'notification_callback' => array($this, 'format_notifications')
		);

		parent::setup_globals( $globals );
	}

    /**
     * Set up component navigation.
     *
     * @since 1.0.0
     *
     * @see BP_Component::setup_nav() for a description of arguments.
     *
     * @param array $main_nav Optional. See BP_Component::setup_nav() for
     *                        description.
     * @param array $sub_nav  Optional. See BP_Component::setup_nav() for
     *                        description.
     */
    public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
        global $groups_template;

        if ( bp_is_groups_component() && bp_is_single_item() ) {
            $group = ($groups_template->group) ? $groups_template->group : groups_get_current_group();
            $group_link = bp_get_group_permalink( $group );
            $slug = buddymeet_get_slug();
            $budddymeet_link = trailingslashit($group_link . '/' . $slug);

            $sub_nav[] = array(
                'name' => _x('Meet the Group', 'BuddyMeet group call screen sub nav', 'buddymeet'),
                'slug' => 'group',
                'parent_url' => $budddymeet_link,
                'parent_slug' => $slug,
                'screen_function' => 'buddymeet_screen_group',
                'position' => 20,
                'item_css_id' => 'buddymeet-screen-group'
            );

            $sub_nav[] = array(
                'name' => _x('Meet Members', 'BuddyMeet members call screen sub nav', 'buddymeet'),
                'slug' => 'members',
                'parent_url' => $budddymeet_link,
                'parent_slug' => $slug,
                'screen_function' => 'buddymeet_screen_members',
                'position' => 10,
                'item_css_id' => 'buddymeet-screen-members'
            );

            foreach ($sub_nav as $nav) {
                bp_core_new_subnav_item($nav, 'groups');
            }
        }
    }

    public function setup_actions() {
        parent::setup_actions();

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'wp_ajax_members_autocomplete', array($this,'members_autocomplete') );
        add_action( 'wp_ajax_members_add_to_invite_list', array($this,'members_add_to_invite_list') );
        add_action( 'wp_ajax_members_send_invites', array($this,'members_send_invites') );
        add_action( 'wp_ajax_members_delete_room', array($this,'members_delete_room') );
    }

    public function enqueue_styles(){
        global $bp;
        if(buddymeet_get_slug() === $bp->current_action){
            $sub_action = buddymeet_get_current_action();
            if( 'members' === $sub_action){
                //Enqueue the jquery autocomplete library
                wp_enqueue_style( 'buddymeet-invites-css', buddymeet_get_plugin_url() . "assets/css/invites.css", '', buddymeet_get_version(), 'screen'  );
            }
        }
    }

    public function enqueue_scripts(){
        global $bp;
        if(buddymeet_get_slug() === $bp->current_action){
            $sub_action = buddymeet_get_current_action();
            if('members' === $sub_action){
                wp_enqueue_script( 'buddymeet-jquery-autocomplete-js', buddymeet_get_plugin_url() . "assets/js/jquery.autocomplete-min.js", array( 'jquery' ) );
                wp_enqueue_script( 'buddymeet-invites-js', buddymeet_get_plugin_url()  . 'assets/js/invites.js', array( 'buddymeet-jquery-autocomplete-js' ) );
            }

            $handle = 'buddymeet-jitsi-js';
            wp_enqueue_script( $handle, "https://meet.jit.si/external_api.js", array(), buddymeet_get_version(), true);
        }
    }

    public function members_autocomplete() {
        global $bp;

        $group_id = absint($bp->groups->current_group->id);
        $search_terms =  isset($_REQUEST['query']) ? sanitize_text_field($_REQUEST['query']) : null;
        $room =   isset($_REQUEST['room']) ? sanitize_text_field($_REQUEST['room']) : null;

        $args = array(
            'group_id'   => $group_id,
            'group_role' => array( 'member', 'mod', 'admin', 'banned' ),
            'fields' => 'all'
        );
        if ( $search_terms ) {
            $args['search_terms'] = $search_terms;
        }

        $exclude = array(get_current_user_id());
        $room_members = groups_get_groupmeta($group_id, BuddyMeet::ROOM_MEMBERS_PREFIX . $room);
        if($room_members){
            $exclude = array_unique(array_merge($exclude, $room_members));
        }
        $args['exclude'] = $exclude;

        $group_members = groups_get_group_members( $args );
        $return = array(
            'query' 	=> $search_terms,
            'data' 		=> array(),
            'suggestions' 	=> array()
        );

        if($group_members && !empty($group_members)) {
            $suggestions = array();
            $data 	     = array();

            foreach ( $group_members['members'] as $user ) {
                $suggestions[] 	= $user->display_name . ' (' . $user->user_login . ')';
                $data[] 	= $user->ID;
            }

            $return['suggestions'] = $suggestions;
            $return['data']	       = $data;
        }

        die(json_encode( $return ));
    }

    public function members_add_to_invite_list() {
        check_ajax_referer( 'buddymeet_members_add_invite' );

        $member_id = isset($_POST['member_id']) && is_numeric($_POST['member_id']) ? absint($_POST['member_id']) : null;
        $member_action = isset($_POST['member_action']) ? sanitize_text_field($_POST['member_action']) : null;
        $group_id = isset($_POST['group_id']) && is_numeric($_POST['group_id']) ? absint($_POST['group_id']) : null;

        if (is_null($member_id)|| is_null($member_action) || is_null($group_id)){
            return false;
        }

        if ($member_action === 'add_invite') {
            $user = new BP_Core_User($member_id);
            echo sprintf(
                $this->get_invite_list_entry_template(),
                esc_attr($user->id),
                bp_core_fetch_avatar(array( 'item_id' => $user->id )),
                bp_core_get_userlink($user->id),
                esc_html($user->last_active),
                esc_html__('Remove Invite', 'buddymeet')
            );
        }

        die();
    }

    public function members_send_invites() {
        check_ajax_referer( 'buddymeet_send_invites' );

        $bp = buddypress();
        $group = $bp->groups->current_group;
        $group_id = $group->id;
        $requesting_user_id = get_current_user_id();

        $users = buddymeet_sanitize_request_array('users', 'absint');
        $room = isset($_REQUEST['room']) ? sanitize_text_field($_REQUEST['room']) : null;
        $room_name = isset($_REQUEST['room_name']) ? sanitize_text_field($_REQUEST['room_name']) : null;
        $current_user = get_current_user_id();

        if(is_null($room)){
            $users[] = $current_user;
        }

        if (!empty($users)) {
            $room = $this->add_users_to_room($group_id, $users, $room, $room_name)['id'];

            foreach ($users as $user_id) {
                if(bp_is_active( 'notifications' )) {
                    //send the notification
                    $notification_id = bp_notifications_add_notification(array(
                        'user_id' => $user_id,
                        'item_id' => $group->id,
                        'secondary_item_id' => $requesting_user_id,
                        'component_name' => buddymeet_get_slug(),
                        'component_action' => 'members_send_invites',
                        'allow_duplicate' => true
                    ));
                    bp_notifications_add_meta($notification_id, 'room', $room);
                }

                $group_link = bp_get_group_permalink( $group );
                $meet_link = $group_link . buddymeet_get_slug() . '/members/' . $room;

                $args = array(
                    'tokens' => array(
                        'group'          => $group,
                        'group.url'      => bp_get_group_permalink( $group ),
                        'group.name'     => $group->name,
                        'inviter.name'   => bp_core_get_userlink($requesting_user_id, true, false),
                        'inviter.url'    => bp_core_get_user_domain( $requesting_user_id ),
                        'inviter.id'     => $requesting_user_id,
                        'meet.url'    => esc_url( $meet_link  )
                    ),
                );

                bp_send_email( 'budymeet_send_invitation', (int) $user_id, $args );
            }
        }

        $return = array();
        $initialize = isset($_REQUEST['initialize']) ? $_REQUEST['initialize'] === "true" : false;
        if($initialize) {
            $return['redirect'] = esc_url($meet_link);
        }

        die(json_encode($return));
    }

    public function members_delete_room() {
        check_ajax_referer( 'buddymeet_members_delete_room' );

        $bp = buddypress();
        $group_id = $bp->groups->current_group->id;
        $user_id = get_current_user_id();
        $room =  isset($_REQUEST['room']) ? sanitize_text_field($_REQUEST['room']) : null;

        $this->remove_users_from_room($group_id, array($user_id), $room);

        $group_link = bp_get_group_permalink( $bp->groups->current_group );
        $meet_link = $group_link  . 'buddymeet/members/';
        $return = array('redirect' => esc_url($meet_link));
        die(json_encode($return));
    }

    public function format_notifications($action, $item_id, $secondary_item_id, $total_items, $format = 'string'){
        switch ( $action ) {
            case 'members_send_invites':
                $group_id = $item_id;

                $requested_user_id = $secondary_item_id;
                $current_user_id = get_current_user_id();
                $notification_id = bp_get_the_notification_id();
                $room = bp_notifications_get_meta($notification_id, 'room', true);

                $group = groups_get_group( $group_id );
                $user_fullname = bp_core_get_user_displayname( $requested_user_id );
                if($requested_user_id === $current_user_id){
                    $text = sprintf( __( '%s: You sent a meet request', 'buddymeet' ), $group->name );
                } else {
                    $text = sprintf( __( '%s: User %s sent you a meet request', 'buddymeet' ), $group->name, $user_fullname );
                }

                $group_link = bp_get_group_permalink( $group );

                $notification_link = $group_link . buddymeet_get_slug() . '/members/' . $room;

                bp_notifications_mark_notification( $notification_id, false );

                return apply_filters( 'buddymeet_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $group_link, $user_fullname, $group->name, $text, $notification_link );
                break;
            default:
                $custom_action_notification = apply_filters( 'buddymeet_' . $action . '_notification', null, $item_id, $secondary_item_id, $total_items, $format );

                if ( ! is_null( $custom_action_notification ) ) {
                    return $custom_action_notification;
                }

                break;
        }
    }

    public function get_invite_list_entry_template(){
        return '<li id="uid-%1$s">
                    %2$s
                    <h4>%3$s</h4>
                    <span class="activity">%4$s</span>
                    <div class="action">
                        <a class="remove" href="#" id="uid-%1$s">%5$s</a>
                    </div>
                </li>';
    }

    public function add_users_to_room($group_id, $users, $room_id = null, $room_name = null){
        //Add the room in the rooms list of each user
        $room =  array(
            'id' => $room_id === null ? wp_generate_uuid4() : $room_id,
            'name' => $room_name === null ? sprintf(__('Room %s'), time()) : $room_name
        );

        foreach($users as $user_id) {
            $user_rooms_option_key = BuddyMeet::USER_ROOMS_PREFIX . $user_id;
            $rooms = groups_get_groupmeta($group_id, $user_rooms_option_key);
            if($rooms){
                $rooms[] = $room;
            } else {
                $rooms = array($room);
            }
            groups_update_groupmeta($group_id, $user_rooms_option_key, $rooms);
        }

        //add the users as members of the current room
        $room_users_option_key = BuddyMeet::ROOM_MEMBERS_PREFIX . $room['id'];
        $current_users = groups_get_groupmeta($group_id, $room_users_option_key);
        if(!$current_users){
            $current_users = array_unique(array_merge($users, array(get_current_user_id())));
        } else {
            $current_users = array_unique(array_merge($users, $current_users));
        }
        groups_update_groupmeta($group_id, $room_users_option_key, $current_users);

        return $room;
    }

    public function remove_users_from_room($group_id, $users, $room_id){
        if(is_null($room_id)){
            return;
        }

        //delete room from all users
        foreach($users as $user_id){
            $user_rooms_option_key = BuddyMeet::USER_ROOMS_PREFIX . $user_id;
            $rooms = groups_get_groupmeta($group_id, $user_rooms_option_key);
            foreach($rooms as $index => $room){
                if($room['id'] === $room_id){
                    unset($rooms[$index]);
                    break;
                }
            }
            if(empty($rooms)){
                groups_delete_groupmeta($group_id, $user_rooms_option_key);
            } else {
                groups_update_groupmeta($group_id, $user_rooms_option_key, $rooms);
            }

            //remove users from room
            $room_members_option_key = BuddyMeet::ROOM_MEMBERS_PREFIX . $room_id;
            $members = groups_get_groupmeta($group_id, $room_members_option_key);
            foreach($members as $index => $member){
                if($member === $user_id){
                    unset($members[$index]);
                    break;
                }
            }
            if(empty($members)) {
                groups_delete_groupmeta($group_id, $room_members_option_key);
            } else {
                groups_update_groupmeta($group_id, $room_members_option_key, $members);
            }
        }
    }
}

/**
 * Loads the component into the $bp global
 *
 * @uses buddypress()
 */
function buddymeet_load_component() {
	buddypress()->buddymeet = new BuddyMeet_Component;
}
add_action( 'bp_loaded', 'buddymeet_load_component' );

/**
 * Registers the buddymeet shortcode
 * @param $params
 */
function buddymeet_shortcode($params) {
    $params = apply_filters('buddymeet_custom_settings', $params);
    $params = wp_parse_args($params, buddymeet_default_settings());

    $script = sprintf(
        buddymeet_get_jitsi_init_template(),
        $params['domain'],
        $params['settings'],
        $params['toolbar'],
        $params['room'],
        $params['width'],
        $params['height'],
        $params['parent_node'],
        $params['start_audio_only'] === "true" || $params['start_audio_only'] === true ? 1 : 0,
        $params['default_language'],
        $params['film_strip_only'] === "true" || $params['film_strip_only'] === true? 1 : 0,
        $params['background_color'],
        $params['show_watermark'] === "true" || $params['show_watermark'] === true? 1 : 0,
        $params['disable_video_quality_label'] === "true" || $params['disable_video_quality_label'] === true ? 1 : 0,
        $params['user'],
        $params['subject'],
        $params['avatar'],
        $params['password']
    );

    if(wp_doing_ajax()){
        //when initializing the meet via an ajax request we need to return the script to the caller to
        //add it in the page
        echo '<script>' . $script . '</script>';
    } else {
        $handle = "buddymeet-jitsi-js";
        wp_add_inline_script($handle, $script);
    }

    return '<div id="meet"></div>';
}

function buddymeet_get_jitsi_init_template(){
    return 'const domain = "%1$s";
            const settings = "%2$s"; 
            const toolbar = "%3$s"; 
            const options = {
                roomName: "%4$s",
                width: "%5$s",
                height: %6$d,
                parentNode: document.querySelector("%7$s"),
                configOverwrite: {
                    startAudioOnly: %8$b === 1,
                    defaultLanguage: "%9$s",
                },
                interfaceConfigOverwrite: {
                    filmStripOnly: %10$b === 1,
                    DEFAULT_BACKGROUND: "%11$s",
                    DEFAULT_REMOTE_DISPLAY_NAME: "",
                    SHOW_JITSI_WATERMARK: %12$b === 1,
                    SHOW_WATERMARK_FOR_GUESTS: %12$b === 1,
                    LANG_DETECTION: true,
                    CONNECTION_INDICATOR_DISABLED: false,
                    VIDEO_QUALITY_LABEL_DISABLED: %13$b === 1,
                    SETTINGS_SECTIONS: settings.split(","),
                    TOOLBAR_BUTTONS: toolbar.split(","),
                },
            };
            const api = new JitsiMeetExternalAPI(domain, options);
            api.executeCommand("displayName", "%14$s");
            api.executeCommand("subject", "%15$s");
            api.executeCommand("avatarUrl", "%16$s");
            api.addEventListener("videoConferenceJoined", function(event){
                api.executeCommand("password", "%17$s");
            });

            window.api = api;';
}

add_shortcode( 'buddymeet', 'buddymeet_shortcode');
