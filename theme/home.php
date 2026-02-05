<?php $this->layout("_theme", ["title" => $title]); ?>

<main>
    <div class="top-actions">
        <input type="text" id="buscarMaterial" placeholder="Digite o código ou descrição">

        <div>
            <button class="btn-add btn-entry" onclick="abrirMovimentacao()">Novas movimentações</button>
            <button class="btn-add" onclick="abrirModalMaterial()">+ Novo Material</button>
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
            <tr>
                <td>PAP-001</td>
                <td>Papel Couchê 150g</td>
                <td>Papéis</td>
                <td class="saldo">2350</td>
                <td>folha</td>
                <td>resma</td>
                <td>1000</td>
                <td>Prateleira A1</td>
                <td><span class="badge ok">Normal</span></td>
                <td class="actions">
                    <button class="btn-entry" onclick="abrirMovimentacao('ENTRADA', this)">Entrada</button>
                    <button class="btn-exit" onclick="abrirMovimentacao('SAIDA', this)">Saída</button>
                    <button class="btn-edit">Editar</button>
                </td>
            </tr>
        </tbody>
    </table>
    <div id="nav-table">
        <button class="btn-nav disabled-button" id="navVoltar" onclick="getMateriais(-lines)" disabled>
            ◄ </button>
        <span id="nav-index">1</span>

        <button class="btn-nav disabled-button" id="navAvancar" onclick="getMateriais(lines)" disabled> ► </button>
    </div>
</main>

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

                </tbody>
            </table>
        </div>
        <div id="nav-table">
            <button class="btn-nav disabled-button" id="navModalVoltar" onclick="getMateriaisModal(-lines)" disabled>
                ◄ </button>
            <span id="navModal-index">1</span>

            <button class="btn-nav disabled-button" id="navModalAvancar" onclick="getMateriaisModal(lines)" disabled> ► </button>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="fecharModal('modalMov')">Cancelar</button>
            <button class="btn-confirm" onclick="criarMovimentacao()">Confirmar</button>
        </div>
    </div>
</div>


<!-- MODAL NOVO MATERIAL -->
<div class="modal" id="modalMaterial">
    <div class="modal-content">
        <h2 id="titleModalMaterial">Novo Material</h2>

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

        <label>Quantidade Mínima</label>
        <input type="number" id="minimo">

        <label>Localização</label>
        <input type="text" id="localizacao">

        <div class="modal-actions">
            <button class="btn-cancel" onclick="fecharModal('modalMaterial')">Cancelar</button>
            <button class="btn-confirm" onclick="salvarMaterial()">Salvar</button>
        </div>
    </div>
</div>
</body>


<?php $this->start("js"); ?>
<script>
    let carrinhoList = [];
    let materiais = [];
    let qtdMateriais = 0;
    let paginaAtual = 0;
    let linhaSelecionada = null;
    let offset = 0

    let tipoMov = null;
    let offsetModal = 0
    let qtdMateriaisModal = 0;
    let paginaAtualModal = 0;
    let linhaSelecionadaModal = null;

    const lines = 12;

    mostrarLoading()
    getMateriais();

    document.getElementById("buscarMaterial").addEventListener("keyup", function() {
        offset = 0
        getMateriais();
    });

    document.getElementById("buscarMaterialModal").addEventListener("keyup", function() {
        offsetModal = 0
        getMateriaisModal();
    });

    document.querySelectorAll('input[name="evento"]').forEach(radio => {
        radio.addEventListener('click', function() {

            const idx = carrinhoList.findIndex((material) => material.quantidade == 0);

            if (this.value === "SAIDA" && idx >= 0) {

                if (confirm("Existem materiais sem saldo que serão removidos!\nDeseja continuar?")) {
                    carrinhoList = carrinhoList.filter((material) => material.quantidade > 0);
                    atualizarCarrinhoList();

                } else return false;
            }

            tipoMov = this.value;

            document.getElementById('tituloMov').innerText = tipoMov === 'ENTRADA' ? 'Entrada de Material' : 'Saída de Material';
            document.getElementById('areaSolicitante').style.display = tipoMov === 'ENTRADA' ? "none" : 'flex';
            document.getElementById('areaSigma').style.display = tipoMov === 'ENTRADA' ? "initial" : "none";

            atualizarMaterialListModal()
        });
    });

    function getMateriais(increment = 0) {

        offset += increment;

        const search = document.getElementById("buscarMaterial").value.trim();


        $.ajax({
            type: "POST",
            url: "<?= url("/") ?>",
            data: {
                offset: offset,
                search: search
            },
            dataType: "json",
            success: function(response) {

                if (response.code == 200) {
                    materiais = response.data.materiais;
                    qtdMateriais = response.data.qtdMateriais;

                    atualizarMaterialList();
                } else {
                    alert(response.message);
                }

                ocultarLoading();
            }
        });
    }

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
                    materiaisModal = response.data.materiais;
                    qtdMateriaisModal = response.data.qtdMateriais;

                    atualizarMaterialListModal();
                } else {
                    alert(response.message);
                }
            }
        });
    }

    function atualizarMaterialList() {
        const tabela = document.getElementById('tabelaMateriais');

        tabela.innerHTML = "";

        materiais.forEach(material => {
            const tr = document.createElement('tr');

            const quantidade = Number(material.quantidade);
            const minimo = Number(material.quantidade_minima);

            let statusClss = "ok";

            if (quantidade == 0) statusClss = "out";

            else if (quantidade < minimo) statusClss = "low"

            tr.innerHTML = `
            <td class="codigo">${material.codigo}</td>
            <td class="left descricao">${material.descricao}</td>
            <td class="left">${material.categoria}</td>
            <td class="saldo">${material.quantidade}</td>
            <td class="left">${material.unidade_base}</td>
            <td class="left">${material.unidade_compra}</td>
            <td class="minimo">${material.quantidade_minima}</td>
            <td>${material.localizacao}</td>
            <td><span class="badge ${statusClss}">${material.status}</span></td>
            <td class="actions">
                <button class="btn-entry" onclick="abrirMovimentacao('ENTRADA', this)">Entrada 🡇</button>
                <button class="btn-alert" onclick="abrirMovimentacao('SAIDA', this)">Saída 🡅</button>
                <button class="btn-edit" onclick="editarMaterial(this)">Editar</button>
                <button class="btn-exit" onclick="excluirMaterial(this)">Excluir</button>
            </td>
        `;
            tabela.appendChild(tr);

        });

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
    }

    function atualizarMaterialListModal() {

        const tabela = document.getElementById('tabelaMateriaisModal');

        tabela.innerHTML = "";

        materiaisModal.forEach(material => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
            <td class="codigo">${material.codigo}</td>
            <td class="left descricao">${material.descricao}</td>
            <td>${material.quantidade}</td>
            <td class="actions">
                <button class="${tipoMov === "SAIDA" && material.quantidade == 0? "disabled-button ": "btn-entry "}" onclick="adicionarItem(this)">▲</button>
            </td>
        `;
            tabela.appendChild(tr);

        });

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

    }

    function atualizarCarrinhoList() {
        const tabela = document.getElementById('tabelaCarrinho');

        tabela.innerHTML = "";

        carrinhoList.forEach(material => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
            <td class="codigo">${material.codigo}</td>
            <td class="left descricao">${material.descricao}</td>
            <td><input type="number" onkeyup="editQtdItem(this)" min="1" value="${material.quantidadeMov}"></td>
            <td class="actions">
                <button class="btn-exit" onclick="removerItem(this)" >▼</button>
            </td>
        `;
            tabela.appendChild(tr);

        });

    }

    function abrirMovimentacao(tipo = "ENTRADA", btn = null) {

        tipoMov = tipo;

        if (tipoMov === 'ENTRADA') {
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

        if (btn !== null) {
            linhaSelecionada = btn.closest('tr');

            const codigo = linhaSelecionada.querySelector('.codigo').innerText;
            let line = materiais.find((material) => material.codigo == codigo);
            line.quantidadeMov = line.quantidade;

            if (line === undefined) {
                alert("[INICAR] Material não encontrado!");
                return false;
            }

            if (tipoMov === 'SAIDA' && line.quantidade == 0) {
                alert("Não é possivel dar saída para material sem estoque!");
                return false;
            }

            carrinhoList.push(line);
            atualizarCarrinhoList();
        }

        getMateriaisModal();

        document.getElementById('modalMov').classList.add('active');
    }

    function editarMaterial(btn) {
        linhaSelecionada = btn.closest('tr');

        const codigo = linhaSelecionada.querySelector('.codigo').innerText;
        const material = materiais.find((material) => material.codigo === codigo);

        document.getElementById('codigo').value = material.codigo;
        document.getElementById('descricao').value = material.descricao;
        document.getElementById('categoria').value = material.id_categoria;
        document.getElementById('unBase').value = material.unidade_base;
        document.getElementById('unCompra').value = material.unidade_compra;
        document.getElementById('fator').value = material.fator_conversao;
        document.getElementById('minimo').value = material.quantidade_minima;
        document.getElementById('localizacao').value = material.localizacao;

        abrirModalMaterial("editar")
    }

    function excluirMaterial(btn) {
        linhaSelecionada = btn.closest('tr');

        const codigo = linhaSelecionada.querySelector('.codigo').innerText;
        const material = materiais.find((material) => material.codigo === codigo);

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

    function editQtdItem(inpt) {
        linhaSelecionada = inpt.closest('tr');

        const codigo = linhaSelecionada.querySelector('.codigo').innerText;

        let material = carrinhoList.find((material) => material.codigo === codigo);

        if (material === undefined) {
            alert("[EDT] Material não encontrado!");
            return false;
        }

        material.quantidadeMov = inpt.value;
    }

    function removerItem(btn) {
        linhaSelecionada = btn.closest('tr');

        const codigo = linhaSelecionada.querySelector('.codigo').innerText;

        carrinhoList = carrinhoList.filter((material) => material.codigo !== codigo);

        atualizarCarrinhoList();
    }

    function adicionarItem(btn) {
        linhaSelecionada = btn.closest('tr');

        const codigo = linhaSelecionada.querySelector('.codigo').innerText;

        const material = materiaisModal.find((material) => material.codigo === codigo);

        if (carrinhoList.findIndex((material) => material.codigo === codigo) >= 0) {
            alert("Material já incluído para movimentação!");
            return false;
        }

        if (tipoMov === 'SAIDA' && material.quantidade == 0) {
            alert("Não é possivel dar saída para material sem estoque!");
            return false;
        }

        material.quantidadeMov = material.quantidade;

        carrinhoList.push(material)

        atualizarCarrinhoList();
    }

    function abrirModalMaterial(evento = "novo") {
        document.getElementById('modalMaterial').classList.add('active');

        if (evento === "novo") {
            document.getElementById('titleModalMaterial').innerText = "Novo Material";
        } else {
            document.getElementById('titleModalMaterial').innerText = "Editar Material";
        }
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
                        materiais.unshift(material)
                        materiais.pop()

                    } else {

                        const idx = materiais.findIndex((material) => material.id_material === materialReg.id_material);

                        materiais[idx] = material;
                    }

                    atualizarMaterialList();
                    fecharModal('modalMaterial');
                }

            }
        });
    }

    function criarMovimentacao() {
        const pontoResponsavel = document.getElementById('pontoResponsavel').value;
        let quantidade = document.getElementById('quantidadeMov').value;
        let codigoSigma = null;
        let pontoSolicitante = null;
        let nomeSolicitante = null;

        if (pontoResponsavel === "" || pontoResponsavel === undefined) {
            alert("Campo de ponto vazio!");
            return;
        }

        if (quantidade === "" || quantidade === undefined) {
            alert("Campo de quantidade vazio!");
            return;
        }

        if (quantidade === "0") {
            alert("Campo de quantidade com valor inválido!");
            return;
        }

        if (tipoMov == "SAIDA") {

            pontoSolicitante = document.getElementById('pontoSolicitante').value;
            nomeSolicitante = document.getElementById('nomeSolicitante').value;

            if (pontoSolicitante === "" || pontoSolicitante === undefined) {
                alert("Campo de ponto do solicitante vazio!");
                return;
            }

            if (nomeSolicitante === "" || nomeSolicitante === undefined) {
                alert("Campo de nome do solicitante vazio!");
                return;
            }

        } else {
            codigoSigma = document.getElementById('codigoSigma').value;

            if (codigoSigma === "" || codigoSigma === undefined) {
                alert("Campo de código de sigma vazio!");
                return;
            }
        }


        const codigoLinhaSelec = linhaSelecionada.querySelector('.codigo').innerText;
        const materialReg = materiais.find((material) => material.codigo === codigoLinhaSelec);

        const movimentacao = {
            "codigoSigma": codigoSigma,
            "pontoResponsavel": pontoResponsavel,
            "pontoSolicitante": pontoSolicitante,
            "nomeSolicitante": nomeSolicitante,
            "quantidade": quantidade,
            "id_material": materialReg.id_material,
            "tipo": tipoMov
        };

        $.ajax({
            type: "POST",
            url: "<?= url("/criarMovimentacao") ?>",
            data: movimentacao,
            dataType: "json",
            success: function(response) {

                alert(response.message);

                if (response.code == 200) {

                    quantidade = parseFloat(quantidade);

                    const saldoCell = linhaSelecionada.querySelector('.saldo');

                    let saldo = parseFloat(saldoCell.innerText);
                    saldo = tipoMov === 'ENTRADA' ? saldo + quantidade : saldo - quantidade;

                    if (saldo < 0) saldo = 0;

                    saldoCell.innerText = saldo;

                    const minimoCell = linhaSelecionada.querySelector('.minimo');
                    const minimo = parseFloat(minimoCell.innerText);

                    const classList = linhaSelecionada.querySelector('.badge').classList;
                    const atualClass = classList[classList.length - 1]

                    let classStatus = "ok"
                    let txt = "Normal";

                    if (saldo === 0) {
                        classStatus = "out";
                        txt = "Sem Estoque"
                    } else if (saldo <= minimo) {
                        classStatus = "low";
                        txt = "Acabando"
                    }

                    linhaSelecionada.querySelector('.badge').classList.remove(atualClass);
                    linhaSelecionada.querySelector('.badge').classList.add(classStatus);
                    linhaSelecionada.querySelector('.badge').innerHTML = txt

                    fecharModal('modalMov');
                }

            }
        });
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

            if (carrinhoList.length > 0) fechar = confirm("As informações da movimentações serão perdidas! \nDesja continuar?")

            if (!fechar) return false;

            document.getElementById('codigoSigma').value = "";
            document.getElementById('pontoResponsavel').value = "";
            document.getElementById('pontoSolicitante').value = "";
            document.getElementById('nomeSolicitante').value = "";
            document.getElementById("buscarMaterialModal").value = "";

            carrinhoList = [];
            atualizarCarrinhoList();
        }

        document.getElementById(id).classList.remove('active');

        linhaSelecionada = null;
        tipoMov = null;
    }
</script>
<?php $this->end("js"); ?>