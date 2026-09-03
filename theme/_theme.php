<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="<?= url("/theme/assets/css/app.css") ?>">
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <link rel="icon" type="image/x-icon" href="<?= url("/theme/assets/img/icone-estoque.png"); ?>">
  <title><?= SITE . " - " . $title; ?></title>
</head>

<body>

  <header>
    <h1>CGraf Estoque</h1>
    <span><?= $title; ?></span>

    <?php if ($title == "Gerenciamento Estoque"): ?>
      <a href="<?= url("/movimentacoes/") ?>">Histórico de movimentações ➨</a>
      <a href="<?= url("/usuarios/") ?>">Usuários ➨</a>
    <?php endif; ?>
    <?php if ($title == "Movimentações Estoque"): ?>
      <a href="<?= url("/") ?>">Estoque ➨</a>
    <?php endif; ?>
    <?php if ($title == "Gerenciamento de Usuários"): ?>
      <a href="<?= url("/") ?>">Estoque ➨</a>
    <?php endif; ?>
  </header>

  <?= $this->section("content"); ?>

  <div id="background-loader"></div>
  <div id="loader"></div>

  <div class="modal" id="modalPonto">
    <div class="modal-content" id="ponto-content">
      <h2>Ponto responsável</h2>

      <input type="number" @input="setPonto">

      <div class="modal-actions">
        <button class="btn-confirm" @click="setCookie()">Confirmar</button>
      </div>
    </div>
  </div>

  <script src="<?= url("/theme/assets/js/index.js"); ?>"></script>
  <script src="<?= url("/theme/assets/js/jquery.js"); ?>"></script>

  <script>
    const {
      createApp,
      ref,
      onMounted,
    } = Vue;

    createApp({
      setup() {
        const ponto = ref(0);

        // Função para obter o valor de um cookie específico
        function getCookie(nomeCookie) {
          let nome = nomeCookie + "=";
          let cookiesArray = document.cookie.split(';');

          for (let i = 0; i < cookiesArray.length; i++) {
            let c = cookiesArray[i].trim();
            if (c.indexOf(nome) === 0) {
              return c.substring(nome.length, c.length);
            }
          }
          return ""; // Retorna vazio se não encontrar
        }

        // Função para criar/definir cookie
        function setCookie() {
          if (ponto.value === undefined) {
            alert("Valor de ponto inválido!");
            return false;
          }

          $.ajax({
            type: "POST",
            url: "<?= url("/usuario/consultarPonto/") ?>",
            data: {
              ponto: ponto.value
            },
            dataType: "json",
            success: function(response) {


              if (response.code == 200) {

                if (response.data[0].id_usuario !== undefined) {
                  const nome = "usuario";
                  const diasParaExpirar = 7;
                  let data = new Date();

                  data.setTime(data.getTime() + (diasParaExpirar * 24 * 60 * 60 * 1000));

                  let expires = "expires=" + data.toUTCString();

                  // Cria o cookie com nome, valor, expiração e caminho
                  document.cookie = `${nome}=${ponto.value}; ${expires}; path=/`;

                  fecharModal()
                } else {
                  alert("Não há usuário com o ponto informado!");
                }

              }

            }
          });
        }

        function setPonto(e) {
          ponto.value = e.target.value;
        }

        function abrirModalPonto() {
          document.getElementById('modalPonto').classList.add('active');
        }

        function fecharModal() {

          document.getElementById('modalPonto').classList.remove('active');

        }

        onMounted(() => {
          let usuario = getCookie("usuario");
          if (usuario != "") {
            console.log("Bem-vindo de volta: " + usuario);
          } else {
            abrirModalPonto();
            console.log("Cookie não encontrado.");
          }
        });


        return {
          setCookie,
          setPonto
        };
      },
    }).mount("#modalPonto");
  </script>
  <?= $this->section("js"); ?>
</body>

</html>