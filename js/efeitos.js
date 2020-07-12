$(function () {       
    $("#salario").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false,digits:2});
    $("#valor_tp").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false,digits:2});
    $("#valor_tp2").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false,digits:2});
    $("#salario2").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false,digits:2});
    $("#total").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false,digits:2});
    $("#sobra").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false,digits:2});
    $("#subtotal").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false,digits:2});
    
    $(".mes_referencia").hover(function () {
        $(this).addClass("efeito_hover")
    },
            function () {
                $(this).removeClass("efeito_hover");
            });

    $("a#processar").click(function () {
        $(this).parent().next().next().children().toggle("slow");
    });
    $("a#cancelar").click(function () {
        $(this).parent().next().children().toggle("slow");
    });
    
    
});
