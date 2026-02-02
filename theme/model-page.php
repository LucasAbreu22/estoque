<?php $this->layout("_theme", ["title" => $title]); ?>


<?php $this->start("js"); ?>
<script>
    $(function() {

        $("form").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= url("/") ?>",
                data: form.serialize(),
                type: "POST",
                dataType: "json",
                success: function(callback) {

                    if (callback.hasOwnProperty("message") && callback.message.indexOf("[ERRO]") == 0) {
                        alert(callback.message);
                        return false
                    }

                    console.log(callback);

                },
            });
        });
    });
</script>
<?php $this->end("js"); ?>