<?php
/**
 * BuddyMeet functions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * What is the version of the plugin.
 *
 * @uses buddymeet()
 * @return string the version of the plugin
 */
function buddymeet_get_version() {
	return buddymeet()->version;
}

/**
 * Gets the slug of the plugin
 *
 * @uses buddymeet() to get plugin's globals
 * @uses buddypress() to get directory pages global settings
 * @return string the slug
 */
function buddymeet_get_slug() {
    $slug = isset( buddypress()->pages->buddymeet->slug ) ? buddypress()->pages->buddymeet->slug : buddymeet()->buddymeet_slug ;

    return apply_filters( 'buddymeet_get_slug', $slug );
}

/**
 * Gets the name of the plugin
 *
 * @uses buddymeet() to get plugin's globals
 * @uses buddypress() to get directory pages global settings
 * @return string the name
 */
function buddymeet_get_name() {
    $name = isset( buddypress()->pages->buddymeet->slug ) ? buddypress()->pages->buddymeet->title : buddymeet()->buddymeet_name ;

    return apply_filters( 'buddymeet_get_name', $name );
}


/**
 * What is the path to the includes dir ?
 *
 * @uses  buddymeet()
 * @return string the path
 */
function buddymeet_get_includes_dir() {
	return buddymeet()->includes_dir;
}

/**
 * What is the path of the plugin dir ?
 *
 * @uses  buddymeet()
 * @return string the path
 */
function buddymeet_get_plugin_dir() {
	return buddymeet()->plugin_dir;
}

/**
 * What is the url to the plugin dir ?
 *
 * @uses  buddymeet()
 * @return string the url
 */
function buddymeet_get_plugin_url() {
	return buddymeet()->plugin_url;
}

/**
 * Handles Plugin activation
 *
 * @uses bp_core_update_directory_page_ids() to update the BuddyPres component pages ids
 */
function buddymeet_activation() {
	// For network, as plugin is not yet activated, bail method won't help..
	if ( is_network_admin() && function_exists( 'buddypress' ) ) {
		$check = ! empty( $_REQUEST ) && 'activate' == $_REQUEST['action'] && $_REQUEST['plugin'] == buddymeet()->basename && bp_is_network_activated() && buddymeet::version_check();
	} else {
		$check = ! buddymeet::bail();
	}

	if ( empty( $check ) ) {
        return;
    }

    buddymeet_register_custom_email_templates();
    update_option('_buddymeet_enabled', true);

    do_action( 'buddymeet_activation' );
}

/**
 * Handles plugin deactivation
 */
function buddymeet_deactivation() {
	// Bail if config does not match what we need
	if ( buddymeet::bail() )
		return;

    update_option('_buddymeet_enabled', false);

	do_action( 'buddymeet_deactivation' );
}

/**
 * Handles plugin uninstall
 */
function buddymeet_uninstall() {
    update_option('_buddymeet_enabled', false);
}

/**
 * Checks plugin version against db and updates
 *
 * @uses buddymeet_get_version() to get BuddyMeet plugin version
 */
function buddymeet_check_version() {
	// Bail if config does not match what we need
	if ( buddymeet::bail() ) {
		return;
	}

	// Finally upgrade plugin version
	update_option( '_buddymeet_version', buddymeet_get_version() );
}
add_action( 'buddymeet_admin_init', 'buddymeet_check_version' );

function buddymeet_default_settings(){
    return array(
        'enabled' => true,
        'room' => '',
        'domain' => 'meet.jit.si',
        'film_strip_only' => false,
        'width' => '100%',
        'height' => 700,
        'start_audio_only' => false,
        'parent_node' => '#meet',
        'default_language' => 'en',
        'background_color' => '#464646',
        'show_watermark' => true,
        'settings' => 'devices,language',
        'disable_video_quality_label' => false,
        'toolbar' => 'microphone,camera,hangup,desktop,fullscreen,profile,chat,recording,settings,raisehand,videoquality,tileview'
    );
}

function buddymeet_groups_get_groupmeta($group_id, $meta_key, $default){
    $value = groups_get_groupmeta( $group_id, $meta_key, true);

    if($value === false || $value === ""){
        $value = $default;
    }

    return $value === "1" ? true : ($value === "0" ? false : $value);
}

function buddymeet_groups_update_groupmeta($group_id, $meta_key, $default){
    $value = sanitize_text_field( $_POST[$meta_key] );
    if(!$value){
        $value = $default;
    }

    groups_update_groupmeta( $group_id, $meta_key, $value );
}

function buddymeet_get_current_action(){
    $bp = buddypress();
    $action = 'members';

    $actions = bp_action_variables();
    if(!empty($actions)){
        $action = $actions[0];
    }
    $bp->action_variables = array($action);
    return $action;
}

function buddymeet_get_current_room(){
    global $wp;

    $group_id = bp_get_group_id();
    $user_id = get_current_user_id();
    $path_params = array_keys($wp->query_vars);
    $room_option_key = BuddyMeet::OPTION_PREFIX_MEET_ROOM . $user_id;

    if(count($path_params) > 3){
        $room = $path_params[2];
        groups_update_groupmeta($group_id, $room_option_key, $room);
    } else {
        $room = buddymeet_groups_get_groupmeta($group_id, $room_option_key, '');
    }
    return $room;
}

function buddymeet_register_custom_email_templates() {

    // Do not create if it already exists and is not in the trash
    $post_exists = post_exists( '[{{{site.name}}}] You have a new meet request in group: {{group.name}}"' );

    if ( $post_exists != 0 && get_post_status( $post_exists ) == 'publish' ) {
        return;
    }

    // Create post object
    $my_post = array(
        /* translators: do not remove {} brackets or translate its contents. */
        'post_title'   => __( '[{{{site.name}}}] You have a new meet request in group: {{group.name}}', 'buddymeet' ),
        /* translators: do not remove {} brackets or translate its contents. */
        'post_content' => __( "<a href=\"{{{inviter.url}}}\">{{inviter.name}}</a> has invited you to join a meet as member of the group &quot;{{group.name}}&quot;. <a href=\"{{{meet.url}}}\">\nGo here to enter the meet</a> or <a href=\"{{{group.url}}}\">visit the group</a> to learn more.", 'buddymeet' ),
        /* translators: do not remove {} brackets or translate its contents. */
        'post_excerpt' => __( "{{inviter.name}} has invited you to join a meet as member of the group \"{{group.name}}\". To join the meet, visit: {{{meet.url}}}. To learn more about the group, visit: {{{group.url}}}. To view {{inviter.name}}'s profile, visit: {{{inviter.url}}}", 'buddymeet' ),
        'post_status'   => 'publish',
        'post_type' => bp_get_email_post_type() // this is the post type for emails
    );

    // Insert the email post into the database
    $post_id = wp_insert_post( $my_post );

    if ( $post_id ) {
        // add our email to the taxonomy term 'post_received_comment'
        // Email is a custom post type, therefore use wp_set_object_terms

        $tt_ids = wp_set_object_terms( $post_id, 'budymeet_send_invitation', bp_get_email_tax_type() );
        foreach ( $tt_ids as $tt_id ) {
            $term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
            wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
                'description' => 'A member sent a meet request.',
            ) );
        }
    }
}

/**
 * Check if buddymeet plugin is enabled for a specific group or generally in the site.
 *
 * @param bool $group_id
 * @return bool
 */

function buddymeet_is_enabled($group_id = false){
    if($group_id){
        $enabled = get_option('_buddymeet_enabled') && groups_get_groupmeta($group_id, 'buddymeet_enabled', true);
    } else {
        $enabled = get_option('_buddymeet_enabled') === "1";
    }
    return $enabled;
}