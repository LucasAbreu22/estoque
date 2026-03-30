-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para almox_beta
CREATE DATABASE IF NOT EXISTS `almox_beta` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `almox_beta`;

-- Copiando estrutura para tabela almox_beta.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `visibilidade` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox_beta.categorias: ~3 rows (aproximadamente)
INSERT INTO `categorias` (`id_categoria`, `nome`, `visibilidade`) VALUES
	(1, 'Papel', 1),
	(2, 'Insumos', 1),
	(3, 'Acabamento', 1);

-- Copiando estrutura para tabela almox_beta.logs_sistema
CREATE TABLE IF NOT EXISTS `logs_sistema` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `tabela_afetada` varchar(100) NOT NULL,
  `id_registro` int(11) NOT NULL,
  `evento` enum('INSERT','UPDATE','DELETE','ENTRADA','SAIDA') NOT NULL,
  `valor_antigo` text DEFAULT NULL,
  `valor_novo` text DEFAULT NULL,
  `data_evento` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `fk_log_usuario` (`id_usuario`),
  CONSTRAINT `fk_log_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox_beta.logs_sistema: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela almox_beta.lotes
CREATE TABLE IF NOT EXISTS `lotes` (
  `id_lote` int(11) NOT NULL AUTO_INCREMENT,
  `id_material` int(11) NOT NULL,
  `lote` int(11) NOT NULL,
  `vencimento` date DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  PRIMARY KEY (`id_lote`),
  UNIQUE KEY `lote` (`lote`),
  KEY `FK__materiais` (`id_material`),
  CONSTRAINT `FK__materiais` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id_material`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox_beta.lotes: ~11 rows (aproximadamente)
INSERT INTO `lotes` (`id_lote`, `id_material`, `lote`, `vencimento`, `quantidade`) VALUES
	(2, 100, 546865, '2026-02-25', 250),
	(3, 20, 46546, '2026-02-25', 0),
	(5, 1, 21321312, '2026-03-11', 3),
	(16, 1, 321312312, '2026-03-12', 1),
	(17, 100, 323, '2026-03-05', 0),
	(18, 20, 3213123, '2026-03-13', 20),
	(20, 101, 231312, '2026-03-24', 1),
	(21, 25, 32141241, '2026-03-24', 5),
	(22, 104, 78989, '2026-04-25', 489),
	(23, 100, 421412, '2026-03-26', 1000),
	(24, 105, 89116546, '2026-03-27', 50000),
	(25, 22, 7899, '2026-03-30', 50000);

-- Copiando estrutura para tabela almox_beta.materiais
CREATE TABLE IF NOT EXISTS `materiais` (
  `id_material` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL,
  `unidade_base` enum('Centímetro','Chapa','Folha','Grama','Litro','Metro','Mililitro','Unidade') NOT NULL,
  `unidade_compra` enum('Bisnaga','Bobina','Bombona','Fardo','Frasco','Galão','Caixa','Lata','Metro','Pacote','Quilo','Resma','Rolo','Saco') NOT NULL,
  `fator_conversao` int(11) NOT NULL DEFAULT 0,
  `quantidade_minima` int(11) DEFAULT NULL,
  `custo_unitario` decimal(10,2) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `data_edicao` datetime DEFAULT NULL,
  `visibilidade` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_material`),
  KEY `fk_material_categoria` (`id_categoria`),
  CONSTRAINT `fk_material_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox_beta.materiais: ~94 rows (aproximadamente)
INSERT INTO `materiais` (`id_material`, `descricao`, `unidade_base`, `unidade_compra`, `fator_conversao`, `quantidade_minima`, `custo_unitario`, `localizacao`, `id_categoria`, `data_criacao`, `data_edicao`, `visibilidade`) VALUES
	(1, 'Papel Cartão', 'Folha', 'Resma', 500, 200, 0.00, 'A2', 1, '2026-01-22 17:35:08', NULL, 1),
	(2, 'Papel Couchê 150g', 'Folha', 'Resma', 500, 200, 0.12, 'A1', 1, '2026-01-27 15:39:10', NULL, 1),
	(3, 'Papel Offset 75g', 'Folha', 'Resma', 500, 500, 0.08, 'A3', 1, '2026-01-27 15:39:10', NULL, 1),
	(4, 'Papel Offset 90g', 'Folha', 'Resma', 500, 400, 0.10, 'A3', 1, '2026-01-27 15:39:10', NULL, 1),
	(5, 'Papel Couchê 300g', 'Folha', 'Resma', 500, 150, 0.30, 'A4', 1, '2026-01-27 15:39:10', NULL, 1),
	(6, 'Papel Sulfite A4', 'Folha', 'Resma', 500, 800, 0.05, 'A5', 1, '2026-01-27 15:39:10', NULL, 1),
	(7, 'Papel Sulfite A3', 'Folha', 'Resma', 500, 300, 0.09, 'A5', 1, '2026-01-27 15:39:10', NULL, 1),
	(8, 'Papel Reciclado', 'Folha', 'Resma', 500, 200, 0.11, 'A6', 1, '2026-01-27 15:39:10', NULL, 1),
	(9, 'Papel Fotográfico', 'Folha', 'Resma', 100, 50, 0.40, 'A6', 1, '2026-01-27 15:39:10', NULL, 1),
	(10, 'Papel Kraft', 'Folha', 'Resma', 500, 400, 0.07, 'A7', 1, '2026-01-27 15:39:10', NULL, 1),
	(11, 'Tinta Preta', 'Unidade', 'Caixa', 12, 5, 120.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(12, 'Tinta Ciano', 'Unidade', 'Caixa', 12, 5, 130.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(13, 'Tinta Magenta', 'Unidade', 'Caixa', 12, 5, 130.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(14, 'Tinta Amarela', 'Unidade', 'Caixa', 12, 5, 130.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(15, 'Verniz UV', 'Unidade', 'Caixa', 6, 2, 200.00, 'B2', 2, '2026-01-27 15:39:10', NULL, 1),
	(16, 'Cola Branca', 'Unidade', 'Caixa', 24, 6, 8.50, 'B2', 2, '2026-01-27 15:39:10', NULL, 1),
	(17, 'Cola Spray', 'Unidade', 'Caixa', 12, 4, 35.00, 'B3', 2, '2026-01-27 15:39:10', '2026-02-02 15:24:49', 1),
	(18, 'Toner Preto', 'Unidade', 'Caixa', 1, 2, 450.00, 'B3', 2, '2026-01-27 15:39:10', NULL, 1),
	(19, 'Toner Colorido', 'Unidade', 'Caixa', 1, 2, 480.00, 'B3', 2, '2026-01-27 15:39:10', NULL, 1),
	(20, 'Álcool Isopropílicoo', 'Unidade', 'Caixa', 12, 3, NULL, 'B4', 2, '2026-01-27 15:39:10', '2026-02-06 09:21:42', 1),
	(21, 'Espiral Plástico', 'Unidade', 'Caixa', 100, 200, 0.25, 'C1', 3, '2026-01-27 15:39:10', '2026-02-02 15:25:33', 1),
	(22, 'Espiral Metálico', 'Unidade', 'Caixa', 100, 200, 0.40, 'C1', 3, '2026-01-27 15:39:10', NULL, 1),
	(23, 'Wire-O Preto', 'Unidade', 'Caixa', 100, 100, 0.55, 'C2', 3, '2026-01-27 15:39:10', NULL, 1),
	(24, 'Capa Transparente A4', 'Unidade', 'Caixa', 100, 100, 0.60, 'C2', 3, '2026-01-27 15:39:10', '2026-02-02 15:19:54', 1),
	(25, 'Capa Colorida A4', 'Unidade', 'Caixa', 100, 100, NULL, 'C2', 3, '2026-01-27 15:39:10', '2026-02-09 11:17:25', 1),
	(26, 'Laminação Fosca', 'Metro', 'Rolo', 50, 10, 4.50, 'C3', 3, '2026-01-27 15:39:10', NULL, 1),
	(27, 'Laminação Brilho', 'Metro', 'Rolo', 50, 10, 4.20, 'C3', 3, '2026-01-27 15:39:10', NULL, 1),
	(28, 'Hot Stamping Dourado', 'Metro', 'Rolo', 30, 5, 12.00, 'C4', 3, '2026-01-27 15:39:10', NULL, 1),
	(29, 'Hot Stamping Prata', 'Metro', 'Rolo', 30, 5, 12.00, 'C4', 3, '2026-01-27 15:39:10', NULL, 1),
	(30, 'Ilhós Metálico', 'Unidade', 'Caixa', 500, 300, 0.10, 'C5', 3, '2026-01-27 15:39:10', NULL, 1),
	(31, 'Papel Couchê 90g', 'Folha', 'Resma', 500, 300, 0.09, 'A8', 1, '2026-01-27 15:39:10', NULL, 1),
	(32, 'Papel Couchê 115g', 'Folha', 'Resma', 500, 300, 0.11, 'A8', 1, '2026-01-27 15:39:10', NULL, 1),
	(33, 'Papel Couchê 170g', 'Folha', 'Resma', 500, 200, 0.14, 'A9', 1, '2026-01-27 15:39:10', NULL, 1),
	(34, 'Papel Offset 120g', 'Folha', 'Resma', 500, 300, 0.13, 'A9', 1, '2026-01-27 15:39:10', NULL, 1),
	(35, 'Papel Colorido', 'Folha', 'Resma', 500, 500, 0.10, 'A10', 1, '2026-01-27 15:39:10', NULL, 1),
	(36, 'Óleo Lubrificante', 'Unidade', 'Caixa', 6, 2, 45.00, 'B5', 2, '2026-01-27 15:39:10', NULL, 1),
	(37, 'Pano de Limpeza', 'Unidade', 'Caixa', 100, 20, 2.00, 'B5', 2, '2026-01-27 15:39:10', NULL, 1),
	(38, 'Luvas Descartáveis', 'Unidade', 'Caixa', 100, 30, 0.80, 'B6', 2, '2026-01-27 15:39:10', NULL, 1),
	(39, 'Máscara Descartável', 'Unidade', 'Caixa', 100, 50, 0.50, 'B6', 2, '2026-01-27 15:39:10', NULL, 1),
	(40, 'Estopa Industrial', 'Unidade', 'Caixa', 10, 3, 15.00, 'B7', 2, '2026-01-27 15:39:10', NULL, 1),
	(41, 'Grampo Industrial', 'Unidade', 'Caixa', 1000, 500, 0.02, 'C6', 3, '2026-01-27 15:39:10', NULL, 1),
	(42, 'Cantoneira Plástica', 'Unidade', 'Caixa', 100, 50, 0.30, 'C6', 3, '2026-01-27 15:39:10', '2026-02-06 14:32:25', 0),
	(43, 'Etiqueta Adesiva', 'Unidade', 'Caixa', 1000, 1000, 0.01, 'C7', 3, '2026-01-27 15:39:10', NULL, 1),
	(44, 'Fita Dupla Face', 'Metro', 'Rolo', 50, 10, 3.50, 'C7', 3, '2026-01-27 15:39:10', NULL, 1),
	(45, 'Fita Crepe', 'Metro', 'Rolo', 50, 10, 2.80, 'C7', 3, '2026-01-27 15:39:10', '2026-02-04 11:22:52', 0),
	(46, 'Papel Couchê 90g', 'Folha', 'Resma', 500, 200, 0.18, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(47, 'Papel Couchê 115g', 'Folha', 'Resma', 500, 200, 0.22, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(48, 'Papel Couchê 150g', 'Folha', 'Resma', 500, 150, 0.28, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(49, 'Papel Couchê 170g', 'Folha', 'Resma', 500, 150, 0.32, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(50, 'Papel Couchê 250g', 'Folha', 'Resma', 250, 100, 0.55, 'C4', 1, '2026-01-27 15:40:39', NULL, 1),
	(51, 'Papel Offset 63g', 'Folha', 'Resma', 500, 300, 0.12, 'B1', 1, '2026-01-27 15:40:39', NULL, 1),
	(52, 'Papel Offset 75g', 'Folha', 'Resma', 500, 300, 0.15, 'B1', 1, '2026-01-27 15:40:39', NULL, 1),
	(53, 'Papel Offset 90g', 'Folha', 'Resma', 500, 250, 0.19, 'B1', 1, '2026-01-27 15:40:39', NULL, 1),
	(54, 'Papel Offset 120g', 'Folha', 'Resma', 500, 200, 0.26, 'B2', 1, '2026-01-27 15:40:39', NULL, 1),
	(55, 'Papel Reciclado A4', 'Folha', 'Caixa', 500, 200, 0.21, 'D1', 1, '2026-01-27 15:40:39', NULL, 1),
	(56, 'Papel Reciclado A3', 'Folha', 'Caixa', 250, 100, 0.39, 'D1', 1, '2026-01-27 15:40:39', NULL, 1),
	(57, 'Papel Fotográfico Brilho', 'Folha', 'Caixa', 100, 50, 1.20, 'E2', 1, '2026-01-27 15:40:39', NULL, 1),
	(58, 'Papel Fotográfico Fosco', 'Folha', 'Caixa', 100, 50, 1.18, 'E2', 1, '2026-01-27 15:40:39', NULL, 1),
	(59, 'Papel Kraft Natural', 'Folha', 'Resma', 500, 300, 0.16, 'A4', 1, '2026-01-27 15:40:39', NULL, 1),
	(60, 'Papel Kraft Branco', 'Folha', 'Resma', 500, 200, 0.18, 'A4', 1, '2026-01-27 15:40:39', NULL, 1),
	(61, 'Papel Vegetal A4', 'Folha', 'Caixa', 250, 100, 0.45, 'E1', 1, '2026-01-27 15:40:39', NULL, 1),
	(62, 'Papel Vegetal A3', 'Folha', 'Caixa', 250, 80, 0.78, 'E1', 1, '2026-01-27 15:40:39', NULL, 1),
	(63, 'Papel Sulfite Carta', 'Folha', 'Caixa', 500, 300, 0.14, 'B3', 1, '2026-01-27 15:40:39', NULL, 1),
	(64, 'Papel Sulfite A3', 'Folha', 'Caixa', 500, 200, 0.22, 'B3', 1, '2026-01-27 15:40:39', NULL, 1),
	(65, 'Papel Sulfite A5', 'Folha', 'Caixa', 500, 400, 0.09, 'B3', 1, '2026-01-27 15:40:39', NULL, 1),
	(66, 'Papel Couchê Adesivo', 'Folha', 'Caixa', 100, 60, 0.95, 'F1', 1, '2026-01-27 15:40:39', NULL, 1),
	(67, 'Papel Offset Adesivo', 'Folha', 'Caixa', 100, 60, 0.75, 'F1', 1, '2026-01-27 15:40:39', NULL, 1),
	(68, 'Papel Transfer Laser', 'Folha', 'Caixa', 50, 40, 1.90, 'F2', 1, '2026-01-27 15:40:39', NULL, 1),
	(69, 'Papel Transfer Inkjet', 'Folha', 'Caixa', 50, 40, 2.10, 'F2', 1, '2026-01-27 15:40:39', NULL, 1),
	(70, 'Papel Color Plus Azul', 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(71, 'Papel Color Plus Vermelho', 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(72, 'Papel Color Plus Verde', 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(73, 'Papel Color Plus Amarelo', 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(74, 'Papel Couchê Metalizado', 'Folha', 'Caixa', 100, 50, 1.45, 'C5', 1, '2026-01-27 15:40:39', NULL, 1),
	(75, 'Papel Couchê Perolado', 'Folha', 'Caixa', 100, 50, 1.35, 'C5', 1, '2026-01-27 15:40:39', NULL, 1),
	(76, 'Papel Duplex 250g', 'Folha', 'Resma', 250, 150, 0.42, 'C6', 1, '2026-01-27 15:40:39', NULL, 1),
	(77, 'Papel Triplex 300g', 'Folha', 'Resma', 250, 120, 0.58, 'C6', 1, '2026-01-27 15:40:39', NULL, 1),
	(78, 'Papel Cartolina Branca', 'Folha', 'Resma', 500, 300, 0.20, 'A1', 1, '2026-01-27 15:40:39', NULL, 1),
	(79, 'Papel Cartolina Colorida', 'Folha', 'Resma', 500, 300, 0.24, 'A1', 1, '2026-01-27 15:40:39', NULL, 1),
	(80, 'Papel Jornal', 'Folha', 'Resma', 1000, 500, 0.06, 'D2', 1, '2026-01-27 15:40:39', NULL, 1),
	(81, 'Papel Bíblia', 'Folha', 'Caixa', 100, 60, 0.85, 'E3', 1, '2026-01-27 15:40:39', NULL, 1),
	(82, 'Papel Autocopiativo', 'Folha', 'Caixa', 250, 150, 0.65, 'F3', 1, '2026-01-27 15:40:39', NULL, 1),
	(83, 'Papel Segurança', 'Folha', 'Caixa', 50, 30, 2.50, 'F4', 1, '2026-01-27 15:40:39', NULL, 1),
	(84, 'Papel Couchê Texturizado', 'Folha', 'Caixa', 100, 50, 1.10, 'C7', 1, '2026-01-27 15:40:39', NULL, 1),
	(85, 'Papel Offset Texturizado', 'Folha', 'Caixa', 100, 60, 0.88, 'C7', 1, '2026-01-27 15:40:39', NULL, 1),
	(86, 'Teste', 'Unidade', 'Resma', 1, 1, NULL, '1', 3, '2026-01-29 17:41:14', NULL, 1),
	(97, 'Produto teste', 'Unidade', 'Caixa', 1, 15, NULL, 'A1', 2, '2026-02-04 11:24:04', '2026-02-04 11:25:29', 1),
	(98, 'Álcool Isopropílico ', 'Unidade', 'Resma', 1, 1, NULL, 'B4', 3, '2026-02-04 13:22:12', '2026-02-11 13:50:05', 0),
	(99, 'Teste NEW1 ', 'Unidade', 'Resma', 1, 1, NULL, '1', 3, '2026-02-04 13:34:47', '2026-02-04 13:38:18', 1),
	(100, 'Ácido Fosfórico', 'Folha', 'Resma', 500, 500, NULL, 'A7', 2, '2026-02-06 14:24:03', '2026-03-25 16:23:09', 1),
	(101, 'Almofada Carimbo P', 'Unidade', 'Resma', 1, 2, NULL, 'Armário 1', 2, '2026-02-06 14:32:00', NULL, 1),
	(102, 'Chapa KOMORI (CAIXA 50)', 'Unidade', 'Caixa', 50, 20, NULL, 'A5', 3, '2026-02-19 17:18:25', NULL, 1),
	(103, 'Chapa KOMORI (c/ 50) venc. 19/02/2027', 'Unidade', 'Caixa', 50, 250, NULL, 'B5', 2, '2026-02-19 17:21:43', NULL, 1),
	(104, 'CHAPA SM74 (c/ 50)', 'Folha', 'Caixa', 50, 1000, NULL, 'A8', 2, '2026-03-24 16:14:24', NULL, 1),
	(105, 'Material Teste', 'Chapa', 'Caixa', 500, 500, NULL, 'B4', 2, '2026-03-27 15:35:49', NULL, 1),
	(106, 'Álcool Isopropílico TESTE', 'Centímetro', 'Bisnaga', 500, 100, NULL, '213sad', 3, '2026-03-30 10:58:39', NULL, 1);

-- Copiando estrutura para tabela almox_beta.materiais_movimentacao
CREATE TABLE IF NOT EXISTS `materiais_movimentacao` (
  `id_movimentacao` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  KEY `FK_materiais_movimentacao_materiais` (`id_material`),
  KEY `FK_materiais_movimentacao_movimentacoes_estoque` (`id_movimentacao`),
  KEY `FK_materiais_movimentacao_lotes` (`id_lote`),
  CONSTRAINT `FK_materiais_movimentacao_lotes` FOREIGN KEY (`id_lote`) REFERENCES `lotes` (`id_lote`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_materiais_movimentacao_materiais` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id_material`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_materiais_movimentacao_movimentacoes_estoque` FOREIGN KEY (`id_movimentacao`) REFERENCES `movimentacoes_estoque` (`id_movimentacao`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox_beta.materiais_movimentacao: ~18 rows (aproximadamente)
INSERT INTO `materiais_movimentacao` (`id_movimentacao`, `id_material`, `id_lote`, `quantidade`) VALUES
	(87, 97, 2, 5),
	(87, 103, 3, 7),
	(88, 22, 5, 70),
	(95, 11, 18, 10),
	(67, 25, 16, 8),
	(100, 100, 2, 3),
	(100, 101, 17, 4),
	(102, 97, 5, 10),
	(112, 20, 3, 5),
	(114, 20, 18, 2),
	(116, 101, 20, 2),
	(117, 25, 21, 5),
	(118, 100, 17, 1),
	(118, 20, 18, 2),
	(118, 101, 20, 1),
	(119, 104, 22, 500),
	(120, 104, 22, 15),
	(121, 100, 23, 2),
	(122, 100, 2, 500),
	(123, 100, 2, 250),
	(124, 105, 24, 100),
	(125, 22, 25, 500);

-- Copiando estrutura para tabela almox_beta.movimentacoes_estoque
CREATE TABLE IF NOT EXISTS `movimentacoes_estoque` (
  `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `codigo_sigma` int(11) DEFAULT NULL,
  `tipo` enum('ENTRADA','SAIDA') NOT NULL,
  `unidade_utilizada` enum('BASE','COMPRA') NOT NULL,
  `fator_conversao_aplicado` int(11) DEFAULT NULL,
  `quantidade_convertida` int(11) NOT NULL,
  `ponto_solicitante` varchar(10) DEFAULT NULL,
  `nome_solicitante` varchar(164) DEFAULT NULL,
  `data_movimentacao` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_movimentacao`),
  KEY `fk_mov_usuario` (`id_usuario`),
  CONSTRAINT `fk_mov_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox_beta.movimentacoes_estoque: ~19 rows (aproximadamente)
INSERT INTO `movimentacoes_estoque` (`id_movimentacao`, `id_usuario`, `codigo_sigma`, `tipo`, `unidade_utilizada`, `fator_conversao_aplicado`, `quantidade_convertida`, `ponto_solicitante`, `nome_solicitante`, `data_movimentacao`) VALUES
	(67, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '456', 'Teste', '2026-03-10 17:47:20'),
	(87, 1, 2147483647, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-11 14:51:08'),
	(88, 1, 321312313, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-11 15:18:14'),
	(95, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '231', 'sdadasd', '2026-03-12 16:41:05'),
	(100, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '548564', 'Teste DOC', '2026-03-20 16:45:15'),
	(101, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '4654', 'Teste 2 DOC', '2026-03-20 16:46:54'),
	(102, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '3123', 'Teste SOLI', '2026-03-24 14:12:03'),
	(103, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '23131', 'Paulo Felipe', '2026-03-24 14:38:18'),
	(112, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '23131', 'Paulo Felipe', '2026-03-24 15:03:19'),
	(114, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '2313213', 'Paulo Felipe', '2026-03-24 15:17:49'),
	(116, 1, 2313123, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-24 15:25:46'),
	(117, 1, 2321412, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-24 15:25:59'),
	(118, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '6220', 'Paulo Felipe', '2026-03-24 15:26:34'),
	(119, 1, 5665654, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-24 16:32:42'),
	(120, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '6220', 'Paulo Felipe', '2026-03-24 16:40:31'),
	(121, 1, 123123, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-25 16:24:16'),
	(122, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '6220', 'Paulo Felipe', '2026-03-25 16:55:02'),
	(123, 1, NULL, 'SAIDA', 'BASE', NULL, 0, '6220', 'Paulo Felipe', '2026-03-25 17:39:58'),
	(124, 1, 56456456, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-27 15:36:54'),
	(125, 1, 546456, 'ENTRADA', 'BASE', NULL, 0, '', '', '2026-03-30 10:55:04');

-- Copiando estrutura para tabela almox_beta.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `ponto` varchar(50) NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `data_edicao` datetime DEFAULT NULL,
  `data_criacao` datetime NOT NULL,
  `visibilidade` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `ponto` (`ponto`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox_beta.usuarios: ~4 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `nome`, `ponto`, `senha`, `data_edicao`, `data_criacao`, `visibilidade`) VALUES
	(1, 'Lucas Abreu', '921456', NULL, NULL, '2026-02-02 12:38:59', 1),
	(2, 'Newton Elias de Souza Junior', '6423', NULL, NULL, '2025-02-01 09:31:00', 1),
	(4, 'Jorge Luiz Gusmão da Trindade', '6231', NULL, NULL, '2026-02-12 17:39:03', 1),
	(5, 'Paulo Felipe Scherer', '6220', NULL, NULL, '2026-02-12 17:39:37', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
