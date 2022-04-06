//Public Script
(function( $ ) {
	'use strict';
	let ajax_url = wc_order_ajax_object.ajax_url;

	jQuery(document).on("change","select#pt-filter-by-date", function(){
		let date = jQuery(this).val();
		let form_data = new FormData();
		form_data.append('action', 'woocommerce_order_filter');
		form_data.append('date', date);

		//Ajax Request Handling
		jQuery.ajax({
			url: ajax_url,
			type: 'POST',
			contentType: false,
			processData: false,
			data: form_data,
			success: function (response) {
				jQuery("table.shop_table.shop_table_responsive.my_account_orders tbody").html(`<div>${response}<div>`);
			}
		});

	});
























})( jQuery );
