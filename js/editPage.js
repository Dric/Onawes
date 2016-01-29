/**
 * Created by cedric.gallard on 29/01/2016.
 */
$('textarea').pagedownBootstrap({
	'editor_hooks': [{
		'event': 'insertImageDialog', 'callback': function (callback) {
			var $modal = $('#mediaManagerModal');
			$modal.on("show.bs.modal", function(e) {
				$(this).find(".modal-body").load($(this).data('ajaxtoload'), function(){
					$('.mediaInsert').on("click", function(e){
						e.preventDefault();
						$modal.modal('hide');
						callback($(this).data('image-id'));
					});
				});
			});
			$modal.modal('show');

			//callback("http://icanhascheezburger.files.wordpress.com/2007/06/schrodingers-lolcat1.jpg");
			return true; // tell the editor that we'll take care of getting the image url
		}
	}]
});
