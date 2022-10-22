jQuery( document ).ready( function(){
	jQuery.ajaxSetup({
	  statusCode: {
	    400: function() {
	      app_ajax_log('400 status code! user error');
	    },
	    500: function() {
	      app_ajax_log('500 status code! server error');
	    }
	  },
	  ajaxStart : function(){
	  	jQuery('.disable-on-ajax-call').prop('disabled',true);
	  },
	  ajaxStop : function(){
	  	jQuery('.disable-on-ajax-call').prop('disabled',false);
	  }
	});

	jQuery('.do-clone').on('click',function(){
		jQuery('.clone-able .clone-item:first-child').clone().appendTo('.clone-able');
	});
});

function app_ajax_loading(){
	jQuery('#app-ajax-loading').show();
}
function app_ajax_stop(){
	jQuery('#app-ajax-loading').hide();
}
function app_ajax_flash(type,msg){

}
function app_ajax_log(msg){
	jQuery('#app-ajax-msg').html(msg);
}