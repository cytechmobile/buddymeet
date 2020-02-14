<?php
if ( !defined( 'ABSPATH' ) ) exit;
global $bp;
$room = buddymeet_get_current_user_room();
do_action( 'bp_before_group_meet_member_content' ) ?>

<form action="#" method="post" id="send-invite-form">

    <div id="meet-wrapper" class="members-meet-parent">
        <?php if($room):?>
            <?php buddymeet_render_jitsi_meet($room['id'], $room['name']); ?>
            <input type="hidden" name="room" id="room" value="<?php esc_attr_e($room['id']); ?>" />
        <?php endif;?>
    </div>
    <?php wp_nonce_field( 'buddymeet_members_delete_room', '_wpnonce_members_delete_room' ) ?>

    <div class="left-menu">
        <div style="float: left;">
            <p><?php _e("Room Name",'buddymeet') ?></p>
            <ul class="first">
                <li>
                    <input type="text" name="room_name" id="room_name" value="<?php esc_attr_e($room['name']) ?>"/>
                </li>
            </ul>
        </div>
        <div style="float: right; width: 80%">
            <p><?php _e("Search for members to invite in the room",'buddymeet') ?></p>
            <ul class="first">
                <li>
                    <input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
                </li>
            </ul>
        </div>

        <?php wp_nonce_field( 'buddymeet_members_add_invite', '_wpnonce_members_add_invite' ) ?>
    </div>

    <div id="members-invite-list" class="members-invite-list">
        <?php do_action( 'bp_before_group_meet_member_list' ) ?>

        <ul id="meet-invite-list" class="item-list"></ul>

        <?php do_action( 'bp_after_group_meet_member_list' ) ?>

        <div class="submit">
            <input type="submit" name="stopCall" id="stopCall" class="submit-btn" value="<?php _e( 'Leave Room','buddymeet' ) ?>" />
            <input type="submit" name="submit" id="submit" class="submit-btn" value="<?php _e( 'Send Invites','buddymeet' ) ?>" />
        </div>
    </div>

    <?php wp_nonce_field( 'buddymeet_send_invites', '_wpnonce_send_invites') ?>

    <input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" />

</form>

<?php do_action( 'bp_after_group_meet_member_content' ) ?>



