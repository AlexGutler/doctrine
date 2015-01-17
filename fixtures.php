<?php
require_once __DIR__ . "/src/AG/config/connectionPDO.php";

$conn = connectionPDO();
echo "Conectado ao banco de dados.\n";

$conn->query("DROP SCHEMA IF EXISTS `silexdb`");
$conn->query("CREATE SCHEMA `silexdb`");
$conn->query("USE `silexdb`");
echo "Schema criado com sucesso.\n";

$conn->query("DROP TABLE IF EXISTS `produtos`");

$conn->query("CREATE TABLE `produtos`(
  `id` INT(11) AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` text NOT NULL,
  `valor` FLOAT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
echo "Tabela de produtos criada com sucesso.\n";

$sql = "INSERT INTO `silexdb`.`produtos` (`id`, `nome`, `descricao`, `valor`) VALUES
      (1, 'Câmera Digital Sony', 'DSC-HX300 20.4 MP, Zoom Óptico de 50x + Cartão de Memória de 8GB', 1533.44),
      (2, 'Smartphone Nokia Lumia 530', 'Desbloqueado Preto Windows Phone 8.1 3G Câmera 5MP Memória Interna 4GB + Capa Laranja', 323.19),
      (3, 'Pen Drive Sony', 'USM-M 8GB Branco', 14.16),
      (4, 'Ultrabook Asus S46CB', 'Intel Core i5 6GB (2GB Memória Dedicada) 500GB + 24GB SSD LED 14 Windows 8', 2024.10),
      (5, 'CD - Pink Floyd', 'The Endless River - Versão Deluxe (CD+DVD)', 163.90),
      (6, 'Livro - As Crônicas de Nárnia', 'Volume Único', 36.10),
      (7, 'Livro - O Mundo de Gelo e Fogo', 'Tudo o que você sempre quis saber sobre Westeros e tinha medo de perguntar a George R. R. Martin!', 89.90),
      (8, 'Game Assassins Creed IV', 'Black Flag Signature Edition + DLC Black Island (Versão Em Português) - Xbox 360', 59.90),
      (9, 'Game - Titanfall', 'XBOX ONE', 129.90),
      (10, 'DVD - Coldplay ', 'Ghost Stories Live 2014 [CD+DVD]', 49.90),
      (11, 'Smartphone Dual Chip Samsung Galaxy S4 Mini Duos ', 'Desbloqueado Branco Android 4.2 3G/Wi-Fi Câmera 8MP 8GB', 679.00),
      (12, 'Ar Condicionado Split Electrolux', '7.000 Btus Frio - 220V', 851.69),
      (13, 'Tablet Samsung Galaxy Tab S T700N', '16GB Wi-fi Tela Super Amoled WQXGA Tela 8.4 Android 4.4 Processador Octa Core com Quad 1.9 GHz + Quad 1.3 Ghz - Bronze', 1079.10),
      (14, 'Console PlayStation 4', '500GB + Controle Dualshock 4', 3499.90),
      (15, 'Console XBOX ONE', '500GB + 2 Jogos + Headset + Controle sem Fio + 14 Dias de Live', 1649.90)
";
$stmt = $conn->prepare($sql);
$stmt->execute();
echo "Registros inseridos com sucesso.\n";