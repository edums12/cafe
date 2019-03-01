<main id="configuracoes">
    <div class="container">
        <p class="title">Configurações de gasto</p>
        
        <form action="<?= base_url("configuracoes/gasto")?>" method="POST">
            <div class="row">
                <div class="col s12">
                    <p>
                        <label>
                            <input type="checkbox" name="utilizar_limite" class="filled-in" {utilizar_limite} />
                            <span>Utilizar limite</span>
                        </label>
                    </p>
                </div>
            </div>

            <div class="row" style="margin-bottom: 0">
                <div class="input-field col s12">
                    <input id="limite" type="number" name="limite" class="validate" autocorrect="off" autocapitalize="none" required value="{limite}" step="0.01">
                    <label for="limite">Limite de gasto (R$)</label>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <input type="submit" value="Salvar" class="btn-flat blue white-text">
                </div>
            </div>
        </form>
    </div>
</main>