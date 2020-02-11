jQuery(document).ready( function() {
	let initializeMeet = true;
	const group_id = jQuery("input#group_id").val();

	autocomplete(jQuery('#room').val());

	jQuery('#send-invite-form #stopCall').on( 'click', function(e) {
		const data = {
			'action' : 'members_delete_room',
			'_wpnonce': jQuery("input#_wpnonce_members_delete_room").val(),
		};

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(data){
				jQuery('#room').val('');
				api.dispose();
				window.api = undefined;
				initializeMeet = true;
				autocomplete(null);
				buddymeet_refresh_buttons_state();
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
			'subject': jQuery('#subject').val(),
			'room': jQuery('#room').val(),
			'initialize': initializeMeet,
			'_wpnonce': jQuery("input#_wpnonce_send_invites").val(),
		};

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(data){
				if(data) {
					jQuery('#meet-wrapper').html(data);
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

	const room = jQuery('#room').val();
	if(room){
		jQuery('#submit').trigger('click');
		initializeMeet = false;
	}

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

		const invites = jQuery( '#meet-invite-list li' ).length;
		if ( invites ) {
			jQuery( '#submit' ).prop( 'disabled', false ).removeClass( 'submit-disabled' );
		} else {
			jQuery( '#submit' ).prop( 'disabled', true ).addClass( 'submit-disabled' );
		}

		if(typeof(api) != "undefined"){
			jQuery( '#stopCall' ).prop( 'disabled', false ).removeClass( 'submit-disabled' );
		} else {
			jQuery( '#stopCall' ).prop( 'disabled', true ).addClass( 'submit-disabled' );
		}
	}
});


