=== BuddyMeet ===
Contributors: tdakanalis
Tags: BuddyMeet, jitsi, video, audio, conference, buddypress
Requires at least: 4.5
Tested up to: 5.3.2
Requires PHP: 5.3
Stable tag: 1.0.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds video and audio conferencing rooms to BuddyPress! Powered by Jitsi Meet!

== Description ==

BuddyMeet is a BuddyPress (2.5+) plugin that uses [Jitsi Meet](https://jitsi.org/jitsi-meet/) to allow the members of a community to participate into virtual conference rooms with video and audio capabilities. BuddyMeet's features include:

* A room where all members of a group can meet each other
* On demand rooms among specific invited group members
* Automatic customization of the room's subject and  the name/avatar of the participants
* Customization of all the paremeters that [Jitsi Meet API](https://github.com/jitsi/jitsi-meet/blob/master/doc/api.md) supports
* The [buddymeet] short code to add a conference room to any wordpress page

BuddyMeet uses by default the meet.jit.si service which is maintained by the Jitsi team at 8x8. Upon the initialization of a room, BuddyMeet sends the following information to the service:

* The name of the current buddypress group as the subject of the call
* The name, the surname and the avatar of the currently logged in user

However, if you want to use your own hosted installation of Jitsi Meet, you can just configure the corresponding domain via the "Manage" menu of a BuddyPress group. For more information about Jitsi Meet please follow the links below:

* [What is Jitsi?](https://jitsi.org)
* [FAQ](https://jitsi.org/user-faq)
* [Jitsi Meet API](https://github.com/jitsi/jitsi-meet/blob/master/doc/api.md)
* [Jitsi License](https://github.com/jitsi/jitsi/blob/master/LICENSE)
* [Jitsi Community Forum](https://community.jitsi.org/)
* [8x8 Terms and Conditions and Policies](https://www.8x8.com/terms-and-conditions)

This plugin is currently available only in english.

== Installation ==

You can download and install BuddyMeet using the built in WordPress plugin installer. If you download BuddyMeet manually, make sure it is uploaded to "/wp-content/plugins/buddymeet/".

== Frequently Asked Questions ==

= If you have any question =

Use the support forum of this plugin.

= Jitsi Meet cannot access my microphone or camera =

Jitsi Meet uses your browser's API to ask for permissions to access your microphone or camera. In case you get an error that your device can not by accessed or used, please check one of the following:

* Another application uses the device.
* Your browsing context is insecure (that is, the page was loaded using HTTP rather than HTTPS).
* You denied access to your browser when you were asked for.
* You have denied globally access to all applications via your browser's configuration

== Screenshots ==

1. BuddyMeet settings page
2. Meet all the group members
3. Invite a group member to a meet
4. Send the invitations to the added group members
5. Meet the invited members
6. Accept a meet invitation
7. Enter the room you was invited into
8. Switch among different rooms you have been invited into

== Changelog ==

= 1.0.0 =

* Initial version of the plugin

== Upgrade Notice ==

Nothing