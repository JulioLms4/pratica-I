<?php
// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'GerenciamentoChamados');
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Cadastro de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar_cliente') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];

    $stmt = $conn->prepare("INSERT INTO Clientes (Nome, Email, Telefone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $telefone);

    if ($stmt->execute()) {
        $mensagem = "Cliente cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar cliente: " . $conn->error;
    }
    $stmt->close();
}

// Listagem de chamados
$resultChamados = $conn->query("
    SELECT Chamados.ID, Clientes.Nome AS Cliente, Descricao_Problema, Criticidade, Status, Data_Abertura, 
           IFNULL(Colaboradores.Nome, 'Não atribuído') AS Colaborador
    FROM Chamados
    LEFT JOIN Clientes ON Chamados.ID_Cliente = Clientes.ID
    LEFT JOIN Colaboradores ON Chamados.ID_Colaborador = Colaboradores.ID
");

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Chamados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 0; }
        h1, h2 { color: #333; }
        form { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, button { width: 100%; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { background-color: #28a745; color: white; cursor: pointer; }
        button:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f8f9fa; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Gerenciamento de Chamados</h1>

    <!-- Mensagem de feedback -->
    <?php if (!empty($mensagem)): ?>
        <p class="success"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <h2>Cadastro de Cliente</h2>
    <form method="POST">
        <input type="hidden" name="action" value="cadastrar_cliente">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
        
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" required>
        
        <button type="submit">Cadastrar Cliente</button>
    </form>

    <h2>Lista de Chamados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Descrição</th>
                <th>Criticidade</th>
                <th>Status</th>
                <th>Data de Abertura</th>
                <th>Colaborador</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($chamado = $resultChamados->fetch_assoc()): ?>
                <tr>
                    <td><?= $chamado['ID'] ?></td>
                    <td><?= htmlspecialchars($chamado['Cliente']) ?></td>
                    <td><?= htmlspecialchars($chamado['Descricao_Problema']) ?></td>
                    <td><?= htmlspecialchars($chamado['Criticidade']) ?></td>
                    <td><?= htmlspecialchars($chamado['Status']) ?></td>
                    <td><?= htmlspecialchars($chamado['Data_Abertura']) ?></td>
                    <td><?= htmlspecialchars($chamado['Colaborador']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
