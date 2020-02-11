<?php
/**
 * BuddyMeet Actions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// BuddyPress / WordPress actions to BuddyMeet ones
add_action( 'bp_init',                  'buddymeet_init',                     14 );
add_action( 'bp_ready',                 'buddymeet_ready',                    10 );
add_action( 'bp_setup_current_user',    'buddymeet_setup_current_user',       10 );
add_action( 'bp_setup_theme',           'buddymeet_setup_theme',              10 );
add_action( 'bp_after_setup_theme',     'buddymeet_after_setup_theme',        10 );
add_action( 'bp_enqueue_scripts',       'buddymeet_register_scripts',          1 );
add_action( 'bp_admin_enqueue_scripts', 'buddymeet_register_scripts',          1 );
add_action( 'bp_enqueue_scripts',       'buddymeet_enqueue_scripts',          10 );
add_action( 'bp_setup_admin_bar',       'buddymeet_setup_admin_bar',          10 );
add_action( 'bp_actions',               'buddymeet_actions',                  10 );
add_action( 'bp_screens',               'buddymeet_screens',                  10 );
add_action( 'admin_init',               'buddymeet_admin_init',               10 );
add_action( 'admin_head',               'buddymeet_admin_head',               10 );

function buddymeet_init(){
	do_action( 'buddymeet_init' );
}

function buddymeet_ready(){
	do_action( 'buddymeet_ready' );
}

function buddymeet_setup_current_user(){
	do_action( 'buddymeet_setup_current_user' );
}

function buddymeet_setup_theme(){
	do_action( 'buddymeet_setup_theme' );
}

function buddymeet_after_setup_theme(){
	do_action( 'buddymeet_after_setup_theme' );
}

function buddymeet_register_scripts() {
	do_action( 'buddymeet_register_scripts' );
}

function buddymeet_enqueue_scripts(){
	do_action( 'buddymeet_enqueue_scripts' );
}

function buddymeet_setup_admin_bar(){
	do_action( 'buddymeet_setup_admin_bar' );
}

function buddymeet_actions(){
	do_action( 'buddymeet_actions' );
}

function buddymeet_screens(){
	do_action( 'buddymeet_screens' );
}

function buddymeet_admin_init() {
	do_action( 'buddymeet_admin_init' );
}

function buddymeet_admin_head() {
	do_action( 'buddymeet_admin_head' );
}