
<script src="<?= base_url('assets/materialize/js/materialize.min.js') ?>"></script>

<script>
    $(document).ready(() => {
        
        <?php if($this->session->flashdata('error') != NULL): ?>

        M.toast({html: "<?= $this->session->flashdata('error') ?>", classes: "toast-error"});

        <?php endif; ?>
        
        <?php if($this->session->flashdata('success') != NULL): ?>

        M.toast({html: "<?= $this->session->flashdata('success') ?>", classes: "toast-success"});

        <?php endif; ?>

        <?php if($this->session->flashdata('registro') != NULL): $registro = (object)$this->session->flashdata('registro');?>

        M.toast({html: "<span><?= $registro->msg?></span><a href=\"<?= base_url("consumo/desfazer/{$registro->id}") ?>\" class=\"btn-flat toast-action\">Desfazer</a>", classes: "toast-success"});

        <?php endif; ?>

        $('.dropdown-trigger').dropdown({
            alignment: 'right',
            constrainWidth: false
        });

        $('.modal').modal();
    });
</script>

</body>
</html>