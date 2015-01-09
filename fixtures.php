<?php
require_once __DIR__ . "/src/AG/config/connectionPDO.php";

$conn = connectionPDO();

$conn->query("DROP SCHEMA IF EXISTS `silexdb`");
$conn->query("CREATE SCHEMA `silexdb`");
$conn->query("USE `silexdb`");

$conn->query("DROP TABLE IF EXISTS `produtos`");

$conn->query("CREATE TABLE `produtos`(
  `id` INT(11) AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` text NOT NULL,
  `valor` FLOAT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");