<?php $this->layout("_theme", ["title" => $title]); ?>

<main>

    <div id="app">
        <div class="top-actions">
            <div class="fltrArea">
                <div class="fltrColumn">
                    <label for="buscarUsuario"> <b> Buscar por nome ou ponto:</b> </label>
                    <input type="text" id="buscarUsuario" v-model="search" @keyup="buscar">
                </div>
            </div>

            <button class="btn-add" @click="abrirModalNovo()">+ Novo Usuário</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ponto</th>
                        <th>Nome</th>
                        <th>Criado em</th>
                        <th>Editado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabelaUsuarios">
                    <tr v-for="(usuario, i) in usuarios" :key="i">
                        <td>{{ usuario.ponto }}</td>
                        <td class="left">{{ usuario.nome }}</td>
                        <td>{{ usuario.data_criacao }}</td>
                        <td>{{ usuario.data_edicao ?? '-' }}</td>
                        <td class="actions">
                            <button class="btn-edit" @click="editarUsuario(usuario)">Editar</button>
                            <button class="btn-exit" @click="excluirUsuario(usuario)">Excluir</button>
                        </td>
                    </tr>
                    <tr v-if="usuarios.length === 0">
                        <td colspan="5" class="left">Nenhum usuário encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="nav-table" style="display: flex; justify-content: flex-start; align-items: center; position: relative; margin-top: 20px;">
            <button class="btn-nav" id="navVoltar" @click="getUsuarios(-lines)"> ◄ </button>
            <span id="nav-index" style="margin: 0 15px;">1</span>
            <button class="btn-nav" id="navAvancar" @click="getUsuarios(lines)"> ► </button>
        </div>

        <!-- MODAL Incluir/Editar USUÁRIO -->
        <div class="modal" id="modalUsuario">
            <div class="modal-content" style="width: 420px;">
                <h2 id="titleModalUsuario">{{ usuarioModal.id_usuario ? 'Editar Usuário' : 'Novo Usuário' }}</h2>

                <label>Nome <span style="color: var(--danger);">*</span></label>
                <input type="text" id="iptNome" v-model="usuarioModal.nome">

                <label>Ponto <span style="color: var(--danger);">*</span></label>
                <input type="number" id="iptPonto" v-model="usuarioModal.ponto">

                <label>Senha <small>(opcional)</small></label>
                <input type="password" id="iptSenha" v-model="usuarioModal.senha" autocomplete="new-password">

                <div class="modal-actions">
                    <button class="btn-cancel" @click="fecharModal('modalUsuario')">Cancelar</button>
                    <button class="btn-confirm" @click="salvarUsuario()">Salvar</button>
                </div>
            </div>
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
            const usuarios = ref([]);
            const search = ref("");
            let qtdUsuarios = 0;
            let offset = 0;
            const lines = 13;

            const usuarioModal = ref({
                id_usuario: null,
                nome: "",
                ponto: "",
                senha: ""
            });

            function abrirModal(id) {
                document.getElementById(id).classList.add('active');
            }

            function fecharModal(id) {
                document.getElementById(id).classList.remove('active');
            }

            function abrirModalNovo() {
                usuarioModal.value = {
                    id_usuario: null,
                    nome: "",
                    ponto: "",
                    senha: ""
                };
                abrirModal('modalUsuario');
            }

            function editarUsuario(usuario) {
                usuarioModal.value = {
                    id_usuario: usuario.id_usuario,
                    nome: usuario.nome,
                    ponto: usuario.ponto,
                    senha: ""
                };
                abrirModal('modalUsuario');
            }

            function buscar() {
                offset = 0;
                getUsuarios();
            }

            function getUsuarios(increment = 0) {
                offset += increment;
                if (offset < 0) offset = 0;

                mostrarLoading();

                $.ajax({
                    type: "POST",
                    url: "<?= url("/usuarios/") ?>",
                    data: {
                        offset: offset,
                        search: search.value.trim()
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.code === 200) {
                            usuarios.value = response.data.usuarios;
                            qtdUsuarios = response.data.qtdUsuarios;

                            const navIdx = document.getElementById("nav-index");
                            const paginaFinal = Math.ceil(qtdUsuarios <= lines ? 1 : qtdUsuarios / lines);
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
                        alert("Erro ao carregar usuários!");
                    }
                });
            }

            function salvarUsuario() {
                if (usuarioModal.value.nome === undefined || usuarioModal.value.nome.trim() === "") {
                    alert("Informação de NOME é obrigatória!");
                    return false;
                }
                if (usuarioModal.value.ponto === undefined || String(usuarioModal.value.ponto).trim() === "") {
                    alert("Informação de PONTO é obrigatória!");
                    return false;
                }

                mostrarLoading();

                $.ajax({
                    type: "POST",
                    url: "<?= url("/usuarios/salvar") ?>",
                    data: {
                        id_usuario: usuarioModal.value.id_usuario,
                        nome: usuarioModal.value.nome,
                        ponto: usuarioModal.value.ponto,
                        senha: usuarioModal.value.senha
                    },
                    dataType: "json",
                    success: function(response) {
                        ocultarLoading();
                        alert(response.message);

                        if (response.code === 200) {
                            fecharModal('modalUsuario');
                            getUsuarios();
                        }
                    },
                    error: function() {
                        ocultarLoading();
                        alert("Erro ao salvar usuário!");
                    }
                });
            }

            function excluirUsuario(usuario) {
                if (!confirm(`Deseja realmente excluir o usuário "${usuario.nome}"?`)) return false;

                mostrarLoading();

                $.ajax({
                    type: "POST",
                    url: "<?= url("/usuarios/excluir") ?>",
                    data: {
                        id_usuario: usuario.id_usuario
                    },
                    dataType: "json",
                    success: function(response) {
                        ocultarLoading();
                        alert(response.message);

                        if (response.code === 200) {
                            getUsuarios();
                        }
                    },
                    error: function() {
                        ocultarLoading();
                        alert("Erro ao excluir usuário!");
                    }
                });
            }

            onMounted(() => {
                getUsuarios();
            });

            return {
                usuarios,
                search,
                lines,
                usuarioModal,
                getUsuarios,
                buscar,
                abrirModalNovo,
                editarUsuario,
                salvarUsuario,
                excluirUsuario,
                fecharModal
            };
        },
    }).mount("#app");
</script>
<?php $this->end("js"); ?>
