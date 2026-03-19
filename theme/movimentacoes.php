<?php $this->layout("_theme", ["title" => $title]); ?>

<main>

    <div id="app">
        <div class="top-actions">
            <div class="fltrArea">
                <div>
                    <label for="dateInicial"> <b>Périodo:</b></label>
                    <div>
                        <input type="date" id="dateInicial">
                        <span>a</span>
                        <input type="date" id="dateFinal">
                    </div>
                </div>

                <div class="fltrColumn">
                    <label for="buscarCodSig"> <b> Digite o código do SIGMA:</b> </label>
                    <input type="number" id="buscarCodSig">
                </div>

                <div class="fltrColumn">
                    <label for="buscarMaterial"> <b> Digite o código ou descrição:</b> </label>
                    <input type="text" id="buscarMaterial">
                </div>

                <div class="fltrColumn">
                    <label for="buscarPessoa"> <b> Digite o ponto ou nome:</b> </label>
                    <input type="text" id="buscarPessoa">
                </div>

                <div class="fltrColumn">
                    <span><b>Tipo moviemntação:</b></span>
                    <div>
                        <input type="checkbox" class="fltrCheck" id="fltrTipoMovEntrada"><label for="fltrTipoMovEntrada">Entrada</label>
                        <input type="checkbox" class="fltrCheck" id="fltrTipoMovSaida"><label for="fltrTipoMovSaida">Saída</label>
                    </div>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cod. Sigma</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Ponto Solicitante</th>
                    <th>Nome Solicitante</th>
                    <th>Ponto Resp.</th>
                    <th>Nome Resp.</th>
                </tr>
            </thead>
            <tbody id="tabelaMovimentacoes">
                <template v-for="(movimentacao, i) in movimentacoes" :key="i">
                    <tr @click="toggleMovimentacao(movimentacao.id_movimentacao)" style="cursor:pointer">
                        <td>{{movimentacao.id_movimentacao}}</td>
                        <td>{{movimentacao.quantidade}}</td>
                        <td>{{movimentacao.data_movimentacao}}</td>
                        <td class="left">{{movimentacao.tipo}}</td>
                        <td class="left">{{movimentacao.ponto_solicitante}}</td>
                        <td class="left">{{movimentacao.nome_solicitante}}</td>
                        <td class="left">{{movimentacao.ponto}}</td>
                        <td class="left">{{movimentacao.nome}}</td>
                    </tr>
                    <tr v-if="movimentacoesAbertas.has(movimentacao.id_movimentacao)" v-for="(material, j) in movimentacao.materialList" :key="'material-'+j" class="sublist">
                        <td colspan="3">
                            <span><b>Material: </b>{{ material.descricao }}</span>
                        </td>
                        <td colspan="2">
                            <span><b>Lote: </b>{{ material.lote }}</span>
                        </td>
                        <td colspan="2">
                            <span><b>Quantidade: </b>{{ material.quantidade }}</span>
                        </td>
                        <td class="actions">
                            <button class="btn-exit" @click="excluirMaterial(material)">Excluir</button>
                        </td>
                    </tr>
                </template>

            </tbody>
        </table>

        <div id="nav-table">
            <button class="btn-nav" id="navVoltar" @click="getMovimentacao(-lines)">
                < </button>
                    <span id="nav-index">1</span>

                    <button class="btn-nav" id="navAvancar" @click="getMovimentacao(lines)"> > </button>
        </div>
    </div>
</main>

<?php $this->start("js"); ?>
<script>
    $(function() {
        mostrarLoading();

    });

    const {
        computed,
        onBeforeUnmount,
        watch,
    } = Vue;

    createApp({
        setup() {
            const movimentacoes = ref([]);
            let qtdMovimentacoes = 0;
            let paginaAtual = 0;
            let offset = 0;
            const lines = 13;

            const movimentacoesAbertas = ref(new Set());

            function toggleMovimentacao(id) {
                if (movimentacoesAbertas.value.has(id)) {
                    movimentacoesAbertas.value.delete(id);
                } else {
                    movimentacoesAbertas.value.add(id);
                }
            }


            function getMovimentacao(increment = 0) {
                let dataInicial = document.getElementById("dateInicial").value;
                let dataFinal = document.getElementById("dateFinal").value;
                const buscarCodSig = document.getElementById("buscarCodSig").value.trim();
                const buscarMaterial = document.getElementById("buscarMaterial").value.trim();
                const buscarPessoa = document.getElementById("buscarPessoa").value.trim();
                const fltrMovEntrada = document.getElementById("fltrTipoMovEntrada").checked;
                const fltrMovSaida = document.getElementById("fltrTipoMovSaida").checked;

                if (dataInicial !== "") dataInicial += " 23:59:59";
                if (dataFinal !== "") dataFinal += " 23:59:59";

                offset += increment;

                $.ajax({
                    type: "POST",
                    url: "<?= url("/movimentacoes/") ?>",
                    data: {
                        offset: offset,
                        dataInicial: dataInicial,
                        dataFinal: dataFinal,
                        buscarCodSig: buscarCodSig,
                        buscarMaterial: buscarMaterial,
                        buscarPessoa: buscarPessoa,
                        fltrMovEntrada: fltrMovEntrada,
                        fltrMovSaida: fltrMovSaida
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.hasOwnProperty("message") && response.message.indexOf("<br>[ERRO]") === 0) {
                            alert(response.message);

                        } else {
                            movimentacoes.value = response.data.movimentacoes;
                            qtdMovimentacoes = response.data.qtdMovimentacoes;

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
                        ocultarLoading()
                    }
                });
            }

            function excluirMaterial(material) {
                $.ajax({
                    type: "post",
                    url: "<?= url("/movimentacoes/excluirMaterial") ?>",
                    data: material,
                    dataType: "json",
                    success: function(response) {
                        alert(response.message);

                        if (response.code === 200) {


                            let movimentacao = movimentacoes.value.find((item) => item.id_movimentacao === material.id_movimentacao);
                            if (movimentacao.materialList.length > 1) {
                                const idxMaterial = movimentacao.materialList.findIndex((item) => item.id_lote == material.id_lote);
                                movimentacao.materialList.splice(idxMaterial, 1);
                            } else {
                                const idxMaterial = movimentacoes.value.findIndex((item) => item.id_movimentacao === movimentacao.id_movimentacao)
                                console.log(idxMaterial);
                                movimentacoes.value.splice(idxMaterial, 1);
                            }
                        }
                    }
                });
            }

            onMounted(() => {
                getMovimentacao();

                document.getElementById("dateInicial").addEventListener("change", function() {
                    offset = 0
                    getMovimentacao();

                });
                document.getElementById("dateFinal").addEventListener("change", function() {
                    offset = 0
                    getMovimentacao();

                });

                document.getElementById("buscarCodSig").addEventListener("keyup", function() {
                    offset = 0
                    getMovimentacao();
                });

                document.getElementById("buscarMaterial").addEventListener("keyup", function() {
                    offset = 0
                    getMovimentacao();
                });

                document.getElementById("buscarPessoa").addEventListener("keyup", function() {
                    offset = 0
                    getMovimentacao();
                });

                document.querySelectorAll('.fltrCheck').forEach(chcks => {
                    chcks.addEventListener('click', function(chck) {
                        offset = 0;
                        getMovimentacao();
                    });
                });
            });


            return {
                movimentacoes,
                getMovimentacao,
                lines,
                excluirMaterial,
                movimentacoesAbertas,
                toggleMovimentacao
            };
        },
    }).mount("#app");
</script>
<?php $this->end("js"); ?>