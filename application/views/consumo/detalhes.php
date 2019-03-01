<main>
    <div class="container">
    
        <br>

        <!-- <div class="row">   
            <div class="col s12 no-padding">
                <text class="grey-text center-align text-darken-3">Olá, {nome}</text>
            </div>
            <div class="col s6 right-align no-padding">
                <a class="dropdown-trigger blue-text" data-target="meses_consumo">{mes} - {ano}</a>
            </div>
        </div> -->
        
        <!-- <ul id="meses_consumo" class="dropdown-content">
            {meses_consumo}
                <li><a href="<?= base_url("consumo/detalhado/{mes}/{ano}") ?>" class="blue-text">{mes_extenso}/{ano}</a></li>
            {/meses_consumo}
        </ul> -->

        <div class="row no-margin">
            <div class="col s12 center no-padding">
                <p class="no-margin">Olá {nome}</p>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col s6 no-padding">
                <h5 class="blue-text"><b>{consumo_mensal}</b></h5>
            </div>
            <div class="col s6 no-padding">
                <a href="#confirmar-pagamento" id="btn-pagar" class="btn blue white-text right disabled modal-trigger">Pagar</a>
            </div>
        </div>

        <div id="confirmar-pagamento" class="modal">
            <form method="POST" action="<?= base_url("consumo/pagar") ?>">
                <input type="hidden" name="id_pagamentos">
                <div class="modal-content">
                    <p class="no-margin">Confirmar o pagamento de <span id="quantidade_cafe_tomados"></span> café(s) tomado(s)?</p>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn-flat btn-small blue-text" value="Confirmar">
                </div>
            </form>
        </div>

        <table class="striped">
            <thead>
                <th>
                    <label>
                        <input type="checkbox" id="pagar_todos" class="filled-in indeterminate"/>
                        <span></span>
                    </label> 
                </th>
                <th>Data</th>
                <th>Café</th>
                <th>Valor</th>
            </thead>
            <tbody>
                {consumo}
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="pagamentos[]" class="filled-in" value="{id_consumo}"/>
                                <span></span>
                            </label>
                        </td>
                        <td>{data}</td>
                        <td>{tipo}</td>
                        <td>{valor}</td>
                    </tr>
                {/consumo}
            </tbody>
        </table>
    </div>
</main>



<script>
    function todos_selecionados(){
        var inputs = $("input[name='pagamentos[]']");

        var quantidade_checkeds = 0;

        for(var i = 0; i < inputs.length; i++)
        {            
            if ($(inputs[i]).is(":checked"))
                quantidade_checkeds++;
        }

        return inputs.length == quantidade_checkeds;
    }

    function um_selecionado(){
        var inputs = $("input[name='pagamentos[]']");

        for(var i = 0; i < inputs.length; i++)
        {            
            if ($(inputs[i]).is(":checked"))
                return true;
        }

        return false;
    }

    function inputs_selecionados(){
        var inputs = $("input[name='pagamentos[]']");

        var selecionados = new Array();

        for(var i = 0; i < inputs.length; i++)
        {            
            if ($(inputs[i]).is(":checked"))
                selecionados.push($(inputs[i]).val());
        }

        return selecionados;
    }

    $(document).ready(function(){
        habilitarBotao();
    })

    $("input[id='pagar_todos']").click(function(){
        if (todos_selecionados())
        {
            $("input[name='pagamentos[]']").prop("checked", false).change();
            $(this).prop("checked", false);
        }
        else
        {
            $("input[name='pagamentos[]']").prop("checked", true).change();
            $(this).prop("checked", true);
        }
    });

    $("table tr td").click(function(e){       
        var field = $(e.target.parentElement.children[0]).find("input");

        if (field.is(":checked")) 
            field.prop("checked", false).change();
        else
            field.prop("checked", true).change();

        if (todos_selecionados())
            $("input[id='pagar_todos']").prop("checked", true);
        else
            $("input[id='pagar_todos']").prop("checked", false);
    });

    $("input[name='pagamentos[]']").on('change', function(){
        habilitarBotao();
    });

    $("#btn-pagar").click(function(){
        var selecionados = inputs_selecionados();

        $("input[name='id_pagamentos']").val(selecionados.join(';'))
        $("#quantidade_cafe_tomados").text(selecionados.length);
    })

    function habilitarBotao(){
        if (um_selecionado())
            $("#btn-pagar").removeClass("disabled");
        else
            $("#btn-pagar").addClass("disabled");
    }
</script>