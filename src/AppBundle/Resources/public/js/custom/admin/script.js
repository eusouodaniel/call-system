$(function () {
    $('.date-picker').datetimepicker({
    	format: 'DD/MM/YYYY'
    });

    $('.datetime-picker').datetimepicker({
    	format: 'DD/MM/YYYY HH:mm'
    });

    $(".btn-export").click(function(){
        var form = $(".form-search");
        form.attr("action",$(this).data('url-export'));
        form.submit();

        form.attr("action",$(this).data('url-original'));
    });
});

$(document).ready(function() {
    $(".edit-form-chamado select option[value='99']").remove();    
});

$(document).ready(function(){
    $("#appbundle_chamado_dtLimit").attr("required", "required");
})