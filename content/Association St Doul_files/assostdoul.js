jQuery(document).ready(function ($) {
	/** Gestion des infobulles
	* Une infobulle peut être affichée à gauche, à droite, en haut et en bas du conteneur auquel elle est reliée.
	* Il suffit pour celà d'employer les classes suivantes :
	* - tooltip-left
	* - tooltip-right
	* - tooltip-top
	* - tooltip-bottom (défaut)
	* Il faut également renseigner une balises title ou alt.
	*/
  $('.tooltip-right[title]').tooltip({placement: 'right'});
  $('.tooltip-left[title]').tooltip({placement: 'left'});
  $('.tooltip-bottom[title]').tooltip({placement: 'bottom'});
  $('.tooltip-top[title]').tooltip({placement: 'top'});
  $('.tooltip-right[alt]').tooltip({placement: 'right', title: function(){return $(this).attr('alt');}});
  $('.tooltip-left[alt]').tooltip({placement: 'left', title: function(){return $(this).attr('alt');}});
  $('.tooltip-bottom[alt]').tooltip({placement: 'bottom', title: function(){return $(this).attr('alt');}});
  $('.tooltip-top[alt]').tooltip({placement: 'top', title: function(){return $(this).attr('alt');}});
  $('a').tooltip({placement: 'bottom'});
	
	$('#nav ul li a').bind('click', function(e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: $(this.hash).offset().top - 58 }, 300);
	});
	
	// Javascript to enable link to tab
	var hash = document.location.hash;
	var prefix = "tab_";
	if (hash) {
	    $('.nav-tabs a[href='+hash.replace(prefix,"")+']').tab('show');
	} 

	// Change hash for page-reload
	$('.nav-tabs a').on('shown', function (e) {
	    window.location.hash = e.target.hash.replace("#", "#" + prefix);
	});
	if (is_admin){
		$('[data-toggle="confirmation"]').confirmation({singleton:true, popout:true, title: 'Êtes-vous sûr(e) ?', btnOkClass: 'btn', btnOkLabel: 'Oui', btnCancelLabel: 'Non'});
		$('.img-ajax').each(function(){
			var cat = $(this).data('cat');
			$(this).load('admin.php', {ajax:'img_list', cat: cat}, function(){
				$('[data-toggle="confirmation"]').confirmation({singleton:true, popout:true, title: 'Êtes-vous sûr(e) ?', btnOkClass: 'btn', btnOkLabel: 'Oui', btnCancelLabel: 'Non'});
			});
		});
		
	}else{
		function moveScroller() {
			var move = function() {
				var st = $(window).scrollTop();
				var ot = $("#sports-logos-anchor").offset().top;
				var s = $("#sports-logos");
				if(st > ot) {
					s.css({
						position: "fixed",
						top: "2px"
					});
				} else {
					if(st <= ot) {
						s.css({
							position: "relative",
							top: ""
						});
					}
				}
			};
			$(window).scroll(move);
			move();
		}
		moveScroller();
		//$("#loadingDiv").show();
		var nImages = $(".carousel-inner").length;
		var loadCounter = 0;
		$('.carousel').carousel();

		$(".item img").on("load", function() {
		    loadCounter++;
		    if(nImages == loadCounter) {
						$("#carousel-loading").hide();
		        $('#carousel').fadeIn(600);
		    }
		}).each(function() {

		    // attempt to defeat cases where load event does not fire
		    // on cached images
		    if(this.complete) $(this).trigger("load");
		});
		/*$('#carousel').load('index.php', {ajax: 'carousel'}, function(){
			
			//$('#carousel').fadeIn(200);
		});*/
		$(".button").click(function() {  
	    // validate and process form here  
	      
	    $('.error').hide();  
	      var name = $("input#name").val();  
	        if (name == "") {  
	      $("label#name_error").show();  
	      $("input#name").focus();  
	      return false;  
	    }  
	        var email = $("input#email").val();  
	        if (email == "") {  
	      $("label#email_error").show();  
	      $("input#email").focus();  
	      return false;  
	    }  
	        var phone = $("input#phone").val();  
	        if (phone == "") {  
	      $("label#phone_error").show();  
	      $("input#phone").focus();  
	      return false;  
	    }  
	      
	  });
	}  
});
