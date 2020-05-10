$(function () {
  $(".person-type").change(function(){
    validaPersonType();
  });
  if($(".person-type").val()=="PJ" || ($(".person-type").val()=="" && $("#appbundle_company_cnpj").val()!="")){
    $(".person-type").val("PJ");
    validaPersonType();
  } else {
    $(".person-type").val("PF");
    validaPersonType();
  }

  $(".contract-type").change(function(){
    validaContractType();
  });
  if($(".contract-type").val()=="Contrato" || ($(".contract-type").val()=="" && $(".responsible").val()!="")){
    $(".contract-type").val("Contrato");
    validaPersonType()
  }
});

function validaPersonType(personType){
  if($(".person-type").val()=="PJ"){
    $("#person-type-cnpj").show();
    $("#person-type-cpf").hide();
    $("input.cpf").val("");
  }else{
    $("#person-type-cnpj").hide();
    $("#person-type-cpf").show();
    $("input.cnpj").val("");
  }
}

function validaContractType(personType){
  if($(".contract-type").val()=="Contrato"){
    $(".responsible-block").show();
  }else{
    $("#appbundle_company_user").val("");
    $(".responsible-block").hide();
  }
}
