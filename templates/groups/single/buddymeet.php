<?php
$action = buddymeet_get_current_action();
?>
    <div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'BuddyMeet secondary navigation', 'buddymeet' ); ?>" role="navigation">
        <ul>
            <?php bp_get_options_nav(buddymeet_get_slug()); ?>
        </ul>
    </div>
<?php


switch ( $action ) {
    case 'group' :
        bp_get_template_part('groups/single/group-meet');
        break;
    case 'members' :
        bp_get_template_part('groups/single/members-meet');
}

