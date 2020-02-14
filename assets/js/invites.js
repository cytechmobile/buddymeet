jQuery(document).ready( function() {
	const room = jQuery('#room').val();
	let initializeMeet = room === '' || room === undefined;
	const group_id = jQuery("input#group_id").val();

	autocomplete(jQuery('#room').val());

	jQuery('#send-invite-form #stopCall').on( 'click', function(e) {
		const data = {
			'action' : 'members_delete_room',
			'room' :  jQuery('#room').val(),
			'_wpnonce': jQuery("input#_wpnonce_members_delete_room").val(),
		};

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(response){
				const url = JSON.parse(response);
				if(url.redirect) {
					window.location = url.redirect;
				}
			},
			error: function(data){
			}
		});

		e.preventDefault();

	});

	jQuery('#send-invite-form #submit').on( 'click', function(e) {
		const users = [];

		jQuery('#meet-invite-list').find('li').each(function(index,value) {
			users.push( jQuery(this).attr('id').split('-')[1] );
			jQuery(this).addClass('autocomplete-loading');
		});

		// set ajax data
		const data = {
			'action' : 'members_send_invites',
			'users': users,
			'room_name': jQuery('#room_name').val(),
			'room': jQuery('#room').val(),
			'initialize': initializeMeet,
			'_wpnonce': jQuery("input#_wpnonce_send_invites").val(),
		};

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(response){
				if(response) {
					const url = JSON.parse(response);
					if(url.redirect){
						window.location = url.redirect;
					}
				}
				jQuery('#meet-invite-list').find('li').remove();

				//we only initialize the meet once until it gets explicitly closed
				initializeMeet = false;
				autocomplete(jQuery('#room').val());
				buddymeet_refresh_buttons_state();
			},
			error: function(data){
			}
		});

		e.preventDefault();

	});

	jQuery('#active-rooms').on( 'change', function(e) {
		const room =  jQuery('#active-rooms').val();
		let location = window.location.href;
		location = location.substr(0, location.indexOf('buddymeet/')) + 'buddymeet/members/';
		window.location = location + room;

		e.preventDefault();
	});


	jQuery('#room_name').on( 'keyup', function(e) {
		buddymeet_refresh_buttons_state();
	});

	buddymeet_refresh_buttons_state();

	function autocomplete(room){
		const options = {
			serviceUrl: ajaxurl,
			width: 300,
			delimiter: /(,|;)\s*/,
			onSelect: buddymeet_on_autocomplete_select,
			deferRequestBy: 500,
			params: { action: 'members_autocomplete', room:  room},
			noCache: true //set to true, to disable caching
		};

		jQuery('#send-invite-form #send-to-input').autocomplete(options);
	}

	function buddymeet_on_autocomplete_select(value, data ) {
		// Put the item in the invite list
		jQuery('div.item-list-tabs li.selected').addClass('loading');

		jQuery.post( ajaxurl, {
				action: 'members_add_to_invite_list',
				'member_action': 'add_invite',
				'_wpnonce': jQuery("input#_wpnonce_members_add_invite").val(),
				'member_id': data,
				'group_id': group_id
			},
			function(response) {
				jQuery('.ajax-loader').toggle();

				if ( '0' !== response ) {
					jQuery('#meet-invite-list').append(response);
					jQuery("#message").hide();

					jQuery('.action a').click(function(){
						jQuery(this).closest('li').remove();
						buddymeet_refresh_buttons_state();
					});
				}

				jQuery('div.item-list-tabs li.selected').removeClass('loading');

				// Refresh the submit button state
				buddymeet_refresh_buttons_state();
			});

		// Remove the value from the send-to-input box
		jQuery('#send-to-input').val('');
	}

	function buddymeet_refresh_buttons_state(){

		const hasInvites = jQuery( '#meet-invite-list li' ).length;
		const roomName = jQuery( '#room_name' ).val();
		const hasRoomName = roomName !== '' && roomName !== undefined;
		if ( hasInvites  && hasRoomName) {
			jQuery( '#submit' ).prop( 'disabled', false ).removeClass( 'submit-disabled' );
		} else {
			jQuery( '#submit' ).prop( 'disabled', true ).addClass( 'submit-disabled' );
		}

		if(typeof(api) != "undefined"){
			jQuery( '#stopCall' ).prop( 'disabled', false ).removeClass( 'submit-disabled' );
			jQuery( '#room_name' ).prop( 'disabled', true );
		} else {
			jQuery( '#stopCall' ).prop( 'disabled', true ).addClass( 'submit-disabled' );
			jQuery( '#room_name' ).prop( 'disabled', false );
		}
	}
});


