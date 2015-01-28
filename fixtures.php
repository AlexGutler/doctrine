<?php
require_once __DIR__ ."/vendor/autoload.php";
use AG\Database\DB;
$config = include __DIR__ . "/src/AG/config/config.php";

$db = new DB($config['db']['dsn'], $config['db']['dbname'], $config['db']['username'], $config['db']['password']);
$conn = $db->getConnection();
echo "Conectado ao banco de dados.\n";

$conn->query("USE silexdb");
echo "Schema criado com sucesso.\n";


$conn->query("DROP TABLE IF EXISTS produtos_tags;");
$conn->query("DROP TABLE IF EXISTS produtos;");
$conn->query("DROP TABLE IF EXISTS tags;");
$conn->query("DROP TABLE IF EXISTS categorias;");


$conn->query("CREATE TABLE categorias (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");

$sql = "INSERT INTO categorias (id, nome) VALUES
(1, 'Eletrônicos'), (2, 'Games');";
$stmt = $conn->prepare($sql);
$stmt->execute();

$conn->query("CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL,
              PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");

$sql = "INSERT INTO tags (id, nome) VALUES
(1, 'FIFA'), (2, 'PS4'), (3, 'Samsung'), (4, 'Smart');";
$stmt = $conn->prepare($sql);
$stmt->execute();

$conn->query("CREATE TABLE produtos (id INT AUTO_INCREMENT NOT NULL,
            categoria_id INT DEFAULT NULL, nome VARCHAR(255) NOT NULL, descricao LONGTEXT NOT NULL,
            valor DOUBLE PRECISION NOT NULL, INDEX IDX_3E524353397707A (categoria_id), PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");

$conn->query("CREATE TABLE produtos_tags (produto_id INT NOT NULL, tag_id INT NOT NULL,
            INDEX IDX_F00CAA2A105CFD56 (produto_id), INDEX IDX_F00CAA2ABAD26311 (tag_id),
            PRIMARY KEY(produto_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");

$conn->query("ALTER TABLE produtos ADD CONSTRAINT FK_3E524353397707A FOREIGN KEY (categoria_id) REFERENCES categorias (id)
              ON DELETE SET NULL;");

$conn->query("ALTER TABLE produtos_tags ADD CONSTRAINT FK_F00CAA2A105CFD56 FOREIGN KEY (produto_id) REFERENCES produtos (id);");

$conn->query("ALTER TABLE produtos_tags ADD CONSTRAINT FK_F00CAA2ABAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE;");

$sql = "INSERT INTO produtos (id, nome, descricao, valor, categoria_id) VALUES
(1, 'FIFA 15', 'Eletronic Arts, PS4, Português', 152.90, 2),
(2, 'Smart TV Samsung', 'Smart TV 3D LED 55” 4K Ultra HD Curva Samsung ', 5299.00, 1);";
$stmt = $conn->prepare($sql);
$stmt->execute();

$sql = "INSERT INTO produtos_tags (produto_id, tag_id) VALUES
(1, 1), (1, 2), (2, 3), (2, 4);";
$stmt = $conn->prepare($sql);
$stmt->execute();

echo "Tabelas criadas com sucesso.\n";
echo "Dados inseridos com sucesso.\n";