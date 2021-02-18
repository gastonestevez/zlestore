// Estas funciones de JQuery se utilizan para que el footer quede siempre abajo
// https://www.codigonexo.com/blog/programacion/javascript/funcion-para-colocar-footer-siempre-al-final/
// Cuando se haya cargado todo el contenido, cuando se reescale la pantalla y cuando esté disponible el DOM,
//vamos a buscar el elemento #main y le vamos a dar una altura mínima para que ocupe toda la pantalla menos el
//tamaño de la cabecera y el tamaño del footer. De este modo, el main siempre será todo lo alto que se pueda
// y el footer quedará siempre abajo.

jQuery(window).load(function(){
//Cuando se carga todo el contenido.
jQuery("#main").css("min-height", (jQuery(window).height()-jQuery("footer").outerHeight()-jQuery("header").outerHeight()+"px"));
}).resize(function(){
//Cuando se escala la pantalla.
jQuery("#main").css("min-height", (jQuery(window).height()-jQuery("footer").outerHeight()-jQuery("header").outerHeight()+"px"));
});
jQuery(document).ready(function(){
//Cuando el DOM está disponible.
jQuery("#main").css("min-height", (jQuery(window).height()-jQuery("footer").outerHeight()-jQuery("header").outerHeight()+"px"));
})
