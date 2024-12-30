<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busca de Barbearia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body, html {
            height: 100%;
            font-family: Arial, sans-serif;
        }
        /* Fundo */
        body {
            background-image: url('fundo.jpg'); /* Adicione a imagem de fundo */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        /* Container principal */
        .container {
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
        }
        /* Logo */
        .logo img {
            max-width: 100%;
            height: auto;
        }
        /* Barra de pesquisa */
        .search-bar {
            margin-top: 20px;
        }
        .search-bar input[type="text"] {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            outline: none;
        }
        .search-bar button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <img src="logo.png" alt="Logo da Barbearia">
        </div>
        <!-- Barra de Pesquisa -->
        <div class="search-bar">
            <input type="text" placeholder="Pesquise por uma barbearia..." name="search">
            <button type="submit">Buscar</button>
        </div>
    </div>
</body>
</html>
