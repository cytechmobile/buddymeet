=== BuddyMeet ===
Contributors: cytechltd
Tags: BuddyMeet, jitsi, video, conference, buddypress
Requires at least: 4.5
Tested up to: 6.5.2
Requires PHP: 5.3
Stable tag: 2.5.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Adds video and audio conferencing rooms to BuddyPress! Powered by Jitsi Meet!

== Description ==

BuddyMeet is a BuddyPress (2.5+) plugin that uses [Jitsi Meet](https://jitsi.org/jitsi-meet/) to allow the members of a community to participate into virtual conference rooms with video and audio capabilities. BuddyMeet's features include:

* A room where all members of a group can meet each other
* On demand rooms among specific invited group members
* Automatic customization of the room's subject and  the name/avatar of the participants
* Customization of all the parameters that [Jitsi Meet API](https://jitsi.github.io/handbook/docs/dev-guide/dev-guide-iframe) supports

Moreover, you can use the shortcode [buddymeet room=ROOM_HERE subject=SUBJECT_HERE] to add a conference room to any WordPress page. In that case, you have to pass any configuration by using the following shortcode parameters:

* domain: The domain of the Jitsi Meet installation. BuddyMeet uses by default the 8x8.vc service.
* room: The identifier of the room.
* subject: The subject of the room. If empty the room is being displayed as the subject.
* password: A password for the room. The first to enter the room sets that password and all other participants have to put it to enter.
* show_watermark: Whether to show the Jitsi.org watermark or not.
* show_brand_watermark: Whether to show a custom branded watermark or not.
* brand_watermark_link: the custom brand watermark to show.
* width: The width of the embedded window.
* height: The height of the embedded window.
* start_audio_only: Start the meet with the microphone only enabled and the camera off.
* film_strip_only: Start the meet in filmstrip only mode.
* disable_video_quality_label: Disable the video quality indicator.
* mobile_open_in_browser: Launch the meet directly within the browser in mobile devices without opening the jitsi mobile app.
* user: The user to display. Leave empty to automatically set the display name of the logged-in user.
* avatar: The url with the avatar to display. Leave empty to automatically set the avatar of the logged-in user.
* settings: A csv with the Jitsi Meet settings to get enabled. For the available options check [here](https://github.com/jitsi/jitsi-meet/blob/master/interface_config.js#L124)
* toolbar: A csv with the Jitsi Meet toolbar options to get enabled. For the available options check [here](https://github.com/jitsi/jitsi-meet/blob/master/config.js#L718).
* background_color: The background color of the window
* default_language: The default language of the Jitsi Meet interface.

BuddyMeet uses by default the 8x8.vc service which is maintained by the Jitsi team at 8x8. Upon the initialization of a room, BuddyMeet sends the following information to the service:

* The name of the current buddypress group as the subject of the call
* The name, the surname and the avatar of the currently logged-in user

However, if you want to use your own hosted installation of Jitsi Meet, you can just configure the corresponding domain via the "Manage" menu of a BuddyPress group. For more information about Jitsi Meet please follow the links below:

* [What is Jitsi?](https://jitsi.org)
* [FAQ](https://jitsi.org/user-faq)
* [Jitsi Meet API](https://github.com/jitsi/jitsi-meet/blob/master/doc/api.md)
* [Jitsi License](https://github.com/jitsi/jitsi/blob/master/LICENSE)
* [Jitsi Community Forum](https://community.jitsi.org/)
* [8x8 Terms and Conditions and Policies](https://www.8x8.com/terms-and-conditions)

This plugin is currently available only in english.

== Installation ==

You can download and install BuddyMeet using the built-in WordPress plugin installer. If you download BuddyMeet manually, make sure it is uploaded to "/wp-content/plugins/buddymeet/".

== Frequently Asked Questions ==

= If you have any question =

Use the support forum of this plugin.

= Jitsi Meet cannot access my microphone or camera =

Jitsi Meet uses your browser's API to ask for permissions to access your microphone or camera. In case you get an error that your device can not by accessed or used, please check one of the following:

* Another application uses the device.
* Your browsing context is insecure (that is, the page was loaded using HTTP rather than HTTPS).
* You denied access to your browser when you were asked for.
* You have denied globally access to all applications via your browser's configuration

= I cannot find the Settings page =

BuddyMeet is mainly a BuddyPress plugins. It actually extends the BuddyPress Groups component by adding a new BuddyMeet menu page as well as a settings page accessible via the Manage menu of the Group. However, if you want to use BuddyMeet in any other WordPress page you can use the [buddymeet] shortcode. In that case the plugin just adds a room in the respective page by using the passed configuration parameters.

= How can I create / switch among multiple rooms =

That functionality is accessible only from inside a BuddyPress Group. For more information please check the previous FAQ entry.

= Is it compatible with my theme? =

If you experience any UI issues you can override the templates of the plugin by copying the templates/group folder to your theme and then customizing them as you wish.

= JitsiMeetExternalAPI is not defined  =

If you get the above error please check if your site uses the "Rocket Loader" CloudFlare service. In that case you have to add - via your CloudFlare dashboard - a page rule with the setting "Rocket Loader". That will disable the service for the page that reports the error. For more information on how to add a page rule check [here](https://support.cloudflare.com/hc/en-us/articles/218411427).

= I activated BudddyMeet and BuddyPress, but I cannot see anything =

Please ensure that you have followed all instructions to properly setup BuddyPress (e.g. you have changed the default WordPress permalink settings). Also make sure that you have enabled the "Groups" component via the BuddyPress settings. After that create a group and in the creation wizard make sure you enabled BuddyMeet for that group.

= Branded watermark is not displayed =

Please note that this setting can only be used if you have set up your own Jitsi Meet server installation.

== Screenshots ==

1. BuddyMeet settings page
2. Meet all the group members
3. Invite a group member to a meet
4. Send the invitations to the added group members
5. Meet the invited members
6. Accept a meet invitation
7. Enter the room you was invited into
8. Switch among different rooms you have been invited into
9. Set the default Jitsi domain via the administration menu

== Changelog ==

= 2.5.0 =
* Removed the client disposal logic upon receiving the videoConferenceLeft event because this event is triggered when starting the login flow.

= 2.4.0 =
* Added compatibility with BuddyPress 12.0
* Added admin menu that enables WordPress administrators to set the default Jitsi domain. This domain will serve as the default for all newly created BuddyPress Groups and shortcodes. Users retain the option to override this value via group settings or shortcode parameters.

= 2.3.0 =

* Added short code input sanitization to prohibit possible XSS attacks.
* Updated all references to the documentation of Jitsi Meet settings and toolbar options.

= 2.2.0 =

* Changed the default public domain from meet.jit.si to 8x8.vc.

= 2.1.0 =

* Added the setting mobile_open_in_browser. When enabled the meet launches directly within the browser in mobile devices without opening the jitsi mobile app.

= 2.0.0 =

* Transferred the ownership of the plugin. Hereafter, the plugin will be actively maintained and further developed by Cytech - https://www.cytechmobile.com !!!
* Tested and updated compatibility with WordPress 6.1.1
* Added all the new Jitsi Meet toolbar and setting options by default
* Improved scripts loading that caused under specific condition the appearance of a blank page instead of the jitsi

= 1.8.0 =

* On self-hosted Jitsi domains, the participant needs to be a moderator before setting a password (Issue: https://community.jitsi.org/t/lock-failed-on-jitsimeetexternalapi/32060)

= 1.7.5 =

* Changed the format of the room names to alphanumeric ones so that they are compatible with the default Apache's rewrite rules when running a Jitsi Meet instance with Apache as the web server.

= 1.7.4 =

* Fixed mistakenly opening php tag

= 1.7.3 =

* Added an information message to let user know the call has been ended.
* Fixed a php warning when rendering the rooms a users has created.

= 1.7.2 =

* Fixed a small issue when the user hangs out the meet.

= 1.7.1 =

* Added a donation button to support the development effort.

= 1.7.0 =

* Updated the short code to work inside single posts
* Updated the short code to automatically set the user and avatar parameters when the user is logged in
* Updated the default settings to display the mistakenly removed password setting
* Added a listener to handle the hangs out event
* Added an extra option to hide/show the 'Meet Member' menu. When disabled, the submenu is not displayed and the users enter immediately the group room.
* Fixed various PHP warnings
* Added missing translations

= 1.6.0 =

* Added the missing legacy/home.php file

= 1.5.0 =

* Added support for BuddyPress themes that are based on the Legacy theme pack (and not on the Nouveau theme pack).
* Fixed the issue of disabling buttons that co-exist in the same page with the buddymeet short code
* Updated the pot file with all missing translations

= 1.4.0 =

* Added the show_brand_watermark and brand_watermark_link settings. You can now set a branded watermark if you use your own Jitsi Meet server.
* Fixed the activation process of the plugin
* Updated the autocomplete logic to use the built-in autocomplete script of the WordPress
* Updated the FAQ

= 1.3.0 =

* Fixed some Notice: Undefined index errors

= 1.2.0 =

* Major fix of the "Call to undefined function is_plugin_active" an issue that caused the plugin to break the WP Frontend
* Changed the default templates of the plugin to be compatible with the TwentyTwenty theme

= 1.1.0 =

* Fixed an issue causing the [buddymeet] short code not to work properly
* Added documentation about the configuration parameters of the [buddymeet] short code
* Updated the FAQ

= 1.0.0 =

* Initial version of the plugin

== Upgrade Notice ==

= 1.5.0 ==

In case you had overridden the templates of the plugin you might now have to move the home.php file under a legacy folder.

= 1.2.0 =

* After the update of the plugin to this version you need to deactivate and reactivate the plugin
