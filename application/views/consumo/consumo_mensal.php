<main id="configuracoes">
    <div class="container">
        <p class="title">Consumo mensal</p>

        {meses_consumo}
            <div class="grey lighten-2 group">{mes_extenso} - {ano} <span class="right"><b>R$ {valor_total}</b></span></div>
            <table class="striped">
                <thead>
                    <th>Data</th>
                    <th>Café</th>
                    <th>Valor</th>
                    <th>Pago</th>
                </thead>
                <tbody>
                    {consumos}
                        <tr>
                            <td>{data}</td>
                            <td>{tipo}</td>
                            <td>{valor}</td>
                            <td>{pago}</td>
                        </tr>
                    {/consumos}
                </tbody>
            </table>
        {/meses_consumo}
    </div>
</main>
