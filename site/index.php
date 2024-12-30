<?php
// Iniciar a sessão, caso ainda não esteja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['nome'])) {
    // Redirecionar para a página de login
    header("Location: https://dmbarber.dmprojetos.com");
    exit();
}
?>

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
        body {
            background-image: url('assets/img/fotofundo.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .container {
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
        }
        .logo img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .search-bar {
            position: relative;
            margin-top: 20px;
        }
        .search-bar input[type="text"] {
            width: 75%;
            margin: 15px;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            outline: none;
        }
        .search-bar button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #444;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #666;
        }
        .autocomplete-suggestions {
            position: absolute;
            top: 65px;
            left: 12.5%;
            width: 75%;
            background: white;
            border-radius: 5px;
            overflow: hidden;
            z-index: 1000;
            text-align: left;
        }
        .autocomplete-suggestions div {
            display: flex;
            align-items: center;
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            color: #333;
        }
        .autocomplete-suggestions div img {
            width: 40px;
            height: 40px;
            border-radius: 5px;
            margin-right: 10px;
        }
        .autocomplete-suggestions div:hover {
            background-color: #f0f0f0;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
const barbearias = [
    { nome: "Baianoducorte", logo: "assets/img/baianoducorte.png", link: "https://dmbarber.dmprojetos.com/site/baianoducorte/baianoducorte.php" },
    { nome: "Telles", logo: "assets/img/telles.png", link: "https://dmbarber.dmprojetos.com/site/telles/telles.php" },
    { nome: "Teste", logo: "assets/img/teste.png", link: "https://dmbarber.dmprojetos.com/site/teste/teste.php" },
    { nome: "Urban", logo: "assets/img/urban.png", link: "https://dmbarber.dmprojetos.com/site/urban/urban.php" },
    { nome: "Gonçalves", logo: "assets/img/logogoncalves.png", link: "https://dmbarber.dmprojetos.com/site/goncalves/goncalves.php" },
    { nome: "Lords Vera Cruz", logo: "assets/img/logolords1.png", link: "https://dmbarber.dmprojetos.com/site/lordsveracruz/lordsveracruz.php" },
    { nome: "Lords Chicuta", logo: "assets/img/logolords2.png", link: "https://dmbarber.dmprojetos.com/site/lordschicuta/lordschicuta.php" }
];


            const searchInput = document.querySelector('input[name="search"]');
            const suggestionsBox = document.createElement('div');
            suggestionsBox.classList.add('autocomplete-suggestions');
            document.querySelector('.search-bar').appendChild(suggestionsBox);

            searchInput.addEventListener("input", function () {
                const query = this.value.toLowerCase();
                suggestionsBox.innerHTML = "";

                if (query.length > 0) {
                    const suggestions = barbearias.filter(barbearia =>
                        barbearia.nome.toLowerCase().includes(query)
                    );

                    suggestions.forEach(barbearia => {
                        const suggestionItem = document.createElement("div");
                        suggestionItem.innerHTML = `
                            <img src="${barbearia.logo}" alt="${barbearia.nome}">
                            <span>${barbearia.nome}</span>
                        `;
                        suggestionItem.addEventListener("click", function () {
                            substituirLogo(barbearia);
                            searchInput.value = barbearia.nome;
                            suggestionsBox.innerHTML = "";
                        });
                        suggestionsBox.appendChild(suggestionItem);
                    });
                }
            });

            function substituirLogo(barbearia) {
                const logoContainer = document.querySelector('.logo');
                logoContainer.innerHTML = `
                    <a href="${barbearia.link}">
                        <img src="${barbearia.logo}" alt="Logo da ${barbearia.nome}">
                    </a>
                    <p>Clique na imagem</p>
                `;
            }
        });

        function substituirLogo(event) {
            event.preventDefault();

            const nomeBarbearia = document.querySelector('input[name="search"]').value.toLowerCase();
            const barbearia = barbearias.find(b => b.nome.toLowerCase() === nomeBarbearia);

            if (!barbearia) {
                alert("Barbearia não encontrada.");
                return;
            }

            const logoContainer = document.querySelector('.logo');
            logoContainer.innerHTML = `
                <a href="${barbearia.link}">
                    <img src="${barbearia.logo}" alt="Logo da ${barbearia.nome}">
                </a>
                <p>Clique na imagem</p>
            `;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="assets/img/logo.png" alt="Logo da Barbearia">
        </div>
        <form class="search-bar" onsubmit="substituirLogo(event)">
            <input type="text" placeholder="Pesquise por uma barbearia..." name="search" required>
        </form>
    </div>
</body>
</html>
