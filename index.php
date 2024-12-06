<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Entregas</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="header">
    <img src="img/icone.png" alt="Ícone" class="icon"> 
</div>

    <div class="container">
        <h1>Cadastro de Entrega</h1>
        
        <form class="delivery-form" action="processar.php" method="post">
            <label for="partida">Endereço Atual (Rua, Bairro, Número):</label>
            <input type="text" id="partida" name="partida" required>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="quantidade">Quantidade de Caixas:</label>
            <input type="number" id="quantidade" name="quantidade" required>

            <label for="status">Status:</label>
            <input type="text" id="status" name="status" required>

            <label for="endereco">Endereço de Entrega (Rua, Bairro, Número):</label>
            <input type="text" id="endereco" name="endereco" required>

            <button type="submit" class="btn-submit">Adicionar</button>
        </form>

        <form action="processar.php" method="post" class="reset-form">
            <input type="hidden" name="action" value="zerar">
            <button type="submit" class="btn-reset">Zerar Lista</button>
        </form>

        <div class="navigation-buttons">
            <button onclick="window.location.href='index.php'" class="btn-nav">Voltar</button>
            <button onclick="window.location.href='ver_lista.php'" class="btn-nav">Ver Lista</button>
        </div>

        <footer class="footer-container">
            <p>Desenvolvido por <span>@thaleshenriq</span></p>
        </footer>
    </div>
</body>
</html>
