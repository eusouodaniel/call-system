$(document).ready(function(){
    $(".name").click(function() {
       $(this).next(".description").toggle();
    });
    //Centraliza os titulos nos boxes do final da pagina
   	/*var cw = $('.middle-title-position-img').height(); 
        $('.middle-title-position').css({
               'bottom': (cw/3)-6
           });*/
        
});

function closeModal(){
    $('#paolinelliModal iframe').attr("src", jQuery("#paolinelliModal iframe").attr("src"));
}