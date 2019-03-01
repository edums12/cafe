<main>
    <div class="container">
        <!-- <h4 class="grey-text text-darken-3 consumo line-heigth-normal">
            <p class="left grey-text text-darken-3 font-normal margin-top-0">Olá {nome}</p>
            <b class="blue-text d-block right-align">
                <p class="font-normal no-padding no-margin">Total</p>
                {consumo_mensal}
            </b>
        </h4> -->

        <div class="row no-margin">
            <div class="col s12 center">
                <br>
                <p class="no-margin">Olá {nome}</p>
                <br>
            </div>
        </div>

        <?php if ($utilizar_limite): ?>

        <br><br>

        <div class="row white row-progress">
            <h5 class="blue-text right-align" style="position: absolute; left: calc({left}); top: -40px;">{consumo_mensal}</h5>
            <div class="col s12 no-padding">
                <div class="progress blue lighten-5">
                    <div class="determinate blue" style="width: {progress}%"></div>
                </div>
                <span class="left">0</span>
                <span class="right">{limite}</span>
            </div>
        </div>

        <?php else: ?>

        <h4 class="grey-text text-darken-3 consumo line-heigth-normal no-margin no-padding">
            <b class="blue-text d-block left-align">
                <p class="font-normal no-padding no-margin">Total</p>
                {consumo_mensal}
            </b>
        </h4>

        <?php endif; ?>


        <br>
        
        {cafes}
            <div class="row">
                <a href="<?= base_url("consumo/{id_cafe}") ?>" class="btn blue btn-cafe {disabled}">
                    <span>({consumo})</span>
                    <span>{tipo}</span>
                    <span>{valor}</span>
                </a>
            </div>
        {/cafes}
    </div>
</main>