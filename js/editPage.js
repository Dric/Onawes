/**
 * Created by cedric.gallard on 29/01/2016.
 */
$('textarea').pagedownBootstrap({
	'editor_hooks': [{
		'event': 'insertImageDialog', 'callback': function (callback) {
			var $modal = $('#mediaManagerModal');
			$modal.on("show.bs.modal", function(e) {
				var libraryUrl = $(this).data('ajaxlibrary');
				$(this).find(".modal-body").load($(this).data('ajaxtoload'), function(){
					$('#upload-media').fileinput().on('fileloaded', function(event, file, previewId, index, reader) {
						$('.fileinput-upload-button').hide();
					}).on('fileuploaded', function(event, data, previewId, index) {
						/*var form = data.form, files = data.files, extra = data.extra,
							response = data.response, reader = data.reader;*/
						$('#library').load(libraryUrl, function(){
							mediaActions($modal, callback);
						});
					});
					mediaActions($modal, callback);
				});
			});
			$modal.modal('show');

			//callback("http://icanhascheezburger.files.wordpress.com/2007/06/schrodingers-lolcat1.jpg");
			return true; // tell the editor that we'll take care of getting the image url
		}
	}]
});

function mediaActions($modal, callback){
	$('.mediaInsert').on("click", function(e){
		e.preventDefault();
		$modal.modal('hide');
		callback($(this).data('file-id'));
	});
	$('.mediaDelete').on("click", function(e){
		e.preventDefault();
		var trName = '#' + $(this).data('tr-name');
		$.post( $(this).data('delete-url'), { fileId : $(this).data('file-id')}).done(function(data){
			if (data.ok){
				$(trName).fadeOut();
			}
		});
	});
}
