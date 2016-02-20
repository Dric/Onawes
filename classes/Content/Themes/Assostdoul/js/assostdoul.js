/**
 * Created by Dric on 15/02/2016.
 */
moveScroller();
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

$('ul.navbar-nav li a').bind('click', function(e) {
	e.preventDefault();
	$('html, body').animate({ scrollTop: $(this.hash).offset().top - 58 }, 300);
});