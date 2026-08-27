<?php $this->layout("_theme", ["title" => $title]); ?>

<main>
    <div id="app">
        <div class="top-actions">
            <div class="fltrArea">
                <div class="fltrColumn">
                    <label> <b>Descrição:</b> </label>
                    <input type="text" id="buscarMaterial">
                </div>

                <div class="fltrColumn">
                    <label> <b> Categoria:</b> </label>
                    <select id="fltrCategoria">
                        <option value="" selected>Selecione</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria->id_categoria ?>"><?= $categoria->nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="fltrColumn">
                    <span><b>Status:</b></span>
                    <div>
                        <input type="checkbox" class="fltrCheck" id="fltrStatusNormal"><label for="fltrStatusNormal">Normal</label>
                        <input type="checkbox" class="fltrCheck" id="fltrStatusAcabando"><label for="fltrStatusAcabando">Acabando</label>
                        <input type="checkbox" class="fltrCheck" id="fltrStatusSemEstoque"><label for="fltrStatusSemEstoque">Sem Estoque</label>
                    </div>
                </div>
            </div>

            <div class="fltrArea">
                <button class="btn-add btn-entry" @click="abrirMovimentacao('ENTRADA')">Incluir Entrada</button>
                <button class="btn-add btn-alert" @click="abrirMovimentacao('SAIDA')">Incluir Saída</button>
                <button class="btn-add" @click="abrirModalMaterial()">+ Incluir Material</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Descrição de material</th>
                        <th>Categoria</th>
                        <th>Saldo</th>
                        <th>Un. Base</th>
                        <th>Un. Compra</th>
                        <th>Mínimo</th>
                        <th>Localização</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabelaMateriais">
                    <template v-for="(material, i) in materiais" :key="i">
                        <tr @click="toggleMaterial(material.id_material)" style="cursor:pointer">
                            <td class="left descricao">{{material.descricao}}</td>
                            <td class="left">{{material.categoria}}</td>
                            <td class="saldo">{{material.quantidade}}</td>
                            <td class="left">{{material.unidade_base}}</td>
                            <td class="left">{{material.unidade_compra}}</td>
                            <td class="minimo">{{material.quantidade_minima}}</td>
                            <td>{{material.localizacao}}</td>
                            <td>
                                <span class="badge" :class="getStatusClss(material)">
                                    {{material.status}}
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn-entry" @click.stop="abrirMovimentacao('ENTRADA', material.id_material)">Entrada 🡇</button>
                                <button class="btn-edit" @click.stop="editarMaterial(material.id_material)">Editar</button>
                                <button class="btn-exit" @click.stop="excluirMaterial(material.id_material)">Excluir</button>
                            </td>
                        </tr>

                        <tr v-if="materiaisAbertos.has(material.id_material)" v-for="(lote, index) in material.loteList" :key="'lote-'+index" class="sublist">
                            <td colspan="2">
                                <span><b>Lote: </b>{{ lote.lote }}</span>
                            </td>
                            <td colspan="3">
                                <span><b>Quantidade: </b>{{ lote.quantidade }}</span>
                            </td>
                            <td colspan="3">
                                <span><b>Vencimento: </b>{{ lote.vencimentoFormatted }}</span>
                            </td>
                            <td class="actions">
                                <button class="btn-alert" @click="abrirMovimentacao('SAIDA', lote.id_lote)">Saída 🡅</button>
                                <button class="btn-edit" @click="editarLote(lote)">Editar</button>
                                <button class="btn-exit" @click="excluirLote(lote)">Excluir</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div id="nav-table" style="display: flex; justify-content: flex-start; align-items: center; position: relative; margin-top: 20px;">
            <button class="btn-nav disabled-button" id="navVoltar" @click="getMateriais(-lines)" disabled>
                ◄ </button>
            <span id="nav-index" style="margin: 0 15px;">1</span>

            <button class="btn-nav disabled-button" id="navAvancar" @click="getMateriais(lines)" disabled> ► </button>

            <button class="btn-add" style="position: absolute; right: 0; margin: 0; height: 35px; padding: 0 20px;" @click="gerarRelatorio()">
                📄 Relatório PDF
            </button>
        </div>


        <!-- MODAL MOVIMENTAÇÃO -->
        <div class="modal" id="modalMov">
            <div class="modal-content">
                <h2 id="tituloMov"></h2>
                <br>

                <span>Evento</span>
                <div id="eventoArea">
                    <div>
                        <input type="radio" id="movEntrada" name="evento" value="ENTRADA">
                        <label for="movEntrada">Entrada</label>
                    </div>
                    <div>
                        <input type="radio" id="movSaida" name="evento" value="SAIDA">
                        <label for="movSaida">Saída</label>
                    </div>
                </div>
                <br>

                <div id="areaSigma">
                    <label>Código de requisição do SIGMAS</label>
                    <input type="number" id="codigoSigma">
                </div>

                <label>Ponto responsável</label>
                <input type="number" id="pontoResponsavel" disabled>

                <div id="areaSolicitante">
                    <div id="areaPontoSolicitante">
                        <label>Ponto solicitante</label>
                        <input type="number" id="pontoSolicitante" @input="editPontoSolicitante">
                    </div>
                    <div id="areaNomeSolicitante">
                        <label>Nome solicitante</label>
                        <input type="text" id="nomeSolicitante">
                    </div>
                </div>

                <h4>Materiais movimentação</h4>
                <div id="areaCarrinho">
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição de material</th>
                                <th>Lote</th>
                                <th>Unidade</th>
                                <th>QTD</th>
                                <th>Vencimento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaCarrinho">
                            <template v-for="(material, i) in carrinhoList" :key="i">
                                <tr v-for="(lote, j) in material.loteList" :key="j">
                                    <td class="left descricao">{{material.descricao}}</td>
                                    <td class="columnLote"><input type="number" min="1" @input="editLote($event, i, j)" :value="lote.lote" :disabled="tipoMov==='SAIDA' ? true : false"></td>
                                    <td class="left">{{tipoMov ==='SAIDA' ? material.unidade_base : material.unidade_compra}}</td>
                                    <td><input type="number" @input="editQtdItem($event, i, j)" min="1" :value="lote.quantidade"></td>
                                    <td><input type="date" @input="editVencLote($event, i, j)" :value="lote.vencimento" :disabled="tipoMov==='SAIDA' ? true : false"></td>
                                    <td class="actions">
                                        <button class="btn-exit" @click="removerItem(i, j)">▼</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <h4>Materiais almox</h4>
                <input type="text" id="buscarMaterialModal" placeholder="Digite o código ou descrição">
                <div id="areaAlmox">
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição de material</th>
                                <th>Saldo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaMateriaisModal">
                            <template v-for="(materialModal, i) in materiaisModal" :key="i">
                                <tr>
                                    <td class="left descricao">{{materialModal.descricao}}</td>
                                    <td>{{materialModal.quantidade}}</td>
                                    <td class="actions">
                                        <button :class="tipoMov === 'SAIDA' && materialModal.quantidade == 0? 'disabled-button ': 'btn-entry '" @click="toggleMaterialModal(materialModal.id_material)">▲</button>
                                    </td>
                                </tr>
                                <tr v-if="materiaisAbertosModal.has(materialModal.id_material) && tipoMov === 'SAIDA'" v-for="(lote, index) in materialModal.loteList" :key="'loteModal-'+index" class="sublist">
                                    <td colspan="2">
                                        <div style="display:flex; gap:20px; justify-content: space-evenly;">
                                            <span><b>Lote: </b>{{ lote.lote }}</span>
                                            <span><b>Quantidade: </b>{{ lote.quantidade }}</span>
                                            <span><b>Vencimento: </b>{{ lote.vencimentoFormatted }}</span>
                                        </div>
                                    </td>
                                    <td class="actions">
                                        <button :class="tipoMov === 'SAIDA' && lote.quantidade == 0? 'disabled-button ': 'btn-entry '" @click="adicionarItem(lote.id_material, lote.id_lote)">▲</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div id="nav-table">
                    <button class="btn-nav disabled-button" id="navModalVoltar" @click="getMateriaisModal(-lines)" disabled>
                        ◄ </button>
                    <span id="navModal-index">1</span>

                    <button class="btn-nav disabled-button" id="navModalAvancar" @click="getMateriaisModal(lines)" disabled> ► </button>
                </div>
                <div class="modal-actions">
                    <button class="btn-cancel" @click="fecharModal('modalMov')">Cancelar</button>
                    <button class="btn-confirm" @click="criarMovimentacao()">Confirmar</button>
                </div>
            </div>
        </div>

        <!-- MODAL Incluir MATERIAL -->
        <div class="modal" id="modalMaterial">
            <div class="modal-content">
                <h2 id="titleModalMaterial">Material</h2>

                <label>Descrição</label>
                <input type="text" id="descricao">

                <label>Categoria</label>
                <select id="categoria">
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria->id_categoria ?>"><?= $categoria->nome ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Unidade Base</label>
                <select id="unBase">
                    <option>Centímetro</option>
                    <option>Chapa</option>
                    <option>Folha</option>
                    <option>Grama</option>
                    <option>Litro</option>
                    <option>Metro</option>
                    <option>Mililitro</option>
                    <option>Pacote</option>
                    <option>Quilo</option>
                    <option>Unidade</option>
                </select>

                <label>Unidade por embalagem</label>
                <select id="unCompra">
                    <option>Bisnaga</option>
                    <option>Bobina</option>
                    <option>Bombona</option>
                    <option>Fardo</option>
                    <option>Frasco</option>
                    <option>Galão</option>
                    <option>Caixa</option>
                    <option>Lata</option>
                    <option>Metro</option>
                    <option>Pacote</option>
                    <option>Quilo</option>
                    <option>Resma</option>
                    <option>Rolo</option>
                    <option>Saco</option>
                    <option>Unidade</option>
                </select>

                <label>Fator de Conversão</label>
                <input type="number" id="fator">

                <label>Quantidade Mínima em Estoque</label>
                <input type="number" id="minimo">

                <label>Localização</label>
                <input type="text" id="localizacao">

                <div class="modal-actions">
                    <button class="btn-cancel" @click="fecharModal('modalMaterial')">Cancelar</button>
                    <button class="btn-confirm" @click="salvarMaterial()">Salvar</button>
                </div>
            </div>
        </div>

        <!-- MODAL Incluir LOTE -->
        <div class="modal" id="modalLote">
            <div class="modal-content">
                <h2 id="titleModalLote">Lote</h2>

                <label>Lote</label>
                <input type="number" id="iptLote" min="1" @input="editLoteModal($event)" :value="loteModal.lote">

                <label>Quantidade</label>
                <input type="number" id="iptQTDLote" @input="editQtdItemModal($event)" min="1" :value="loteModal.quantidade">

                <label>Vencimento</label>
                <input type="date" id="iptVencLote" @input="editVencLoteModal($event)" :value="loteModal.vencimento">

                <div class="modal-actions">
                    <button class="btn-cancel" @click="fecharModal('modalLote')">Cancelar</button>
                    <button class="btn-confirm" @click="salvarLote()">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</main>




<?php $this->start("js"); ?>
<script>
    const {
        onBeforeUnmount,
        watch,
    } = Vue;

    createApp({
        setup() {
            const lines = 12;

            const materiais = ref([]);
            let paginaAtual = 0;
            let qtdMateriais = 0;
            let offset = 0;

            const carrinhoList = ref([]);
            const materiaisModal = ref([]);
            const tipoMov = ref(null);
            let offsetModal = 0;
            let qtdMateriaisModal = 0;
            let paginaAtualModal = 0;

            let linhaSelecionada = null;
            const materiaisAbertos = ref(new Set());
            const materiaisAbertosModal = ref(new Set());

            const loteModal = ref({});

            function toggleMaterial(id) {
                if (materiaisAbertos.value.has(id)) {
                    materiaisAbertos.value.delete(id);
                } else {
                    materiaisAbertos.value.add(id);
                }
            }

            function toggleMaterialModal(id) {
                if (tipoMov.value === "SAIDA") {
                    if (materiaisAbertosModal.value.has(id)) {
                        materiaisAbertosModal.value.delete(id);
                    } else {
                        materiaisAbertosModal.value.add(id);
                    }
                } else {
                    adicionarItem(id);
                }
            }

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

            // MATERIAIS
            function getMateriais(increment = 0) {

                offset += increment;

                const search = document.getElementById("buscarMaterial").value.trim();
                const fltrCategoria = document.getElementById('fltrCategoria').selectedOptions[0].value;
                const fltrStatusNormal = document.getElementById("fltrStatusNormal").checked;
                const fltrStatusAcabando = document.getElementById("fltrStatusAcabando").checked;
                const fltrStatusSemEstoque = document.getElementById("fltrStatusSemEstoque").checked;

                $.ajax({
                    type: "POST",
                    url: "<?= url("/") ?>",
                    data: {
                        offset: offset,
                        search: search,
                        fltrCategoria: fltrCategoria,
                        fltrStatusNormal: fltrStatusNormal,
                        fltrStatusAcabando: fltrStatusAcabando,
                        fltrStatusSemEstoque: fltrStatusSemEstoque,
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.code == 200) {
                            materiais.value = response.data.materiais.map(material => ({
                                ...material,
                                quantidade: Number(material.quantidade),
                                quantidade_minima: Number(material.quantidade_minima),
                                loteList: material.loteList || []
                            }));
                            qtdMateriais = response.data.qtdMateriais;

                            const navIdx = document.getElementById("nav-index");

                            const paginaFinal = Math.ceil(qtdMateriais <= lines ? 1 : qtdMateriais / lines);

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

                        } else {
                            alert(response.message);
                        }

                        ocultarLoading();
                    }
                });

                materiaisAbertos.value = new Set();
            }

            function editarMaterial(id_material) {

                linhaSelecionada = materiais.value.find((material) => material.id_material === id_material);

                if (linhaSelecionada === undefined) {
                    alert("[EDT] Material não encontrado!");
                    return false;
                }

                document.getElementById('descricao').value = linhaSelecionada.descricao;
                document.getElementById('categoria').value = linhaSelecionada.id_categoria;
                document.getElementById('unBase').value = linhaSelecionada.unidade_base;
                document.getElementById('unCompra').value = linhaSelecionada.unidade_compra;
                document.getElementById('fator').value = linhaSelecionada.fator_conversao;
                document.getElementById('minimo').value = linhaSelecionada.quantidade_minima;
                document.getElementById('localizacao').value = linhaSelecionada.localizacao;

                abrirModalMaterial("editar")
            }

            function excluirMaterial(id_material) {
                if (!confirm('Desejar realmente excluir o LOTE? \nClique "OK" para prosseguir')) {
                    return false
                }

                const material = materiais.value.find((material) => material.id_material === id_material);

                if (material === undefined) {
                    alert("[EXCL] Material não encontrado!");
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "<?= url("/excluirMaterial") ?>",
                    data: {
                        id_material: material.id_material
                    },
                    dataType: "json",
                    success: function(response) {

                        alert(response.message);

                    }
                });
            }

            function salvarMaterial() {

                const descricao = document.getElementById('descricao').value;
                const id_categoria = document.getElementById('categoria').value;
                const categoria = document.getElementById('categoria').selectedOptions[0].text;
                const unBase = document.getElementById('unBase').value;
                const unCompra = document.getElementById('unCompra').value;
                const fator = document.getElementById('fator').value;
                const minimo = document.getElementById('minimo').value;
                const localizacao = document.getElementById('localizacao').value;

                if (descricao === "" || descricao === undefined) {
                    alert("Campo de descrição vazio!");
                    return;
                }

                if (id_categoria === "" || id_categoria === undefined) {
                    alert("Campo de categoria não selecionado!");
                    return;
                }

                if (unBase === "" || unBase === undefined) {
                    alert("Campo de unidade base não selecionado!");
                    return;
                }

                if (unCompra === "" || unCompra === undefined) {
                    alert("Campo de Unidade por embalagem não selecionado!");
                    return;
                }

                if (fator === "" || fator === undefined) {
                    alert("Campo de fator de conversão vazio!");
                    return;
                }

                if (minimo === "" || minimo === undefined) {
                    alert("Campo de quantidade mínima vazio!");
                    return;
                }

                if (localizacao === "" || localizacao === undefined) {
                    alert("Campo de localização vazio!");
                    return;
                }

                const material = {
                    "id_material": null,
                    "id_categoria": id_categoria,
                    "categoria": categoria,
                    "descricao": descricao,
                    "quantidade": 0,
                    "unidade_base": unBase,
                    "unidade_compra": unCompra,
                    "fator_conversao": fator,
                    "quantidade_minima": minimo,
                    "custo_unitario": 0.00,
                    "status": "Sem Estoque",
                    "localizacao": localizacao,
                };

                if (linhaSelecionada !== null) material.id_material = linhaSelecionada.id_material;

                $.ajax({
                    type: "POST",
                    url: "<?= url("/salvarMaterial") ?>",
                    data: material,
                    dataType: "json",
                    success: function(response) {

                        alert(response.message);

                        if (response.code == 200) {

                            if (material.id_material === null) {
                                material.id_material = response.data.newId;
                                qtdMateriais = response.data.qtdMateriais;
                                materiais.value.unshift(material)
                                materiais.value.pop()

                            } else {

                                const idx = materiais.value.findIndex((material) => material.id_material === linhaSelecionada.id_material);
                                console.log(idx)
                                materiais.value[idx] = material;
                            }

                            // atualizarMaterialList();
                            fecharModal('modalMaterial');
                        }

                    }
                });
            }

            function getStatusClss(material) {
                if (material.quantidade == 0) return "out";

                else if (material.quantidade < material.quantidade_minima) return "low"

                else return "ok"
            }

            function gerarRelatorio() {
                window.open("<?= url("/documento/gerarRelatorio") ?>", "_blank");
            }

            // LOTE
            function editarLote(lote) {
                document.getElementById('modalLote').classList.add('active');

                loteModal.value = {
                    ...lote
                };
            }

            function excluirLote(lote) {
                if (!confirm('Desejar realmente excluir o LOTE? \nClique "OK" para prosseguir')) {
                    return false
                }

                if (lote.id_lote < 1 || lote.id_lote === undefined) {
                    alert("Número de ID de lote inválido!");
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "<?= url("/excluirLote") ?>",
                    data: lote.id_lote,
                    dataType: "json",
                    success: function(response) {
                        alert(response.message);

                        if (response.code == 200) {
                            let material = materiais.value.find((material) => material.id_material === lote.id_material);

                            const idxLote = material.loteList.findIndex((item) => item.id_lote == lote.id_lote);

                            material.loteList.splice(idxLote, 1);
                            material.quantidade -= Number(lote.quantidade);
                        }
                    }
                });
            }


            // MOVIMENTAÇÃO
            function getMateriaisModal(increment = 0) {

                offsetModal += increment;

                const search = document.getElementById("buscarMaterialModal").value.trim();

                $.ajax({
                    type: "POST",
                    url: "<?= url("/") ?>",
                    data: {
                        offset: offsetModal,
                        search: search
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.code == 200) {
                            materiaisModal.value = response.data.materiais.map(material => ({
                                ...material,
                                quantidade: Number(material.quantidade),
                                quantidade_minima: Number(material.quantidade_minima),
                                loteList: material.loteList || []
                            }));

                            qtdMateriaisModal = response.data.qtdMateriais;

                            const navIdx = document.getElementById("navModal-index");

                            const paginaFinal = Math.ceil(qtdMateriaisModal <= lines ? 1 : qtdMateriaisModal / lines);

                            paginaAtualModal = (offsetModal / lines) + 1;

                            const navVoltar = document.getElementById("navModalVoltar");
                            const navAvancar = document.getElementById("navModalAvancar");

                            if (paginaAtualModal == 1) {
                                navVoltar.disabled = true;
                                navVoltar.classList.add("disabled-button");
                            } else if (paginaAtualModal > 1) {
                                navVoltar.disabled = false;
                                navVoltar.classList.remove("disabled-button");
                            }

                            if (paginaAtualModal == paginaFinal) {
                                navAvancar.disabled = true;
                                navAvancar.classList.add("disabled-button");
                            } else if (paginaAtualModal < paginaFinal && paginaAtualModal > 1 || paginaFinal > 1) {
                                navAvancar.disabled = false;
                                navAvancar.classList.remove("disabled-button");
                            }

                            navIdx.innerHTML = `${paginaAtualModal}/${paginaFinal} Páginas`;

                        } else {
                            alert(response.message);
                        }
                    }
                });

                materiaisAbertosModal.value = new Set();

            }

            function adicionarItem(id_material = null, id_lote = null) {

                let material = {
                    ...materiaisModal.value.find((material) => material.id_material === id_material)
                };

                if (material === undefined) {
                    alert("[ADD] Material não encontrado!");
                    return false;
                }

                if (id_lote !== null) {

                    const lotes = carrinhoList.value.flatMap((material) => material.loteList);
                    const loteFind = lotes.find((lote) => lote.id_lote == id_lote);

                    if (loteFind !== undefined) {
                        alert("[ADD] Lote já incluído para movimentação!");
                        return false;
                    }

                    const lote = {
                        ...material.loteList.find((lote) => lote.id_lote === id_lote)
                    };

                    if (lote === undefined) {
                        alert("[ADD] Lote não encontrado!");
                        return false;
                    }

                    let carrinhoMaterial = carrinhoList.value.find((item) => item.id_material === material.id_material);

                    if (carrinhoMaterial === undefined) {
                        material.loteList = [];
                        material.loteList.push(lote);

                        carrinhoList.value.push({
                            ...material
                        });

                    } else {
                        carrinhoMaterial.loteList.push(lote);
                    }

                } else {
                    let materialCarrinho = carrinhoList.value.find((item) => item.id_material === material.id_material);

                    const loteList = [{
                        lote: null,
                        quantidade: 0,
                        vencimento: null
                    }];

                    if (materialCarrinho === undefined) {
                        material.loteList = loteList;

                        carrinhoList.value.push({
                            ...material
                        });
                    } else {
                        materialCarrinho.loteList.push(...loteList);
                    }
                }
            }

            function removerItem(idxMat, idxLot) {
                if (idxMat !== undefined || idxLot !== undefined) {
                    carrinhoList.value[idxMat].loteList.splice(idxLot, 1);

                } else {
                    alert("Material não identificado não identificado!");
                }
            }

            function criarMovimentacao() {
                const pontoResponsavel = getCookie('usuario');

                let codigoSigma = document.getElementById('codigoSigma').value;

                let pontoSolicitante = document.getElementById('pontoSolicitante').value;
                let nomeSolicitante = document.getElementById('nomeSolicitante').value.trim();

                if (pontoResponsavel === "") {
                    alert("Campo de Ponto do Responsável está vazio!");
                    return false;
                }

                if (tipoMov.value === "ENTRADA") {
                    pontoSolicitante = "";
                    nomeSolicitante = "";

                    if (codigoSigma === "") {
                        alert("Campo de Código de requisição do SIGMAS está vazio!");
                        return false;
                    }
                } else {
                    codigoSigma = "";

                    if (pontoSolicitante === "") {
                        alert("Campo de Ponto do Solicitante está vazio!");
                        return false;
                    }

                    if (nomeSolicitante === "") {
                        alert("Campo de Nome do Solicitante está vazio!");
                        return false;
                    }
                }

                if (carrinhoList.value.length < 1) {
                    alert("Não existe material para realizar movimentação!");
                    return false;
                }

                let erroQTD = false;

                carrinhoList.value.forEach(material => {
                    material.loteList.forEach(lote => {
                        if (lote.lote === undefined) {
                            alert(`O material "${material.descricao}" não possui NÚMERO de lote informado!`)
                            erroQTD = true
                            return false;
                        }
                        if (lote.quantidade < 1 || lote.quantidade === "") {
                            alert(`O material "${material.descricao}" não pode movimentar uma QUANTIDADE zerada!`)
                            erroQTD = true
                            return false;
                        }
                        if (lote.vencimento === undefined) {
                            alert(`O material "${material.descricao}" não possui VENCIMENTO de lote informado!`)
                            erroQTD = true
                            return false;
                        }
                    });
                });

                if (erroQTD) return false;

                $.ajax({
                    type: "POST",
                    url: "<?= url("/criarMovimentacao") ?>",
                    data: {
                        materiais: carrinhoList.value,
                        tipo: tipoMov.value,
                        pontoResponsavel: pontoResponsavel,
                        codigoSigma: codigoSigma,
                        pontoSolicitante: pontoSolicitante,
                        nomeSolicitante: nomeSolicitante,
                    },
                    dataType: "json",
                    success: function(response) {

                        alert(response.message);

                        if (response.code == 200) {

                            carrinhoList.value.forEach(material => {
                                let line = materiais.value.find((item) => item.id_material === material.id_material);
                                if (!line) return;

                                line.quantidade = Number(line.quantidade);
                                if (!line.loteList) line.loteList = [];

                                material.loteList.forEach(lote => {
                                    lote.quantidade = Number(lote.quantidade);
                                    if (tipoMov.value === 'ENTRADA') {
                                        const qtdConvertida = Number(material.fator_conversao) * lote.quantidade;
                                        line.quantidade += qtdConvertida;

                                        // Atualiza o objeto lote para refletir a quantidade convertida na sub-lista
                                        lote.quantidade = qtdConvertida;
                                        const data = new Date(lote.vencimento);
                                        lote.vencimentoFormatted = data.toLocaleDateString('pt-BR', {
                                            timeZone: 'UTC'
                                        });

                                        line.loteList.push(lote);
                                    } else {
                                        let idxLoteLine = line.loteList.findIndex((item) => Number(item.id_lote) === Number(lote.id_lote));

                                        if (idxLoteLine !== -1) {
                                            line.loteList[idxLoteLine].quantidade -= lote.quantidade;
                                            if (line.loteList[idxLoteLine].quantidade <= 0) {
                                                line.loteList.splice(idxLoteLine, 1);
                                            }
                                        }
                                        line.quantidade -= lote.quantidade;
                                    }
                                });

                                // Atualiza o status uma única vez após processar todos os lotes do material
                                if (line.quantidade <= 0) line.status = "Sem Estoque";
                                else if (line.quantidade < Number(line.quantidade_minima)) line.status = "Acabando";
                                else line.status = "Normal";
                            });

                            if (tipoMov.value === 'SAIDA') {
                                const link = document.createElement('a');
                                link.href = `<?= url("/documento/comprovanteSaida/") ?>${response.id_movimentacao}`;
                                link.target = '_blank';
                                link.click();
                            }

                            tipoMov.value = "SAIR";

                            fecharModal('modalMov');
                        }

                    }
                });
            }

            function editQtdItem(event, idxMat, idxLot) {
                carrinhoList.value[idxMat].loteList[idxLot].quantidade = Number(event.target.value);
            }

            function editVencLote(event, idxMat, idxLot) {
                carrinhoList.value[idxMat].loteList[idxLot].vencimento = event.target.value;
            }

            function editLote(event, idxMat, idxLot) {
                carrinhoList.value[idxMat].loteList[idxLot].lote = Number(event.target.value);
            }

            function editPontoSolicitante(event) {
                const ponto = document.getElementById('pontoSolicitante').value;

                if (ponto === "") return;

                $.ajax({
                    type: "POST",
                    url: "<?= url("/getSolicitante") ?>",
                    data: {
                        ponto: ponto
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.code == 200) {
                            document.getElementById('nomeSolicitante').value = response.data.nome;

                        } else {
                            alert(response.message);
                        }

                        ocultarLoading();
                    }
                });
            }


            // MODAL
            function abrirMovimentacao(tipo = "ENTRADA", id_item = null) {

                const ponto = getCookie('usuario');

                if (ponto == "") {
                    alert("Não há usuário autenticado para realizar essa ação!");
                    return false;
                }

                document.getElementById('pontoResponsavel').value = ponto;

                tipoMov.value = tipo;

                if (tipoMov.value === 'ENTRADA') {
                    document.getElementById('tituloMov').innerText = 'Entrada de Material';
                    document.getElementById('areaSolicitante').style.display = "none";
                    document.getElementById('areaSigma').style.display = "initial";

                    document.getElementById("movEntrada").checked = true;
                } else {
                    document.getElementById('tituloMov').innerText = 'Saída de Material';
                    document.getElementById('areaSolicitante').style.display = 'flex';
                    document.getElementById('areaSigma').style.display = "none";

                    document.getElementById("movSaida").checked = true;
                }

                if (id_item !== null && id_item > 0) {
                    let material = {};

                    if (tipoMov.value === 'ENTRADA') {
                        material = {
                            ...materiais.value.find((material) => material.id_material == id_item)
                        };

                        material.loteList = [{
                            lote: null,
                            quantidade: 0,
                            vencimento: null
                        }];

                    } else {

                        const lotes = materiais.value.flatMap((material) => material.loteList);
                        const loteFind = lotes.find((lote) => lote.id_lote == id_item);

                        material = {
                            ...materiais.value.find((material) => material.id_material == loteFind.id_material),
                            loteList: [{
                                ...loteFind
                            }]
                        };
                    }

                    if (material === undefined) {
                        alert("[MOV] Material não encontrado!");
                        return false;
                    }

                    if (material === undefined) {
                        alert("[INICAR] Material não encontrado!");
                        return false;
                    }

                    carrinhoList.value.push({
                        ...material
                    });
                }

                getMateriaisModal();

                document.getElementById('modalMov').classList.add('active');
            }

            function abrirModalMaterial(evento = "novo") {
                document.getElementById('modalMaterial').classList.add('active');

                if (evento === "novo") {
                    document.getElementById('titleModalMaterial').innerText = "Incluir Material";
                } else {
                    document.getElementById('titleModalMaterial').innerText = "Editar Material";
                }
            }

            function editQtdItemModal(event) {
                loteModal.value.quantidade = Number(event.target.value);
            }

            function editVencLoteModal(event) {
                loteModal.value.vencimento = event.target.value;
            }

            function editLoteModal(event) {
                loteModal.value.lote = Number(event.target.value);
            }

            function salvarLote() {
                if (loteModal.value.lote < 1) {
                    alert("[ERRO][Salvar LOTE 01] Informação de LOTE vazia!");
                    return false;
                }
                if (loteModal.value.quantidade < 0) {
                    alert("[ERRO][Salvar LOTE 02] Informação de QUANTIDADE inválida!");
                    return false;
                }
                if (loteModal.value.vencimento === "") {
                    alert("[ERRO][Salvar LOTE 03] Informação de VENCIMENTO vazia!");
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "<?= url("/salvarLote") ?>",
                    data: loteModal.value,
                    dataType: "json",
                    success: function(response) {

                        alert(response.message);

                        if (response.code == 200) {
                            let material = materiais.value.find((material) => material.id_material === loteModal.value.id_material);

                            if (material === undefined) {
                                alert("[ERRO][Salvar LOTE 04] Material não encontrado!");
                                return false;
                            }

                            let lote = material.loteList.find((lote) => lote.id_lote === loteModal.value.id_lote);

                            if (lote === undefined) {
                                alert("[ERRO][Salvar LOTE 05] Lote não encontrado!");
                                return false;
                            }

                            material.quantidade -= lote.quantidade;
                            material.quantidade += loteModal.value.quantidade;

                            lote.lote = loteModal.value.lote;
                            lote.quantidade = loteModal.value.quantidade;
                            lote.vencimento = loteModal.value.vencimento;

                            const data = new Date(lote.vencimento);

                            lote.vencimentoFormatted = data.toLocaleDateString('pt-BR', {
                                timeZone: 'UTC'
                            });

                            fecharModal('modalLote');
                        }

                    }
                });


            }

            function fecharModal(id) {

                if (id === "modalMaterial") {
                    document.getElementById('descricao').value = "";
                    document.getElementById('fator').value = "";
                    document.getElementById('minimo').value = "";
                    document.getElementById('localizacao').value = "";
                } else if (id === "modalLote") {
                    document.getElementById('iptLote').value = "";
                    document.getElementById('iptQTDLote').value = "";
                    document.getElementById('iptVencLote').value = "";
                } else {

                    let fechar = true;

                    const codigoSigma = document.getElementById('codigoSigma');
                    const pontoResponsavel = document.getElementById('pontoResponsavel');
                    const pontoSolicitante = document.getElementById('pontoSolicitante');
                    const nomeSolicitante = document.getElementById('nomeSolicitante');
                    const buscarMaterialModal = document.getElementById("buscarMaterialModal");

                    if (tipoMov.value != "SAIR") {

                        if (codigoSigma.value !== "" || pontoResponsavel.value !== "" || pontoSolicitante.value !== "" ||
                            nomeSolicitante.value !== "" || carrinhoList.value.length > 0) fechar = confirm("As informações da movimentações serão perdidas! \nDesja continuar?")

                        if (!fechar) return false;
                    }

                    codigoSigma.value = "";
                    pontoResponsavel.value = "";
                    pontoSolicitante.value = "";
                    nomeSolicitante.value = "";
                    buscarMaterialModal.value = "";

                    carrinhoList.value = [];
                    materiaisAbertosModal.value = new Set();
                }

                document.getElementById(id).classList.remove('active');

                linhaSelecionada = null;
                tipoMov.value = null;
            }

            onMounted(() => {
                mostrarLoading();
                getMateriais();

                document.getElementById("buscarMaterial").addEventListener("keyup", function() {
                    offset = 0;
                    getMateriais();
                });

                document.getElementById("buscarMaterialModal").addEventListener("keyup", function() {
                    offsetModal = 0
                    getMateriaisModal();
                });

                document.getElementById('fltrCategoria').addEventListener("change", function() {
                    offset = 0;
                    getMateriais();
                });

                document.querySelectorAll('input[name="evento"]').forEach(radio => {
                    radio.addEventListener('click', function() {

                        const idx = carrinhoList.value.findIndex((material) => material.quantidade == 0);

                        carrinhoList.value = [];

                        tipoMov.value = this.value;

                        document.getElementById('tituloMov').innerText = tipoMov.value === 'ENTRADA' ? 'Entrada de Material' : 'Saída de Material';
                        document.getElementById('areaSolicitante').style.display = tipoMov.value === 'ENTRADA' ? "none" : 'flex';
                        document.getElementById('areaSigma').style.display = tipoMov.value === 'ENTRADA' ? "initial" : "none";

                    });
                });

                document.querySelectorAll('.fltrCheck').forEach(chcks => {
                    chcks.addEventListener('click', function(chck) {
                        offset = 0;
                        getMateriais();
                    });
                });
            });

            return {
                lines,
                tipoMov,
                materiais,
                carrinhoList,
                materiaisModal,
                abrirMovimentacao,
                adicionarItem,
                removerItem,
                getMateriais,
                getMateriaisModal,
                salvarMaterial,
                editarMaterial,
                excluirMaterial,
                fecharModal,
                getStatusClss,
                criarMovimentacao,
                editQtdItem,
                editVencLote,
                editLote,
                abrirModalMaterial,
                materiaisAbertos,
                toggleMaterial,
                materiaisAbertosModal,
                toggleMaterialModal,
                loteModal,
                editarLote,
                salvarLote,
                editQtdItemModal,
                editVencLoteModal,
                editLoteModal,
                excluirLote,
                gerarRelatorio,
                editPontoSolicitante
            };
        },
    }).mount("#app");
</script>
<?php $this->end("js"); ?>