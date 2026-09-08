<?php $this->layout("_theme", ["title" => $title]); ?>

<main>

    <div id="app">
        <div class="top-actions">
            <div class="fltrArea">
                <div>
                    <label for="dateInicial"> <b>Período:</b></label>
                    <div>
                        <input type="date" id="dateInicial" v-model="dataInicial" @change="buscar">
                        <span>a</span>
                        <input type="date" id="dateFinal" v-model="dataFinal" @change="buscar">
                    </div>
                </div>

                <div class="fltrColumn">
                    <label for="fltrTabela"> <b>Tabela:</b> </label>
                    <select id="fltrTabela" v-model="tabela" @change="buscar">
                        <option value="">Todas</option>
                        <option value="materiais">Materiais</option>
                        <option value="usuarios">Usuários</option>
                        <option value="movimentacoes_estoque">Movimentações</option>
                        <option value="lotes">Lotes</option>
                    </select>
                </div>

                <div class="fltrColumn">
                    <label for="fltrEvento"> <b>Evento:</b> </label>
                    <select id="fltrEvento" v-model="evento" @change="buscar">
                        <option value="">Todos</option>
                        <option value="INSERT">Criação (INSERT)</option>
                        <option value="UPDATE">Edição (UPDATE)</option>
                        <option value="DELETE">Exclusão (DELETE)</option>
                        <option value="ENTRADA">Entrada</option>
                        <option value="SAIDA">Saída</option>
                    </select>
                </div>

                <div class="fltrColumn">
                    <label for="buscarUsuario"> <b>Usuário (nome ou ponto):</b> </label>
                    <input type="text" id="buscarUsuario" v-model="buscarUsuario" @keyup="buscar">
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Ponto</th>
                        <th>Tabela</th>
                        <th>ID Reg.</th>
                        <th>Evento</th>
                        <th>Valor anterior</th>
                        <th>Valor novo</th>
                    </tr>
                </thead>
                <tbody id="tabelaLogs">
                    <tr v-for="(log, i) in logs" :key="i">
                        <td>{{ log.data_evento }}</td>
                        <td class="left">{{ log.nome }}</td>
                        <td>{{ log.ponto }}</td>
                        <td class="left">{{ log.tabela_afetada }}</td>
                        <td>{{ log.id_registro }}</td>
                        <td>{{ log.evento }}</td>
                        <td class="left" style="white-space: pre-line;">{{ formatarValor(log.valor_antigo) }}</td>
                        <td class="left" style="white-space: pre-line;">{{ formatarValor(log.valor_novo) }}</td>
                    </tr>
                    <tr v-if="logs.length === 0">
                        <td colspan="8" class="left">Nenhum registro de log encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="nav-table" style="display: flex; justify-content: flex-start; align-items: center; position: relative; margin-top: 20px;">
            <button class="btn-nav" id="navVoltar" @click="getLogs(-lines)"> ◄ </button>
            <span id="nav-index" style="margin: 0 15px;">1</span>
            <button class="btn-nav" id="navAvancar" @click="getLogs(lines)"> ► </button>
        </div>
    </div>
</main>

<?php $this->start("js"); ?>
<script>
    $(function() {
        mostrarLoading();
    });

    // createApp, ref e onMounted já são desestruturados de Vue no layout (_theme.php).
    createApp({
        setup() {
            const logs = ref([]);
            const tabela = ref("");
            const evento = ref("");
            const buscarUsuario = ref("");
            const dataInicial = ref("");
            const dataFinal = ref("");

            let qtdLogs = 0;
            let offset = 0;
            const lines = 14;

            function formatarValor(valor) {
                if (valor === null || valor === undefined || valor === "") return "-";

                try {
                    const obj = JSON.parse(valor);

                    if (obj !== null && typeof obj === "object") {
                        return Object.keys(obj)
                            .map((k) => `${k}: ${obj[k] === null ? "-" : obj[k]}`)
                            .join("\n");
                    }

                    return String(obj);
                } catch (e) {
                    return String(valor);
                }
            }

            function buscar() {
                offset = 0;
                getLogs();
            }

            function getLogs(increment = 0) {
                offset += increment;
                if (offset < 0) offset = 0;

                mostrarLoading();

                let dtInicial = dataInicial.value;
                let dtFinal = dataFinal.value;

                if (dtInicial !== "") dtInicial += " 00:00:00";
                if (dtFinal !== "") dtFinal += " 23:59:59";

                $.ajax({
                    type: "POST",
                    url: "<?= url("/logs/") ?>",
                    data: {
                        offset: offset,
                        tabela: tabela.value,
                        evento: evento.value,
                        buscarUsuario: buscarUsuario.value.trim(),
                        dataInicial: dtInicial,
                        dataFinal: dtFinal
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.code === 200) {
                            logs.value = response.data.logs;
                            qtdLogs = response.data.qtdLogs;

                            const navIdx = document.getElementById("nav-index");
                            const paginaFinal = Math.ceil(qtdLogs <= lines ? 1 : qtdLogs / lines);
                            const paginaAtual = (offset / lines) + 1;

                            const navVoltar = document.getElementById("navVoltar");
                            const navAvancar = document.getElementById("navAvancar");

                            navVoltar.disabled = paginaAtual <= 1;
                            navVoltar.classList.toggle("disabled-button", paginaAtual <= 1);

                            navAvancar.disabled = paginaAtual >= paginaFinal;
                            navAvancar.classList.toggle("disabled-button", paginaAtual >= paginaFinal);

                            navIdx.innerHTML = `${paginaAtual}/${paginaFinal} Páginas`;
                        } else {
                            alert(response.message);
                        }
                        ocultarLoading();
                    },
                    error: function() {
                        ocultarLoading();
                        alert("Erro ao carregar os logs!");
                    }
                });
            }

            onMounted(() => {
                getLogs();
            });

            return {
                logs,
                tabela,
                evento,
                buscarUsuario,
                dataInicial,
                dataFinal,
                lines,
                getLogs,
                buscar,
                formatarValor
            };
        },
    }).mount("#app");
</script>
<?php $this->end("js"); ?>
