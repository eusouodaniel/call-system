/**
 * Wrapper que exibe o mapa dentro do container informado
 * @param latitude double Latitude do ponto a ser exibido
 * @param longitude double Longitude do ponto a ser exibido
 */
(function($) {
    $.fn.customGmap = function(latitude, longitude) {
    	return this.gmap({
            'zoom': 16, 
            'center': latitude + ',' + longitude,
            'callback': function() {
                this.addMarker({
                    'position': latitude + ',' + longitude
                    });
            }
        });
    }
})(jQuery);

$(document).ready(function() {
    $('.map-container').each(function() {
        var latitude = $(this).data('map-latitude');
        var longitude = $(this).data('map-longitude');
        $(this).gmap({
            'zoom': 16,
            'center': latitude + ',' + longitude,
            'callback': function() {
                this.addMarker({
                    'position': latitude + ',' + longitude
                });
            }
        });
    });
});