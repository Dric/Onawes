/**
 * Created by Dric on 08/04/2015.
 */

/** Gestion des infobulles
 * Une infobulle peut être affichée à gauche, à droite, en haut et en bas du conteneur auquel elle est reliée.
 * Il suffit pour cela d'employer les classes suivantes :
 * - tooltip-left
 * - tooltip-right
 * - tooltip-top
 * - tooltip-bottom (défaut pour les balises a)
 * Il faut également renseigner une balise title ou alt.
 */
function toolTips(){
	var positions = ['top', 'left', 'bottom', 'right'];
	$.each(positions, function(key, pos){
		$('.tooltip-'+pos).tooltip({placement: pos, title: function(){return ($(this).attr('title').length > 0 ) ? $(this).attr('title') : $(this).attr('alt');}});
	});
	$('a').tooltip({placement: 'bottom'});

}

/**
 * Detect if a function is set and is a function
 * @param possibleFunction
 * @returns {boolean}
 */
function isFunction(possibleFunction) {
	return typeof(possibleFunction) === typeof(Function);
}


$("#menu-toggle").click(function(e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
});

toolTips();
