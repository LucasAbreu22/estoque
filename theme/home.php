<?php $this->layout("_theme", ["title" => $title]); ?>

<main>
    <div id="app">
        <div class="top-actions">
            <div class="fltrArea">
                <div class="fltrColumn">
                    <label> <b> Digite o código ou descrição:</b> </label>
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

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
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

                <tr v-for="(material, i) in materiais" :key="i">
                    <td class="codigo">{{material.codigo}}</td>
                    <td class="left descricao">{{material.descricao}}</td>
                    <td class="left">{{material.categoria}}</td>
                    <td class="saldo">{{material.quantidade}}</td>
                    <td class="left">{{material.unidade_base}}</td>
                    <td class="left">{{material.unidade_compra}}</td>
                    <td class="minimo">{{material.quantidade_minima}}</td>
                    <td>{{material.localizacao}}</td>
                    <td><span class="badge" :class="getStatusClss(material)">{{material.status}}</span></td>
                    <td class="actions">
                        <button class="btn-entry" @click="abrirMovimentacao('ENTRADA', material.id_material)">Entrada 🡇</button>
                        <button class="btn-alert" @click="abrirMovimentacao('SAIDA', material.id_material)">Saída 🡅</button>
                        <button class="btn-edit" @click="editarMaterial(material.id_material)">Editar</button>
                        <button class="btn-exit" @click="excluirMaterial(material.id_material)">Excluir</button>
                    </td>
                </tr>

            </tbody>
        </table>

        <div id="nav-table">
            <button class="btn-nav disabled-button" id="navVoltar" @click="getMateriais(-lines)" disabled>
                ◄ </button>
            <span id="nav-index">1</span>

            <button class="btn-nav disabled-button" id="navAvancar" @click="getMateriais(lines)" disabled> ► </button>
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
                    <label>Código do sigma</label>
                    <input type="number" id="codigoSigma">
                </div>

                <label>Ponto responsável</label>
                <input type="number" id="pontoResponsavel">

                <div id="areaSolicitante">
                    <div id="areaPontoSolicitante">
                        <label>Ponto solicitante</label>
                        <input type="number" id="pontoSolicitante">
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
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>QTD</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaCarrinho">
                            <tr v-for="(material, i) in carrinhoList" :key="i">
                                <td class="codigo">{{material.codigo}}</td>
                                <td class="left descricao">{{material.descricao}}</td>
                                <td><input type="number" @input="editQtdItem($event, i)" min="1" :value="material.quantidadeMov"></td>
                                <td class="actions">
                                    <button class="btn-exit" @click="removerItem(material.id_material)">▼</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h4>Materiais almox</h4>
                <input type="text" id="buscarMaterialModal" placeholder="Digite o código ou descrição">
                <div id="areaAlmox">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>Saldo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaMateriaisModal">
                            <tr v-for="(material, i) in materiaisModal" :key="i">
                                <td class="codigo">{{material.codigo}}</td>
                                <td class="left descricao">{{material.descricao}}</td>
                                <td>{{material.quantidade}}</td>
                                <td class="actions">
                                    <button :class="tipoMov === 'SAIDA' && material.quantidade == 0? 'disabled-button ': 'btn-entry '" @click="adicionarItem(material.id_material)">▲</button>
                                </td>
                            </tr>
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
                <h2 id="titleModalMaterial">Incluir Material</h2>

                <label>Código</label>
                <input type="text" id="codigo">

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
                    <option>Unidade</option>
                    <option>Folha</option>
                    <option>Metro</option>
                </select>

                <label>Unidade de Compra</label>
                <select id="unCompra">
                    <option>Resma</option>
                    <option>Caixa</option>
                    <option>Rolo</option>
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
            }

            function editarMaterial(id_material) {

                linhaSelecionada = materiais.value.find((material) => material.id_material === id_material);

                if (linhaSelecionada === undefined) {
                    alert("[EDT] Material não encontrado!");
                    return false;
                }

                document.getElementById('codigo').value = linhaSelecionada.codigo;
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

                const codigo = document.getElementById('codigo').value;
                const descricao = document.getElementById('descricao').value;
                const id_categoria = document.getElementById('categoria').value;
                const categoria = document.getElementById('categoria').selectedOptions[0].text;
                const unBase = document.getElementById('unBase').value;
                const unCompra = document.getElementById('unCompra').value;
                const fator = document.getElementById('fator').value;
                const minimo = document.getElementById('minimo').value;
                const localizacao = document.getElementById('localizacao').value;

                let materialReg = {}

                if (codigo === "" || codigo === undefined) {
                    alert("Campo de código vazio!");
                    return;
                }

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
                    alert("Campo de unidade de compra não selecionado!");
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
                    "codigo": codigo,
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

                if (linhaSelecionada !== null) {

                    const codigoLinhaSelec = linhaSelecionada.querySelector('.codigo').innerText;
                    materialReg = materiais.find((material) => material.codigo === codigoLinhaSelec);

                    material.id_material = materialReg.id_material;
                }

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

                                const idx = materiais.value.findIndex((material) => material.id_material === materialReg.id_material);

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
            }

            function adicionarItem(id_material) {

                const material = materiaisModal.value.find((material) => material.id_material === id_material);
                if (material === undefined) {
                    alert("[ADD] Material não encontrado!");
                    return false;
                }

                if (carrinhoList.value.findIndex((material) => material.id_material === id_material) >= 0) {
                    alert("Material já incluído para movimentação!");
                    return false;
                }

                if (tipoMov.value === 'SAIDA' && material.quantidade == 0) {
                    alert("Não é possivel dar saída para material sem estoque!");
                    return false;
                }

                material.quantidadeMov = material.quantidade;

                carrinhoList.value.push({
                    ...material
                });
            }

            function removerItem(id_material) {

                if (id_material > 0) carrinhoList.value = carrinhoList.value.filter((material) => material.id_material !== id_material);

                else alert("ID não identificado!");

            }

            function criarMovimentacao() {
                const pontoResponsavel = document.getElementById('pontoResponsavel').value.trim();

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
                        alert("Campo de Código do SiGMA está vazio!");
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

                carrinhoList.value.forEach(element => {
                    if (element.quantidadeMov < 1 || element.quantidadeMov === "") {
                        alert(`O material "${element.descricao}" não pode movimentar uma quantidade zerada!`)
                        erroQTD = true
                        return false;
                    }
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

                                line.quantidade = parseFloat(line.quantidade)

                                material.quantidadeMov = parseFloat(material.quantidadeMov)

                                line.quantidade = tipoMov.value === 'ENTRADA' ? line.quantidade + material.quantidadeMov : line.quantidade - material.quantidadeMov;

                                if (line.quantidade < 0) line.quantidade = 0;

                                if (material.quantidade == 0) line.status = "Sem Estoque";

                                else if (material.quantidade < material.quantidade_minima) line.status = "Acabando"

                                else line.status = "Normal"

                                material = line;
                            });

                            tipoMov.value = "SAIR";
                            // atualizarMaterialList();

                            fecharModal('modalMov');
                        }

                    }
                });
            }

            function editQtdItem(event, index) {

                carrinhoList.value[index].quantidadeMov = Number(event.target.value);
            }

            // MODAL
            function abrirMovimentacao(tipo = "ENTRADA", id_material = null) {

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

                if (id_material !== null && id_material > 0) {
                    let material = materiais.value.find((material) => material.id_material == id_material);

                    if (material === undefined) {
                        alert("[MOV] Material não encontrado!");
                        return false;
                    }
                    material.quantidadeMov = material.quantidade;

                    if (material === undefined) {
                        alert("[INICAR] Material não encontrado!");
                        return false;
                    }

                    if (tipoMov.value === 'SAIDA' && material.quantidade == 0) {
                        alert("Não é possivel dar saída para material sem estoque!");
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

            function fecharModal(id) {

                if (id === "modalMaterial") {
                    document.getElementById('codigo').value = "";
                    document.getElementById('descricao').value = "";
                    document.getElementById('fator').value = "";
                    document.getElementById('minimo').value = "";
                    document.getElementById('localizacao').value = "";
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
                    // atualizarCarrinhoList();
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

                        if (this.value === "SAIDA" && idx >= 0) {

                            if (confirm("Existem materiais sem saldo que serão removidos!\nDeseja continuar?")) {
                                carrinhoList.value = carrinhoList.value.filter((material) => material.quantidade > 0);

                            } else return false;
                        }

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
                abrirModalMaterial
            };
        },
    }).mount("#app");
</script>
<?php $this->end("js"); ?>