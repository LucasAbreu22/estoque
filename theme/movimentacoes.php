<?php $this->layout("_theme", ["title" => $title]); ?>

<main>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Material</th>
                <th>QTD.</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Ponto Solicitante</th>
                <th>Nome Solicitante</th>
                <th>Ponto Resp.</th>
                <th>Nome Resp.</th>
            </tr>
        </thead>
        <tbody id="tabelaMovimentacoes">

        </tbody>
    </table>
    <div id="nav-table">
        <button class="btn-nav" id="navVoltar" onclick="getMovimentacao(-lines)">
            < </button>
                <span id="nav-index">1</span>

                <button class="btn-nav" id="navAvancar" onclick="getMovimentacao(lines)"> > </button>
    </div>
</main>
<?php $this->start("js"); ?>
<script>
    $(function() {
        mostrarLoading()
        getMovimentacao();

    });

    let movimentacoes = [];
    let qtdMovimentacoes = 0;
    let paginaAtual = 0;
    let offset = 0
    const lines = 13;

    function getMovimentacao(increment = 0) {

        offset += increment;

        $.ajax({
            type: "POST",
            url: "<?= url("/movimentacoes/") ?>",
            data: {
                offset: offset
            },
            dataType: "json",
            success: function(response) {

                if (response.hasOwnProperty("message") && response.message.indexOf("<br>[ERRO]") === 0) {
                    alert(response.message);

                } else {
                    movimentacoes = response.data.movimentacoes;
                    qtdMovimentacoes = response.data.qtdMovimentacoes;
                    atualizarMovimentacaoList();
                }
                ocultarLoading()
            }
        });
    }

    function atualizarMovimentacaoList() {
        const tabela = document.getElementById('tabelaMovimentacoes');

        tabela.innerHTML = "";

        movimentacoes.forEach(movimentacao => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
            <td>${movimentacao.id_movimentacao}</td>
            <td>${movimentacao.codigo}</td>
            <td class="left">${movimentacao.descricao}</td>
            <td>${movimentacao.quantidade}</td>
            <td>${movimentacao.data_movimentacao}</td>
            <td class="left">${movimentacao.tipo}</td>
            <td class="left">${movimentacao.ponto_solicitante}</td>
            <td class="left">${movimentacao.nome_solicitante}</td>
            <td class="left">${movimentacao.ponto}</td>
            <td class="left">${movimentacao.nome}</td>
        `;

            tabela.appendChild(tr);
        });

        const navIdx = document.getElementById("nav-index");


        const paginaFinal = Math.ceil(qtdMovimentacoes <= lines ? 1 : qtdMovimentacoes / lines);

        paginaAtual = (offset / lines) + 1;

        const navVoltar = document.getElementById("navVoltar");
        const navAvancar = document.getElementById("navAvancar");

        if (paginaAtual == 1) {
            navVoltar.disabled = true;
            navVoltar.classList.add("disabled-button");
        } else if (paginaAtual > 1) {
            navVoltar.disabled = false;
            navVoltar.classList.remove("disabled-button");
        }

        if (paginaAtual == paginaFinal) {
            navAvancar.disabled = true;
            navAvancar.classList.add("disabled-button");
        } else if (paginaAtual < paginaFinal && paginaAtual > 1 || paginaFinal > 1) {
            navAvancar.disabled = false;
            navAvancar.classList.remove("disabled-button");
        }

        navIdx.innerHTML = `${paginaAtual}/${paginaFinal} Páginas`;
    }
</script>
<?php $this->end("js"); ?>