<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="<?= url("/theme/assets/css/app.css") ?>">
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <title><?= SITE . " - " . $title; ?></title>
</head>

<body>

  <header>
    <h1>CGraf Estoque</h1>
    <span><?= $title; ?></span>

    <?php if ($title == "Gerenciamento Estoque"): ?>
      <a href="<?= url("/movimentacoes/") ?>">Histórico de movimentações ➨</a>
    <?php endif; ?>
    <?php if ($title == "Movimentações Estoque"): ?>
      <a href="<?= url("/") ?>">Estoque ➨</a>
    <?php endif; ?>
  </header>

  <?= $this->section("content"); ?>

  <div id="background-loader"></div>
  <div id="loader"></div>

  <script src="<?= url("/theme/assets/js/index.js"); ?>"></script>
  <script src="<?= url("/theme/assets/js/jquery.js"); ?>"></script>

  <?= $this->section("js"); ?>
</body>

</html>