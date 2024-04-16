<?php
/**
Plugin Name: BuddyMeet
Plugin URI:
Description: Adds a meeting room with video and audio capabilities to BuddyPress. Powered by <a target="_blank" href="https://jitsi.org/"> Jitsi Meet </a>.
Version: 2.5.0
Requires at least: 4.6.0
Tags: buddypress
License: GPL V2
Author: Cytech <wp@cytech.gr>
Author URI: https://www.cytechmobile.com
Text Domain: buddymeet
Domain Path: /languages
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyMeet' ) ) :
/**
 * Main BuddyMeet Class
 */
class BuddyMeet {

    const USER_ROOMS_PREFIX = 'buddymeet_user_room_';
    const ROOM_MEMBERS_PREFIX = 'buddymeet_room_members_';
    const PUBLIC_JITSI_DOMAIN_OLD = 'meet.jit.si';
    const PUBLIC_JITSI_DOMAIN = '8x8.vc';

	private static $instance;

	/**
	 * Required BuddyPress version for the plugin.
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 *
	 * @var  string
	 */
	public static $required_bp_version = '2.5.0';

	/**
	 * BuddyPress config.
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public static $bp_config = array();

	/**
	 * Main BuddyMeet Instance
	 *
	 * Avoids the use of a global
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 *
	 * @uses BuddyMeet::setup_globals() to set the global needed
	 * @uses BuddyMeet::includes() to include the required files
	 * @uses BuddyMeet::setup_actions() to set up the hooks
	 * @return object the instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new BuddyMeet;
			self::$instance->setup_globals();
			self::$instance->includes();
			self::$instance->setup_actions();
		}
		return self::$instance;
	}


	private function __construct() { /* Do nothing here */ }

	/**
	 * Some usefull vars
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 *
	 * @uses plugin_basename()
	 * @uses plugin_dir_path() to build BuddyMeet plugin path
	 * @uses plugin_dir_url() to build BuddyMeet plugin url
	 */
	private function setup_globals() {
		$this->version    = '2.5.0';

		// Setup some base path and URL information
		$this->file       = __FILE__;
		$this->basename   = apply_filters( 'buddymeet_plugin_basename', plugin_basename( $this->file ) );
		$this->plugin_dir = apply_filters( 'buddymeet_plugin_dir_path', plugin_dir_path( $this->file ) );
		$this->plugin_url = apply_filters( 'buddymeet_plugin_dir_url',  plugin_dir_url ( $this->file ) );

		// Includes
		$this->includes_dir = apply_filters( 'buddymeet_includes_dir', trailingslashit( $this->plugin_dir . 'includes'  ) );
		$this->includes_url = apply_filters( 'buddymeet_includes_url', trailingslashit( $this->plugin_url . 'includes'  ) );

		// Languages
		$this->lang_dir  = apply_filters( 'buddymeet_lang_dir', trailingslashit( $this->plugin_dir . 'languages' ) );

		// BuddyMeet slug and name
		$this->buddymeet_slug = apply_filters( 'buddymeet_slug', 'buddymeet' );
		$this->buddymeet_name = apply_filters( 'buddymeet_name', 'BuddyMeet' );

		$this->domain           = 'buddymeet';
		$this->errors           = new WP_Error(); // Feedback
	}

	/**
	 * Ιncludes the needed files
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 *
	 * @uses is_admin() for the settings files
	 */
	private function includes() {
		require( $this->includes_dir . 'buddymeet-actions.php'         );
		require( $this->includes_dir . 'buddymeet-functions.php'       );

		//TODO CHECK ADMIN INTERFACES
		/*if( is_admin() ){
			require( $this->includes_dir . 'admin/buddymeet-admin.php' );
		}*/
	}


	/**
	 * The main hook used is bp_include to load our custom BuddyPress component
     *
     * @package BuddyMeet
	 * @since 1.0.0
	 */
	private function setup_actions() {
		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'buddymeet_activation'   );
		add_action( 'deactivate_' . $this->basename, 'buddymeet_deactivation' );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'bp_loaded',  array( $this, 'load_textdomain' ) );
		add_action( 'bp_include', array( $this, 'load_component'  ) );

        add_action( 'bp_setup_nav', array($this, 'set_default_groups_nav'), 20 );

        add_action( 'admin_menu', array($this, 'admin_menu'), 10 );
        add_action( 'admin_init', array($this, 'register_settings'), 10 );

        add_filter( 'buddymeet_custom_settings', array($this, 'buddymeet_post_settings'), 9 );

        add_shortcode( 'buddymeet', array($this, 'add_shortcode'));

		do_action_ref_array( 'buddymeet_after_setup_actions', array( &$this ) );

        add_filter( 'buddymeet_groups_get_groupmeta', array($this, 'buddymeet_migrate_groupmeta'), 10, 3 );
	}

    public function admin_menu(){
        $page_title = __( buddymeet_get_name(), 'buddymeet' );
        $menu_title =  __( buddymeet_get_name(), 'buddymeet' );
        $capability = 'manage_options';
        $menu_slug  = buddymeet_get_slug();
        $function   = array($this, 'display_admin_menu_page');
        $icon_url   = 'dashicons-video-alt3';
        $position   = 20;

        add_menu_page( $page_title,$menu_title,$capability,$menu_slug,$function,$icon_url,$position );
    }

    public function display_admin_menu_page(){ ?>
        <h1><?php echo get_admin_page_title();?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'buddymeet-settings' ); ?>
            <?php do_settings_sections( 'buddymeet-settings' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo __( 'Default Jitsi Domain', 'buddymeet' ); ?></th>
                    <td><input type="text" name="buddymeet_jitsi_domain" value="<?php echo BuddyMeet::get_default_jitsi_domain(); ?>"/></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    <?php }

    public function register_settings() {
        register_setting( 'buddymeet-settings', 'buddymeet_jitsi_domain' );
    }

    public function set_default_groups_nav() {
        bp_core_new_nav_default (
            array(
                'parent_slug'       => buddymeet(),
                'subnav_slug'       => 'members',
                'screen_function'   => 'buddymeet_screen_members'
            )
        );
    }

	/**
	 * Loads the translation
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 * @uses get_locale()
	 * @uses load_textdomain()
	 */
	public function load_textdomain() {
		$locale = apply_filters( 'buddymeet_load_textdomain_get_locale', get_locale(), $this->domain );
		$mofile = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );
		$mofile_global = WP_LANG_DIR . '/buddymeet/' . $mofile;

		if ( ! load_textdomain( $this->domain, $mofile_global ) ) {
			load_plugin_textdomain( $this->domain, false, basename( $this->plugin_dir ) . '/languages' );
		}
	}

    public function enqueue_styles(){
        if(function_exists( 'buddypress' )) {
            global $bp;
            if (buddymeet_get_slug() === $bp->current_action) {
                $sub_action = buddymeet_get_current_action();
                if ('members' === $sub_action) {
                    //Enqueue the jquery autocomplete library
                    wp_enqueue_style('buddymeet-invites-css', buddymeet_get_plugin_url() . "assets/css/invites.css", '', buddymeet_get_version(), 'screen');
                }
            }
        }

        wp_enqueue_style('buddymeet-css', buddymeet_get_plugin_url() . "assets/css/buddymeet.css", '', buddymeet_get_version(), 'screen');
    }

    public function enqueue_scripts(){
        $load_scripts = false;
        if(is_page() || is_single() || is_singular()){
            $post = get_post();
            if($post && has_shortcode($post->post_content, buddymeet_get_slug())){
                $load_scripts = true;
            } else if( function_exists( 'buddypress' )){
                global $bp;
                if(buddymeet_get_slug() === $bp->current_action){
                    $load_scripts = true;
                }
            }
        }

        $load_scripts = apply_filters( 'buddymeet_enqueue_scripts_load_scripts', $load_scripts );

        if($load_scripts){
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script( 'buddymeet-invites-js', buddymeet_get_plugin_url()  . 'assets/js/invites.js', array( 'jquery-ui-autocomplete' ) );
            wp_localize_script('buddymeet-invites-js', 'args', array(
                'ajaxurl' =>  admin_url( 'admin-ajax.php', 'relative' )
            ));

            $handle = 'buddymeet-jitsi-js';
            wp_enqueue_script( $handle, "https://8x8.vc/external_api.js", array(), buddymeet_get_version(), true);
        }
    }

	/**
	 * Finally, Load the component
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 */
	public function load_component() {
		if ( self::bail() ) {
			add_action( self::$bp_config['network_admin'] ? 'network_admin_notices' : 'admin_notices', array( $this, 'warning' ) );
		} else {
			require( $this->includes_dir . 'buddymeet-component-class.php' );
		}
	}

	/**
	 * Checks BuddyPress version
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 */
	public static function version_check() {
		// taking no risk
		if ( ! defined( 'BP_VERSION' ) )
			return false;

		return version_compare( BP_VERSION, self::$required_bp_version, '>=' );
	}

	/**
	 * Checks if your plugin's config is similar to BuddyPress
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 */
	public static function config_check() {
		/**
		 * blog_status    : true if your plugin is activated on the same blog
		 * network_active : true when your plugin is activated on the network
		 * network_status : BuddyPress & your plugin share the same network status
		 */
		self::$bp_config = array(
			'blog_status'    => false,
			'network_active' => false,
			'network_status' => true,
			'network_admin'  => false
		);

		$buddypress = false;

		if ( function_exists( 'buddypress' ) ) {
			$buddypress = buddypress()->basename;
		}

		if ( $buddypress && get_current_blog_id() == bp_get_root_blog_id() ) {
			self::$bp_config['blog_status'] = true;
		}

		$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

		// No Network plugins
		if ( empty( $network_plugins ) )
			return self::$bp_config;

		$buddymeet = plugin_basename( __FILE__ );

		// Looking for BuddyMeet
		$check = array( $buddymeet );

		// And for BuddyPress if set
		if ( ! empty( $buddypress ) )
			$check = wp_parse_args($check, $buddypress);

		// Are they active on the network ?
		$network_active = array_diff( $check, array_keys( $network_plugins ) );

		// If result is 1, your plugin is network activated
		// and not BuddyPress or vice & versa. Config is not ok
		if ( count( $network_active ) == 1 )
			self::$bp_config['network_status'] = false;

		self::$bp_config['network_active'] = isset( $network_plugins[ $buddymeet ] );

		// We need to know if the BuddyPress is network activated to choose the right
		// notice ( admin or network_admin ) to display the warning message.
		self::$bp_config['network_admin']  = ! empty( $buddypress ) && isset( $network_plugins[ $buddypress ] );

		return self::$bp_config;
	}

	/**
	 * Bail if BuddyPress config is different than this plugin
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 */
	public static function bail() {
		$retval = false;

		$config = self::config_check();

		if ( ! self::version_check() || ! $config['blog_status'] || ! $config['network_status'] )
			$retval = true;

		return $retval;
	}

	/**
	 * Display a warning message to admin
	 *
	 * @package BuddyMeet
	 * @since 1.0.0
	 */
	public function warning() {
		$warnings = $resolve = array();

		if ( ! self::version_check() ) {
			$warnings[] = sprintf( esc_html__( 'BuddyMeet requires at least version %s of BuddyPress.', 'buddymeet' ), self::$required_bp_version );
			$resolve[]  = sprintf( esc_html__( 'Upgrade BuddyPress to at least version %s', 'buddymeet' ), self::$required_bp_version );
		}

		if ( ! empty( self::$bp_config ) ) {
			$config = self::$bp_config;
		} else {
			$config = self::config_check();
		}

		if ( ! $config['blog_status'] ) {
			$warnings[] = esc_html__( 'BuddyMeet requires to be activated on the blog where BuddyPress is activated.', 'buddymeet' );
			$resolve[]  = esc_html__( 'Activate BuddyMeet on the same blog than BuddyPress', 'buddymeet' );
		}

		if ( ! $config['network_status'] ) {
			$warnings[] = esc_html__( 'BuddyMeet and BuddyPress need to share the same network configuration.', 'buddymeet' );
			$resolve[]  = esc_html__( 'Make sure BuddyMeet is activated at the same level than BuddyPress on the network', 'buddymeet' );
		}

		if ( ! empty( $warnings ) ) {
			// Give some more explanations to administrator
			if ( is_super_admin() ) {
				$deactivate_link = ! empty( $config['network_active'] ) ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
				$deactivate_link = '<a href="' . esc_url( $deactivate_link ) . '">' . esc_html__( 'deactivate', 'buddymeet' ) . '</a>';
				$resolve_message = '<ol><li>' . sprintf( __( 'You should %s BuddyMeet', 'buddymeet' ), $deactivate_link ) . '</li>';

				foreach ( (array) $resolve as $step ) {
					$resolve_message .= '<li>' . $step . '</li>';
				}

				if ( $config['network_status'] && $config['blog_status']  )
					$resolve_message .= '<li>' . esc_html__( 'Once done try to activate BuddyMeet again.', 'buddymeet' ) . '</li></ol>';

				$warnings[] = $resolve_message;
			}

		?>
		<div id="message" class="error">
			<?php foreach ( $warnings as $warning ) : ?>
				<p><?php esc_html_e($warning); ?></p>
			<?php endforeach ; ?>
		</div>
		<?php
		}
	}

    /**
     * Registers the buddymeet shortcode
     * @param $params
     */
    public function add_shortcode($params) {
        global $wp;
        $params = apply_filters('buddymeet_custom_settings', $params);
        $params = wp_parse_args($params, buddymeet_default_settings());
        $hangoutMessage = __("The video call has been ended.", "buddymeet");

        // sanitize short code input parameters for echoing in JS
        $params = array_map(function($item) {
            return esc_js($item);
        }, $params);

        $script = sprintf(
            $this->get_jitsi_init_template(),
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
            $params['show_brand_watermark'] === "true" || $params['show_brand_watermark'] === true? 1 : 0,
            $params['brand_watermark_link'],
            $params['disable_video_quality_label'] === "true" || $params['disable_video_quality_label'] === true ? 1 : 0,
            isset($params['user']) ? $params['user'] : '',
            $params['subject'],
            isset($params['avatar']) ? $params['avatar'] : '',
            isset($params['password']) ? $params['password'] : '',
            $hangoutMessage,
            $params['mobile_open_in_browser'] === "true" || $params['mobile_open_in_browser'] === true ? 1 : 0
        );

        if(wp_doing_ajax()){
            //when initializing the meet via an ajax request we need to return the script to the caller to
            //add it in the page
            echo '<script>' . $script . '</script>';
        } else {
            $handle = "buddymeet-jitsi-js";
            wp_enqueue_script($handle, "https://8x8.vc/external_api.js", array(), buddymeet_get_version(), true);
            wp_add_inline_script($handle, $script);
        }

        return '<div id="meet"></div>';
    }

    public function get_jitsi_init_template(){
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
                    deeplinking: {
                        disabled: %21$b === 1
                    }
                },
                interfaceConfigOverwrite: {
                    filmStripOnly: %10$b === 1,
                    DEFAULT_BACKGROUND: "%11$s",
                    DEFAULT_REMOTE_DISPLAY_NAME: "",
                    SHOW_JITSI_WATERMARK: %12$b === 1,
                    SHOW_WATERMARK_FOR_GUESTS: %12$b === 1,
                    SHOW_BRAND_WATERMARK: %13$b === 1,
                    BRAND_WATERMARK_LINK: "%14$s",
                    LANG_DETECTION: true,
                    CONNECTION_INDICATOR_DISABLED: false,
                    VIDEO_QUALITY_LABEL_DISABLED: %15$b === 1,
                    SETTINGS_SECTIONS: settings.split(","),
                    TOOLBAR_BUTTONS: toolbar.split(","),
                },
            };
            const api = new JitsiMeetExternalAPI(domain, options);
            api.executeCommand("displayName", "%16$s");
            api.executeCommand("subject", "%17$s");
            api.executeCommand("avatarUrl", "%18$s");
            
            /** 
             * Only moderators can set a password
             * Issue: https://community.jitsi.org/t/lock-failed-on-jitsimeetexternalapi/32060
             */
            api.addEventListener("participantRoleChanged", (event) => {
                if ("%19$s" && event.role === "moderator"){
                    api.executeCommand("password", "%19$s");
                }
            });

            window.api = api;';
    }

    public function buddymeet_post_settings($settings){
        $extra = array();
        if(is_page() || is_single()) {
            $post = get_post();
            if ($post && has_shortcode($post->post_content, buddymeet_get_slug())) {
                $user = wp_get_current_user();
                if($user->exists()) {
                    if (!array_key_exists('user', $settings)) {
                        $extra['user'] = $user->display_name;
                    }
                    if (!array_key_exists('avatar', $settings)) {
                        $extra['avatar'] = get_avatar_url($user->ID);
                    }
                }
            }
        }

        return wp_parse_args($extra, $settings);
    }

    public function buddymeet_migrate_groupmeta($value, $group_id, $key) {
        if ($value === BuddyMeet::PUBLIC_JITSI_DOMAIN_OLD) {
            return BuddyMeet::get_default_jitsi_domain();
        }
        return $value;
    }

    public static function get_default_jitsi_domain() {
        $domain = get_option('buddymeet_jitsi_domain', BuddyMeet::PUBLIC_JITSI_DOMAIN);
        if (empty($domain)) {
            $domain = BuddyMeet::PUBLIC_JITSI_DOMAIN;
        }
        return $domain;
    }
}

function buddymeet() {
	return buddymeet::instance();
}

buddymeet();

/**
 * BuddyMeet unistall Hook registration
 */
register_uninstall_hook( __FILE__, 'buddymeet_uninstall' );

endif;

