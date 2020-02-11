<?php
if ( !defined( 'ABSPATH' ) ) exit;

global $bp;
?>

<div class="group-meet">
    <?php
    $group_id = $bp->groups->current_group->id;
    $group_name = esc_js($bp->groups->current_group->name);
    $user_name = esc_js($bp->loggedin_user->userdata->display_name);
    $avatar_url = esc_js(bp_get_loggedin_user_avatar( 'html=false' ));

    //apply group settings
    $room = groups_get_groupmeta( $group_id, 'buddymeet_room', true);
    $password = groups_get_groupmeta( $group_id, 'buddymeet_password', true);

    $domain =  groups_get_groupmeta( $group_id, 'buddymeet_domain', true);
    $film_strip_only =  groups_get_groupmeta( $group_id, 'buddymeet_film_strip_only', true) === '1' ?  'true' : 'false';
    $width =  groups_get_groupmeta( $group_id, 'buddymeet_width', true);
    $height =  groups_get_groupmeta( $group_id, 'buddymeet_height', true);
    $start_audio_only =  groups_get_groupmeta( $group_id, 'buddymeet_start_audio_only', true) === '1' ? 'true' : 'false';
    $default_language =  groups_get_groupmeta( $group_id, 'buddymeet_default_language', true);
    $background_color =  groups_get_groupmeta( $group_id, 'buddymeet_background_color', true);
    $show_watermark =  groups_get_groupmeta( $group_id, 'buddymeet_show_watermark', true)  === '1' ? 'true' : 'false';
    $disable_video_quality_label =  groups_get_groupmeta( $group_id, 'buddymeet_disable_video_quality_label', true) === '1' ? 'true' : 'false';
    $settings =  groups_get_groupmeta( $group_id, 'buddymeet_settings', true);
    $toolbar =  groups_get_groupmeta( $group_id, 'buddymeet_toolbar', true);

    $content = '[buddymeet 
            room = "' . $room . '" 
            subject = "' . $group_name . '"
            user = "' . $user_name . '"
            avatar = "' . $avatar_url . '"
            password = "' . $password . '"
            domain = "' . $domain . '"
            film_strip_only = "' . $film_strip_only . '"
            width = "' . $width . '"
            height = "' . $height . '"
            start_audio_only = "' . $start_audio_only . '"
            default_language = "' . $default_language . '"
            background_color = "' . $background_color . '"
            show_watermark = "' . $show_watermark . '"
            disable_video_quality_label = "' . $disable_video_quality_label . '"
            settings = "' . $settings . '"
            toolbar = "' . $toolbar . '"
        ]';

    echo do_shortcode($content);
    ?>
</div>