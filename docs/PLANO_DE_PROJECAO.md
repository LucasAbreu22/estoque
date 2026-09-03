# Plano de Projeção — Sistema de Estoque (Almox / CGraf)

Documento com sugestões de melhorias e novas funcionalidades que agregam valor
ao sistema. As sugestões estão priorizadas e organizadas por tema, considerando
o estado atual do código (PHP + PDO, camada DAO/Model, front-end em Vue 3 + jQuery,
banco `almox`).

> Observação: este documento é consultivo. Nenhuma das sugestões abaixo foi
> implementada além do escopo já solicitado (CRUD de usuários e LOGs).

---

## 1. Segurança e Autenticação

Hoje a identificação do responsável é feita por um **cookie com o número do ponto**
(`usuario`), sem senha. Isso é simples, porém frágil.

- **Autenticação real opcional**: a coluna `usuarios.senha` já existe. Seria
  possível oferecer login com senha (usando `password_hash`/`password_verify`)
  mantendo o fluxo por ponto como fallback. *(Só implementar se houver requisito.)*
- **Sessões no servidor**: migrar do cookie de ponto para `$_SESSION`, evitando
  que o usuário troque o ponto manualmente no navegador.
- **Perfis de acesso (RBAC)**: adicionar coluna `perfil` (ex.: `ADMIN`, `OPERADOR`)
  para restringir quem gerencia usuários, exclui materiais, etc.
- **Proteção CSRF**: incluir token nos formulários/POSTs (todas as ações hoje são
  POST sem token).
- **Escapar saída (XSS)**: garantir `htmlspecialchars` em dados renderizados fora
  do binding do Vue (relatórios/PDF, por exemplo).

## 2. Auditoria e LOGs (evolução do que foi implementado)

O registro de LOGs (`logs_sistema`) passou a funcionar para criação, edição e
exclusão de materiais e usuários, além de ENTRADA/SAIDA de movimentações.

- **Tela de auditoria**: página para consultar `logs_sistema` com filtros por
  usuário, tabela, evento e período (o `LogDAO::getLogs` já foi preparado para isso).
- **Diff visual**: exibir lado a lado `valor_antigo` × `valor_novo` (já são JSON).
- **Retenção/arquivamento**: rotina para arquivar logs antigos e manter a tabela
  performática.

## 3. Integridade e Modelagem de Dados

- **`lotes.lote` UNIQUE global**: hoje o número de lote é único em toda a tabela.
  O mais correto costuma ser `UNIQUE (id_material, lote)`, permitindo o mesmo
  número de lote para materiais diferentes.
- **Padronizar `data_edicao`**: aplicar `DEFAULT NULL` e atualização consistente
  em todas as tabelas (materiais já faz; validar usuários).
- **Índices**: adicionar índices em colunas muito filtradas (`materiais.descricao`,
  `movimentacoes_estoque.data_movimentacao`).
- **Tipo do campo `ponto`**: hoje é `varchar(50)` no banco, mas tratado como `INT`
  no model `Usuario`. Padronizar o tipo evita conversões implícitas.

## 4. Experiência do Usuário (Front-end)

- **Substituir `alert/confirm`** por toasts/modais de confirmação padronizados.
- **Feedback de validação inline** nos formulários (campos obrigatórios em vermelho).
- **Componentização**: os arquivos de tema misturam HTML, PHP e um app Vue grande.
  Extrair componentes e mover o JS inline para arquivos dedicados melhora a manutenção.
- **Paginação com contagem total** exibida ("Mostrando X–Y de Z").
- **Ordenação por coluna** nas tabelas de materiais/movimentações/usuários.

## 5. Funcionalidades de Estoque

- **Alertas de vencimento**: destacar lotes próximos do vencimento e um painel
  "vencendo em 30 dias".
- **Alertas de estoque mínimo**: notificação (tela/e-mail) quando `saldo < quantidade_minima`.
- **Dashboard**: indicadores de saldo, giro, entradas/saídas no período e custo total.
- **Exportações**: exportar listagens para Excel/CSV além do PDF já existente.
- **Inventário/ajuste de estoque**: fluxo específico para correções com justificativa
  (registrado em log).

## 6. Qualidade de Código e Arquitetura

- **`declare(strict_types=1)`** e padronização de type hints (hoje há mistura de
  `INT/STRING` maiúsculo com tipos nativos).
- **Camada de serviço**: centralizar regras que hoje se repetem entre controllers.
- **Tratamento de erros unificado**: retornos JSON padronizados (`code`, `message`, `data`).
- **Testes automatizados**: testes de unidade para DAOs/Models e testes de fumaça
  para as rotas principais.
- **Variáveis de ambiente**: mover credenciais de `Config.php` para `.env`
  (fora do versionamento).

## 7. Operação e Infraestrutura

- **Backups automáticos** do banco `almox` com política de retenção.
- **Logs de aplicação** (erros PHP) em arquivo/monitoramento, separados dos logs de auditoria.
- **Migrations versionadas** para evoluir o schema de forma rastreável.

---

### Priorização sugerida (curto → longo prazo)

1. Tela de auditoria de LOGs + filtros. *(baixo esforço, alto valor)*
2. Alertas de estoque mínimo e vencimento. *(alto valor operacional)*
3. Perfis de acesso + CSRF/sessão. *(segurança)*
4. Dashboard e exportações. *(gestão)*
5. Refatorações de arquitetura e testes. *(sustentabilidade)*
