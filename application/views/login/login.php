<main class="login">
    <div class="row">
        <div class="col s10 m4 offset-s1 offset-m4">
            <div class="card z-depth-1">
                <div class="card-content">
                    <form action="<?= base_url('login')?>" method="POST">
                        <span class="card-title center-align blue-text">Login</span>

                        <p class="center-align">Entre com seu usuário e senha</p>
                        <br>

                        <div class="row">
                            <div class="input-field col s12">
                                <input id="user" type="text" name="user" class="validate" autocorrect="off" autocapitalize="none" required>
                                <label for="user">Usuário</label>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="pass" type="password" name="pass" class="validate" autocorrect="off" autocapitalize="none" required>
                                <label for="pass">Senha</label>
                            </div>
                        </div>
                        <br>
                        <input type="submit" value="Entrar" class="btn blue z-depth-1">
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>