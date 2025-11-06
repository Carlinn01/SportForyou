<?php
// Script de debug para verificar esportes favoritos
session_start();
require_once "login/src/ConexaoBD.php";

$idusuario = $_SESSION['idusuarios'] ?? 1; // Use seu ID ou deixe 1 como padrão
$conexao = ConexaoBD::conectar();

echo "<h2>Debug - Esportes Favoritos</h2>";

// Verifica se as colunas existem
echo "<h3>1. Verificando se as colunas existem:</h3>";
$colunas = ['esportes_favoritos', 'esportes_detalhados'];
foreach ($colunas as $coluna) {
    try {
        $sql = "SHOW COLUMNS FROM usuarios LIKE '$coluna'";
        $stmt = $conexao->query($sql);
        $existe = $stmt->rowCount() > 0;
        echo "<p>Coluna <strong>$coluna</strong>: " . ($existe ? "✅ EXISTE" : "❌ NÃO EXISTE") . "</p>";
    } catch (PDOException $e) {
        echo "<p>Erro ao verificar coluna $coluna: " . $e->getMessage() . "</p>";
    }
}

// Verifica os dados salvos
echo "<h3>2. Dados salvos no banco:</h3>";
// Primeiro verifica se a coluna esportes_detalhados existe antes de tentar buscar
$sql = "SHOW COLUMNS FROM usuarios LIKE 'esportes_detalhados'";
$stmt = $conexao->query($sql);
$tem_esportes_detalhados = $stmt->rowCount() > 0;

if ($tem_esportes_detalhados) {
    $sql = "SELECT idusuarios, nome, esportes_favoritos, esportes_detalhados FROM usuarios WHERE idusuarios = ?";
} else {
    $sql = "SELECT idusuarios, nome, esportes_favoritos FROM usuarios WHERE idusuarios = ?";
}
$stmt = $conexao->prepare($sql);
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    echo "<p><strong>ID:</strong> " . $usuario['idusuarios'] . "</p>";
    echo "<p><strong>Nome:</strong> " . $usuario['nome'] . "</p>";
    echo "<p><strong>esportes_favoritos (raw):</strong> " . ($usuario['esportes_favoritos'] ?? 'NULL') . "</p>";
    echo "<p><strong>esportes_detalhados (raw):</strong> " . ($usuario['esportes_detalhados'] ?? 'NULL') . "</p>";
    
    if (!empty($usuario['esportes_favoritos'])) {
        $decoded = json_decode($usuario['esportes_favoritos'], true);
        echo "<p><strong>esportes_favoritos (decoded):</strong> ";
        print_r($decoded);
        echo "</p>";
    }
    
    if ($tem_esportes_detalhados && !empty($usuario['esportes_detalhados'])) {
        $decoded = json_decode($usuario['esportes_detalhados'], true);
        echo "<p><strong>esportes_detalhados (decoded):</strong> ";
        print_r($decoded);
        echo "</p>";
    } elseif (!$tem_esportes_detalhados) {
        echo "<p><strong>esportes_detalhados:</strong> Coluna não existe no banco</p>";
    }
} else {
    echo "<p>Usuário não encontrado!</p>";
}

// Testa criar as colunas se não existirem
echo "<h3>3. Tentando criar colunas se não existirem:</h3>";
try {
    $sql = "SHOW COLUMNS FROM usuarios LIKE 'esportes_favoritos'";
    $stmt = $conexao->query($sql);
    if ($stmt->rowCount() == 0) {
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_favoritos TEXT DEFAULT NULL");
        echo "<p>✅ Coluna esportes_favoritos criada!</p>";
    } else {
        echo "<p>Coluna esportes_favoritos já existe.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Erro ao criar coluna esportes_favoritos: " . $e->getMessage() . "</p>";
}

try {
    $sql = "SHOW COLUMNS FROM usuarios LIKE 'esportes_detalhados'";
    $stmt = $conexao->query($sql);
    if ($stmt->rowCount() == 0) {
        // Tenta criar a coluna após esportes_favoritos
        try {
            $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_detalhados TEXT DEFAULT NULL AFTER esportes_favoritos");
            echo "<p>✅ Coluna esportes_detalhados criada com sucesso!</p>";
        } catch (PDOException $e2) {
            // Se falhar, tenta criar sem especificar posição
            try {
                $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_detalhados TEXT DEFAULT NULL");
                echo "<p>✅ Coluna esportes_detalhados criada com sucesso (sem posição específica)!</p>";
            } catch (PDOException $e3) {
                echo "<p>❌ Erro ao criar coluna esportes_detalhados: " . $e3->getMessage() . "</p>";
            }
        }
    } else {
        echo "<p>✅ Coluna esportes_detalhados já existe.</p>";
    }
} catch (PDOException $e) {
    echo "<p>❌ Erro ao verificar/criar coluna esportes_detalhados: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='pages/perfil.php?id=$idusuario'>Voltar ao Perfil</a></p>";
?>

