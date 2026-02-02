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


-- Copiando estrutura do banco de dados para almox
CREATE DATABASE IF NOT EXISTS `almox` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `almox`;

-- Copiando estrutura para tabela almox.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `visibilidade` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox.categorias: ~3 rows (aproximadamente)
INSERT INTO `categorias` (`id_categoria`, `nome`, `visibilidade`) VALUES
	(1, 'Papel', 1),
	(2, 'Insumos', 1),
	(3, 'Acabamento', 1);

-- Copiando estrutura para tabela almox.materiais
CREATE TABLE IF NOT EXISTS `materiais` (
  `id_material` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `unidade_base` enum('Unidade','Folha','Metro') NOT NULL,
  `unidade_compra` enum('Resma','Caixa','Rolo') NOT NULL,
  `fator_conversao` int(11) NOT NULL DEFAULT 0,
  `quantidade_minima` int(11) DEFAULT NULL,
  `custo_unitario` decimal(10,2) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `data_edicao` datetime DEFAULT NULL,
  `visibilidade` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_material`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `fk_material_categoria` (`id_categoria`),
  CONSTRAINT `fk_material_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela almox.materiais: ~86 rows (aproximadamente)
INSERT INTO `materiais` (`id_material`, `codigo`, `descricao`, `quantidade`, `unidade_base`, `unidade_compra`, `fator_conversao`, `quantidade_minima`, `custo_unitario`, `localizacao`, `id_categoria`, `data_criacao`, `data_edicao`, `visibilidade`) VALUES
	(1, 'PAP-002', 'Papel Cartão', 0, 'Folha', 'Resma', 500, 200, 0.00, 'A2', 1, '2026-01-22 17:35:08', NULL, 1),
	(2, 'PAP-001', 'Papel Couchê 150g', 0, 'Folha', 'Resma', 500, 200, 0.12, 'A1', 1, '2026-01-27 15:39:10', NULL, 1),
	(3, 'PAP-003', 'Papel Offset 75g', 1200, 'Folha', 'Resma', 500, 500, 0.08, 'A3', 1, '2026-01-27 15:39:10', NULL, 1),
	(4, 'PAP-004', 'Papel Offset 90g', 300, 'Folha', 'Resma', 500, 400, 0.10, 'A3', 1, '2026-01-27 15:39:10', NULL, 1),
	(5, 'PAP-005', 'Papel Couchê 300g', 0, 'Folha', 'Resma', 500, 150, 0.30, 'A4', 1, '2026-01-27 15:39:10', NULL, 1),
	(6, 'PAP-006', 'Papel Sulfite A4', 2500, 'Folha', 'Resma', 500, 800, 0.05, 'A5', 1, '2026-01-27 15:39:10', NULL, 1),
	(7, 'PAP-007', 'Papel Sulfite A3', 600, 'Folha', 'Resma', 500, 300, 0.09, 'A5', 1, '2026-01-27 15:39:10', NULL, 1),
	(8, 'PAP-008', 'Papel Reciclado', 0, 'Folha', 'Resma', 500, 200, 0.11, 'A6', 1, '2026-01-27 15:39:10', NULL, 1),
	(9, 'PAP-009', 'Papel Fotográfico', 120, 'Folha', 'Resma', 100, 50, 0.40, 'A6', 1, '2026-01-27 15:39:10', NULL, 1),
	(10, 'PAP-010', 'Papel Kraft', 900, 'Folha', 'Resma', 500, 400, 0.07, 'A7', 1, '2026-01-27 15:39:10', NULL, 1),
	(11, 'INS-001', 'Tinta Preta', 2, 'Unidade', 'Caixa', 12, 5, 120.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(12, 'INS-002', 'Tinta Ciano', 0, 'Unidade', 'Caixa', 12, 5, 130.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(13, 'INS-003', 'Tinta Magenta', 1, 'Unidade', 'Caixa', 12, 5, 130.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(14, 'INS-004', 'Tinta Amarela', 3, 'Unidade', 'Caixa', 12, 5, 130.00, 'B1', 2, '2026-01-27 15:39:10', NULL, 1),
	(15, 'INS-005', 'Verniz UV', 0, 'Unidade', 'Caixa', 6, 2, 200.00, 'B2', 2, '2026-01-27 15:39:10', NULL, 1),
	(16, 'INS-006', 'Cola Branca', 10, 'Unidade', 'Caixa', 24, 6, 8.50, 'B2', 2, '2026-01-27 15:39:10', NULL, 1),
	(17, 'INS-007', 'Cola Spray', 1, 'Unidade', 'Caixa', 12, 4, 35.00, 'B3', 2, '2026-01-27 15:39:10', NULL, 1),
	(18, 'INS-008', 'Toner Preto', 0, 'Unidade', 'Caixa', 1, 2, 450.00, 'B3', 2, '2026-01-27 15:39:10', NULL, 1),
	(19, 'INS-009', 'Toner Colorido', 1, 'Unidade', 'Caixa', 1, 2, 480.00, 'B3', 2, '2026-01-27 15:39:10', NULL, 1),
	(20, 'INS-010', 'Álcool Isopropílico', 0, 'Unidade', 'Caixa', 12, 3, NULL, 'B4', 2, '2026-01-27 15:39:10', '2026-01-30 17:19:29', 1),
	(21, 'ACB-001', 'Espiral Plástico', 300, 'Unidade', 'Caixa', 100, 200, 0.25, 'C1', 3, '2026-01-27 15:39:10', NULL, 1),
	(22, 'ACB-002', 'Espiral Metálico', 0, 'Unidade', 'Caixa', 100, 200, 0.40, 'C1', 3, '2026-01-27 15:39:10', NULL, 1),
	(23, 'ACB-003', 'Wire-O Preto', 120, 'Unidade', 'Caixa', 100, 100, 0.55, 'C2', 3, '2026-01-27 15:39:10', NULL, 1),
	(24, 'ACB-004', 'Capa Transparente A4', 80, 'Unidade', 'Caixa', 100, 100, 0.60, 'C2', 3, '2026-01-27 15:39:10', NULL, 1),
	(25, 'ACB-005', 'Capa Preta A4', 40, 'Unidade', 'Caixa', 100, 100, 0.70, 'C2', 3, '2026-01-27 15:39:10', NULL, 1),
	(26, 'ACB-006', 'Laminação Fosca', 0, 'Metro', 'Rolo', 50, 10, 4.50, 'C3', 3, '2026-01-27 15:39:10', NULL, 1),
	(27, 'ACB-007', 'Laminação Brilho', 15, 'Metro', 'Rolo', 50, 10, 4.20, 'C3', 3, '2026-01-27 15:39:10', NULL, 1),
	(28, 'ACB-008', 'Hot Stamping Dourado', 5, 'Metro', 'Rolo', 30, 5, 12.00, 'C4', 3, '2026-01-27 15:39:10', NULL, 1),
	(29, 'ACB-009', 'Hot Stamping Prata', 0, 'Metro', 'Rolo', 30, 5, 12.00, 'C4', 3, '2026-01-27 15:39:10', NULL, 1),
	(30, 'ACB-010', 'Ilhós Metálico', 600, 'Unidade', 'Caixa', 500, 300, 0.10, 'C5', 3, '2026-01-27 15:39:10', NULL, 1),
	(31, 'PAP-011', 'Papel Couchê 90g', 0, 'Folha', 'Resma', 500, 300, 0.09, 'A8', 1, '2026-01-27 15:39:10', NULL, 1),
	(32, 'PAP-012', 'Papel Couchê 115g', 500, 'Folha', 'Resma', 500, 300, 0.11, 'A8', 1, '2026-01-27 15:39:10', NULL, 1),
	(33, 'PAP-013', 'Papel Couchê 170g', 0, 'Folha', 'Resma', 500, 200, 0.14, 'A9', 1, '2026-01-27 15:39:10', NULL, 1),
	(34, 'PAP-014', 'Papel Offset 120g', 200, 'Folha', 'Resma', 500, 300, 0.13, 'A9', 1, '2026-01-27 15:39:10', NULL, 1),
	(35, 'PAP-015', 'Papel Colorido', 1000, 'Folha', 'Resma', 500, 500, 0.10, 'A10', 1, '2026-01-27 15:39:10', NULL, 1),
	(36, 'INS-011', 'Óleo Lubrificante', 1, 'Unidade', 'Caixa', 6, 2, 45.00, 'B5', 2, '2026-01-27 15:39:10', NULL, 1),
	(37, 'INS-012', 'Pano de Limpeza', 50, 'Unidade', 'Caixa', 100, 20, 2.00, 'B5', 2, '2026-01-27 15:39:10', NULL, 1),
	(38, 'INS-013', 'Luvas Descartáveis', 0, 'Unidade', 'Caixa', 100, 30, 0.80, 'B6', 2, '2026-01-27 15:39:10', NULL, 1),
	(39, 'INS-014', 'Máscara Descartável', 200, 'Unidade', 'Caixa', 100, 50, 0.50, 'B6', 2, '2026-01-27 15:39:10', NULL, 1),
	(40, 'INS-015', 'Estopa Industrial', 5, 'Unidade', 'Caixa', 10, 3, 15.00, 'B7', 2, '2026-01-27 15:39:10', NULL, 1),
	(41, 'ACB-011', 'Grampo Industrial', 1000, 'Unidade', 'Caixa', 1000, 500, 0.02, 'C6', 3, '2026-01-27 15:39:10', NULL, 1),
	(42, 'ACB-012', 'Cantoneira Plástica', 0, 'Unidade', 'Caixa', 100, 50, 0.30, 'C6', 3, '2026-01-27 15:39:10', NULL, 1),
	(43, 'ACB-013', 'Etiqueta Adesiva', 2000, 'Unidade', 'Caixa', 1000, 1000, 0.01, 'C7', 3, '2026-01-27 15:39:10', NULL, 1),
	(44, 'ACB-014', 'Fita Dupla Face', 10, 'Metro', 'Rolo', 50, 10, 3.50, 'C7', 3, '2026-01-27 15:39:10', NULL, 1),
	(45, 'ACB-015', 'Fita Crepe', 8, 'Metro', 'Rolo', 50, 10, 2.80, 'C7', 3, '2026-01-27 15:39:10', NULL, 1),
	(46, 'PAP-083', 'Papel Couchê 90g', 120, 'Folha', 'Resma', 500, 200, 0.18, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(47, 'PAP-084', 'Papel Couchê 115g', 300, 'Folha', 'Resma', 500, 200, 0.22, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(48, 'PAP-085', 'Papel Couchê 150g', 80, 'Folha', 'Resma', 500, 150, 0.28, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(49, 'PAP-086', 'Papel Couchê 170g', 0, 'Folha', 'Resma', 500, 150, 0.32, 'C3', 1, '2026-01-27 15:40:39', NULL, 1),
	(50, 'PAP-087', 'Papel Couchê 250g', 40, 'Folha', 'Resma', 250, 100, 0.55, 'C4', 1, '2026-01-27 15:40:39', NULL, 1),
	(51, 'PAP-088', 'Papel Offset 63g', 600, 'Folha', 'Resma', 500, 300, 0.12, 'B1', 1, '2026-01-27 15:40:39', NULL, 1),
	(52, 'PAP-089', 'Papel Offset 75g', 450, 'Folha', 'Resma', 500, 300, 0.15, 'B1', 1, '2026-01-27 15:40:39', NULL, 1),
	(53, 'PAP-090', 'Papel Offset 90g', 200, 'Folha', 'Resma', 500, 250, 0.19, 'B1', 1, '2026-01-27 15:40:39', NULL, 1),
	(54, 'PAP-091', 'Papel Offset 120g', 100, 'Folha', 'Resma', 500, 200, 0.26, 'B2', 1, '2026-01-27 15:40:39', NULL, 1),
	(55, 'PAP-092', 'Papel Reciclado A4', 350, 'Folha', 'Caixa', 500, 200, 0.21, 'D1', 1, '2026-01-27 15:40:39', NULL, 1),
	(56, 'PAP-093', 'Papel Reciclado A3', 90, 'Folha', 'Caixa', 250, 100, 0.39, 'D1', 1, '2026-01-27 15:40:39', NULL, 1),
	(57, 'PAP-094', 'Papel Fotográfico Brilho', 70, 'Folha', 'Caixa', 100, 50, 1.20, 'E2', 1, '2026-01-27 15:40:39', NULL, 1),
	(58, 'PAP-095', 'Papel Fotográfico Fosco', 60, 'Folha', 'Caixa', 100, 50, 1.18, 'E2', 1, '2026-01-27 15:40:39', NULL, 1),
	(59, 'PAP-096', 'Papel Kraft Natural', 500, 'Folha', 'Resma', 500, 300, 0.16, 'A4', 1, '2026-01-27 15:40:39', NULL, 1),
	(60, 'PAP-097', 'Papel Kraft Branco', 260, 'Folha', 'Resma', 500, 200, 0.18, 'A4', 1, '2026-01-27 15:40:39', NULL, 1),
	(61, 'PAP-098', 'Papel Vegetal A4', 140, 'Folha', 'Caixa', 250, 100, 0.45, 'E1', 1, '2026-01-27 15:40:39', NULL, 1),
	(62, 'PAP-099', 'Papel Vegetal A3', 60, 'Folha', 'Caixa', 250, 80, 0.78, 'E1', 1, '2026-01-27 15:40:39', NULL, 1),
	(63, 'PAP-100', 'Papel Sulfite Carta', 400, 'Folha', 'Caixa', 500, 300, 0.14, 'B3', 1, '2026-01-27 15:40:39', NULL, 1),
	(64, 'PAP-101', 'Papel Sulfite A3', 220, 'Folha', 'Caixa', 500, 200, 0.22, 'B3', 1, '2026-01-27 15:40:39', NULL, 1),
	(65, 'PAP-102', 'Papel Sulfite A5', 800, 'Folha', 'Caixa', 500, 400, 0.09, 'B3', 1, '2026-01-27 15:40:39', NULL, 1),
	(66, 'PAP-103', 'Papel Couchê Adesivo', 90, 'Folha', 'Caixa', 100, 60, 0.95, 'F1', 1, '2026-01-27 15:40:39', NULL, 1),
	(67, 'PAP-104', 'Papel Offset Adesivo', 110, 'Folha', 'Caixa', 100, 60, 0.75, 'F1', 1, '2026-01-27 15:40:39', NULL, 1),
	(68, 'PAP-105', 'Papel Transfer Laser', 50, 'Folha', 'Caixa', 50, 40, 1.90, 'F2', 1, '2026-01-27 15:40:39', NULL, 1),
	(69, 'PAP-106', 'Papel Transfer Inkjet', 45, 'Folha', 'Caixa', 50, 40, 2.10, 'F2', 1, '2026-01-27 15:40:39', NULL, 1),
	(70, 'PAP-107', 'Papel Color Plus Azul', 180, 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(71, 'PAP-108', 'Papel Color Plus Vermelho', 160, 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(72, 'PAP-109', 'Papel Color Plus Verde', 170, 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(73, 'PAP-110', 'Papel Color Plus Amarelo', 190, 'Folha', 'Resma', 250, 150, 0.32, 'C1', 1, '2026-01-27 15:40:39', NULL, 1),
	(74, 'PAP-111', 'Papel Couchê Metalizado', 30, 'Folha', 'Caixa', 100, 50, 1.45, 'C5', 1, '2026-01-27 15:40:39', NULL, 1),
	(75, 'PAP-112', 'Papel Couchê Perolado', 40, 'Folha', 'Caixa', 100, 50, 1.35, 'C5', 1, '2026-01-27 15:40:39', NULL, 1),
	(76, 'PAP-113', 'Papel Duplex 250g', 210, 'Folha', 'Resma', 250, 150, 0.42, 'C6', 1, '2026-01-27 15:40:39', NULL, 1),
	(77, 'PAP-114', 'Papel Triplex 300g', 140, 'Folha', 'Resma', 250, 120, 0.58, 'C6', 1, '2026-01-27 15:40:39', NULL, 1),
	(78, 'PAP-115', 'Papel Cartolina Branca', 500, 'Folha', 'Resma', 500, 300, 0.20, 'A1', 1, '2026-01-27 15:40:39', NULL, 1),
	(79, 'PAP-116', 'Papel Cartolina Colorida', 420, 'Folha', 'Resma', 500, 300, 0.24, 'A1', 1, '2026-01-27 15:40:39', NULL, 1),
	(80, 'PAP-117', 'Papel Jornal', 1000, 'Folha', 'Resma', 1000, 500, 0.06, 'D2', 1, '2026-01-27 15:40:39', NULL, 1),
	(81, 'PAP-118', 'Papel Bíblia', 80, 'Folha', 'Caixa', 100, 60, 0.85, 'E3', 1, '2026-01-27 15:40:39', NULL, 1),
	(82, 'PAP-119', 'Papel Autocopiativo', 120, 'Folha', 'Caixa', 250, 150, 0.65, 'F3', 1, '2026-01-27 15:40:39', NULL, 1),
	(83, 'PAP-120', 'Papel Segurança', 40, 'Folha', 'Caixa', 50, 30, 2.50, 'F4', 1, '2026-01-27 15:40:39', NULL, 1),
	(84, 'PAP-121', 'Papel Couchê Texturizado', 70, 'Folha', 'Caixa', 100, 50, 1.10, 'C7', 1, '2026-01-27 15:40:39', NULL, 1),
	(85, 'PAP-122', 'Papel Offset Texturizado', 95, 'Folha', 'Caixa', 100, 60, 0.88, 'C7', 1, '2026-01-27 15:40:39', NULL, 1),
	(86, 'test', 'Teste', 0, 'Unidade', 'Resma', 1, 1, NULL, '1', 3, '2026-01-29 17:41:14', NULL, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
