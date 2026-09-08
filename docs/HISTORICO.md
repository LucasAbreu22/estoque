# Histórico do Sistema — Linha do Tempo

Registro cronológico das alterações relevantes feitas no sistema de estoque
(Almox / CGraf), para acompanhar o que já foi resolvido e a evolução do projeto.

Formato: cada entrada traz **data**, **escopo**, **o que foi feito** e os
**arquivos** principais afetados.

---

## 2026-09-03 — Ajustes de vencimento de lote

**Escopo:** tratamento de data de vencimento nula nos lotes.

- Ao registrar entrada de lote sem vencimento, o valor passa a ser gravado como
  `NULL` (antes ocorria erro/valor incorreto).
- Nas listagens, quando o vencimento é nulo, `vencimentoFormatted` retorna vazio
  (corrigido também o efeito colateral de `new DateTime(null)` retornar a data atual).
- Incluído o campo de vencimento (formatado) na sublista de materiais/lotes das
  movimentações de estoque.

**Arquivos:** `source/DAO/LoteDAO.php`, `source/Models/Lote.php`,
`source/Models/MaterialMovimentacao.php`, `theme/movimentacoes.php`.

---

## 2026-09-03 — CRUD de Usuários + Sistema de LOGs + Plano de Projeção

**Escopo:** gestão de usuários, auditoria de dados e planejamento.

- **CRUD de usuários**: nova página `/usuarios` (listar, criar, editar, excluir –
  exclusão lógica via `visibilidade`). Apenas `nome` e `ponto` são obrigatórios;
  `senha` é opcional. A forma de autenticação (por ponto, sem senha) não foi alterada.
  - Correção: `UsuarioDAO::getUsuarios` consultava a tabela `materiais` por engano.
- **Sistema de LOGs**: passou a registrar interações de dados em `logs_sistema`:
  - `LogDAO` (persistência) + `Log::registrar()` (helper best-effort que nunca
    interrompe a operação principal) + `usuarioLogadoId()` (resolve o responsável
    pelo cookie de ponto, sem mexer na autenticação).
  - Eventos cobertos: `INSERT`/`UPDATE`/`DELETE` em materiais e usuários, e
    `ENTRADA`/`SAIDA` em movimentações.
- **Plano de projeção**: criado `docs/PLANO_DE_PROJECAO.md` com melhorias priorizadas.

**Arquivos:** `source/DAO/UsuarioDAO.php`, `source/Models/Usuario.php`,
`source/App/Usuarios.php`, `source/DAO/LogDAO.php`, `source/Models/Log.php`,
`source/Config.php`, `source/App/Materiais.php`, `source/App/Web.php`,
`index.php`, `theme/_theme.php`, `theme/usuarios.php`, `docs/PLANO_DE_PROJECAO.md`.

---

## 2026-09-03 — Correção: aviso de *Deprecated* no model Log

**Escopo:** limpeza de aviso do PHP 8+.

- Ajustado o construtor de `Log` (parâmetro obrigatório `$usuario` vinha após um
  opcional). Tornado anulável/opcional, sem alterar comportamento.

**Arquivos:** `source/Models/Log.php`.

---

## 2026-09-04 — Tela de Auditoria de LOGs (Prioridade #1 do plano)

**Escopo:** primeiro item priorizado no plano de projeção.

- Nova página `/logs` para consultar `logs_sistema` com **filtros** por período,
  tabela, evento e usuário (nome/ponto), com **paginação**.
- **Diff legível**: `valor_antigo` × `valor_novo` (JSON) exibidos como pares
  "campo: valor".
- Backend: `LogDAO::getLogs` estendido com filtros + `LogDAO::contarLogs`;
  métodos correspondentes no model `Log`; novo controller `Logs`; rota `logs`;
  link de navegação no cabeçalho.
- Atualizado o `docs/PLANO_DE_PROJECAO.md` (item marcado como concluído e demais
  demandas reordenadas).

**Arquivos:** `source/DAO/LogDAO.php`, `source/Models/Log.php`,
`source/App/Logs.php`, `source/App/Web.php`, `index.php`, `theme/_theme.php`,
`theme/logs.php`, `docs/PLANO_DE_PROJECAO.md`, `docs/HISTORICO.md`.

---

## Estado atual do sistema (resumo)

- Estoque: cadastro de materiais, lotes e movimentações (entrada/saída) com
  relatórios em PDF.
- Usuários: CRUD completo (uso interno; autenticação por ponto).
- Auditoria: registro automático de interações e tela de consulta com filtros.
- Documentação: plano de projeção e este histórico versionados em `docs/`.
