<?php
// Iniciar a sessão, caso ainda não esteja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Exibir a mensagem de boas-vindas se estiver logado
echo "<span style='color: white;'>Bem-vindo, " . $_SESSION['nome'] . "</span>";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
/* Estilos gerais */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f2f2f2;
    background-image: url('assets/fotofundobaiano.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 20px;
}

.container {
    max-width: 700px;
    width: 100%;
    margin: 20px auto;
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    display: flex;
    justify-content: center;
}

.responsive-image {
    width: 100%;
    height: auto;
    max-width: 300px;
    object-fit: contain;
}

.custom-modal,
.day-modal,
.time-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    max-width: 500px;
    background-color: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    padding: 20px;
    border-radius: 8px;
    z-index: 1000;
    text-align: center;
}

.agendamento-btn {
    background-color: #FFC107;
    color: black;
    font-weight: bold;
    padding: 15px 30px;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    margin-bottom: 20px;
}

.agendamento-btn:hover {
    background-color: #e0a800;
}

.selected {
    background-color: #0d6efd !important;
    color: white !important;
}

#serviceContainer {
    display: none;
}

/* Estilos para dispositivos móveis */
@media only screen and (max-width: 600px) {
    .container {
        width: 90%;
        padding: 15px;
        margin: 10px auto;
    }

    .logo-image {
        width: 100%;
        max-width: 300px;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        object-fit: cover;
    }

    .values-image {
        width: 100%;
        max-width: 100%;
        border-radius: 8px;
    }

    .agendamento-btn {
        width: 100%;
        font-size: 16px;
        padding: 12px 0;
    }

    .custom-modal,
    .day-modal,
    .time-modal {
        width: 90%;
        max-width: 400px;
        padding: 15px;
    }
}

/* Estilos para desktops */
@media only screen and (min-width: 601px) {
    .logo-image {
        width: 100%;
        height: auto;
        max-width: 300px;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        object-fit: cover;
    }

    .values-image {
        width: 100%;
        height: auto;
        max-width: 400px;
        object-fit: contain;
        border-radius: 8px;
    }
}

/* Botão Meus Agendamentos */
.agenda-button {
    margin-top: 20px;
    padding: 12px 25px;
    font-size: 18px;
    background-color: #4a4a9c;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
.agenda-button:hover {
    background-color: #666;
}

/* Estilos do Modal de Agendamentos */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: none;
}

.modal-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 400px;
    background-color: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    z-index: 1001;
    display: none;
    text-align: center;
}

.modal-content h2 {
    color: #4a4a9c; /* Cor do título */
    font-size: 1.5em;
    font-weight: bold;
    margin-bottom: 20px;
}

.modal-content p {
    font-size: 1em;
    color: #333;
    margin: 5px 0;
}

.modal-content hr {
    border: 0;
    border-top: 1px solid #ddd;
    margin: 15px 0;
}

.modal-content button {
    margin-top: 15px;
    padding: 10px 20px;
    font-size: 16px;
    background-color: #4a4a9c; /* Cor do botão */
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.modal-content button:hover {
    background-color: #666;
}

    </style>

</head>

<body>
<div class="container mb-4">
    <img src="assets/logobaiano.png" alt="Logo da Barbearia" class="responsive-image logo-image">
</div>

<div class="container mb-4">
    <img src="assets/valores.png" alt="Tabela de Valores" class="responsive-image values-image">
</div>

<div class="container mb-4">
    <button class="agenda-button" onclick="mostrarAgendamentos()">Meus Agendamentos</button>

</div>

<div class="modal-overlay" style="display: none;"></div>
<div class="modal-container" style="display: none;">
    <div class="modal-content">
        <!-- Conteúdo do modal será preenchido dinamicamente -->
    </div>
</div>

<div class="container mb-4">
<button class="agendamento-btn" onclick="abrirAgendamentoModal()">Faça seu agendamento</button>
</div>


<input type="hidden" name="nome" id="inputNome" value="<?php echo isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : ''; ?>">
<input type="hidden" name="telefone" id="inputTelefone" value="<?php echo isset($_SESSION['telefone']) ? htmlspecialchars($_SESSION['telefone']) : ''; ?>">
<input type="hidden" id="selectedProfissional" name="selectedProfissional">
<input type="hidden" id="selectedService" name="selectedService">
<input type="hidden" id="selectedDay" name="selectedDay">
<input type="hidden" id="selectedTime" name="selectedTime">

<div class="custom-modal-overlay" id="agendamentoModalOverlay"></div>
<div class="custom-modal" id="agendamentoModal">
    <button class="btn-close" onclick="fecharAgendamentoModal()">×</button>
    <h5>Escolha o Profissional</h5>
    <div class="modal-body">
        <div class="button-container">
            <input type="button" value="Baiano" class="btn btn-primary m-2" onclick="selecionarProfissional(this)">
            <input type="button" value="Gabriel" class="btn btn-primary m-2" onclick="selecionarProfissional(this)">
            <input type="button" value="Guilherme" class="btn btn-primary m-2" onclick="selecionarProfissional(this)">
        </div>
    </div>
</div>

<div class="custom-modal-overlay" id="modalOverlay"></div>
<div class="custom-modal" id="serviceModal">
    <button class="btn-close" onclick="fecharModal()">×</button>
    <h5>Escolha o Serviço</h5>
    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
        <div class="d-grid gap-2">
            <!-- Serviços Masculinos -->
            <h4 style="color: black;">Masculino</h4>
            <input type="button" value="Plano Mensal R$ 00" class="btn btn-secondary" onclick="selecionarServico(this)">
            <input type="button" value="Cabelo R$40" class="btn btn-secondary" onclick="selecionarServico(this)">
            <input type="button" value="Barba R$35" class="btn btn-secondary" onclick="selecionarServico(this)">
            <input type="button" value="Sobrancelha R$10" class="btn btn-secondary" onclick="selecionarServico(this)">
            <input type="button" value="Maquina R$25" class="btn btn-secondary" onclick="selecionarServico(this)">
            <input type="button" value="Cabelo e Barba R$65" class="btn btn-secondary" onclick="selecionarServico(this)">
        </div>
    </div>
    
    <!-- Botão Confirmar -->
<div class="modal-footer" style="display: flex; justify-content: center; margin-top: 10px;">
    <button class="btn btn-primary" onclick="abrirModalDias()">Confirmar</button>
</div>

</div>



<div class="day-modal-overlay" id="dayModalOverlay"></div>
<div class="day-modal" id="dayModal">
    <button class="btn-close" onclick="fecharModalDias()">×</button>
    <h5>Escolha o Dia</h5>
    <div class="modal-body">
        <div class="d-grid gap-2" id="dayButtonsContainer">
            <!-- Os botões dos dias serão inseridos aqui pelo JavaScript -->
        </div>
    </div>
    
    <div class="modal-footer" style="display: flex; justify-content: center; margin-top: 10px;">
        <button class="btn btn-primary" onclick="abrirModalHorarios()">Confirmar</button>
    </div>
</div>

<script>
    function exibirDiasDaSemana() {
    const diasDaSemana = [
        "Domingo",
        "Segunda-feira",
        "Terça-feira",
        "Quarta-feira",
        "Quinta-feira",
        "Sexta-feira",
        "Sábado"
    ];

    // Obter a data e hora atual no fuso horário de Brasília
    const hojeBrasilia = new Date().toLocaleString("en-US", { timeZone: "America/Sao_Paulo" });
    const dataAtual = new Date(hojeBrasilia);
    const diaAtual = dataAtual.getDay(); // Dia da semana atual (0 = Domingo, 6 = Sábado)
    const horaAtual = dataAtual.getHours(); // Hora atual

    const dayButtonsContainer = document.getElementById("dayButtonsContainer");
    dayButtonsContainer.innerHTML = ''; // Limpar o contêiner

    // Adicionar os dias começando do dia atual
    for (let i = 0; i < 7; i++) {
        const diaIndex = (diaAtual + i) % 7; // Pega o índice do dia da semana
        const dia = diasDaSemana[diaIndex];

        // Ignora domingo e o dia atual após as 19h
        if (diaIndex === 0 || (i === 0 && horaAtual >= 19)) {
            continue;
        }

        const btn = document.createElement("input");
        btn.type = "button";
        btn.value = dia;
        btn.className = "btn btn-secondary";
        btn.onclick = function () { selecionarDia(btn); };
        
        dayButtonsContainer.appendChild(btn);
    }
}

window.onload = exibirDiasDaSemana;

</script>

    
</div>

<div class="time-modal-overlay" id="timeModalOverlay"></div>
<div class="time-modal" id="timeModal">
    <button class="btn-close" onclick="fecharModalHorarios()">×</button>
    <h5>Escolha o Horário</h5>
    <div class="modal-body" id="timeButtonsContainer">
        <!-- Horários serão inseridos aqui dinamicamente -->
    </div>
        <div class="modal-footer" style="display: flex; justify-content: center; margin-top: 10px;">
    <button class="btn btn-primary" onclick="enviarAgendamento()">Confirmar</button>
</div>
    
</div>


<script>
    let selectedTimes = []; // Array para armazenar os horários selecionados
    let selectedServices = []; // Array para armazenar os serviços selecionados

    function abrirAgendamentoModal() {
        // Verificar se o usuário está logado
        if (!<?php echo isset($_SESSION['nome']) ? 'true' : 'false'; ?>) {
            // Armazenar a URL da página atual na sessão
            <?php $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; ?>
            // Redirecionar para a página de login
            window.location.href = "https://dmbarber.dmprojetos.com";
            return;
        }

        document.getElementById("agendamentoModal").style.display = "block";
        document.getElementById("agendamentoModalOverlay").style.display = "block";
    }

    function fecharAgendamentoModal() {
        document.getElementById("agendamentoModal").style.display = "none";
        document.getElementById("agendamentoModalOverlay").style.display = "none";
    }

    function selecionarProfissional(element) {
        document.getElementById("selectedProfissional").value = element.value;
        abrirModal();
    }

    function abrirModal() {
        document.getElementById("serviceModal").style.display = "block";
        document.getElementById("modalOverlay").style.display = "block";
    }

    function fecharModal() {
        document.getElementById("serviceModal").style.display = "none";
        document.getElementById("modalOverlay").style.display = "none";
    }

    function abrirModalDias() {
        fecharModal();
        document.getElementById("dayModal").style.display = "block";
        document.getElementById("dayModalOverlay").style.display = "block";
    }

    function fecharModalDias() {
        document.getElementById("dayModal").style.display = "none";
        document.getElementById("dayModalOverlay").style.display = "none";
    }

    function selecionarDia(element) {
        document.querySelectorAll('#dayModal .btn-secondary').forEach(btn => btn.classList.remove('selected'));
        element.classList.add('selected');
        const diaSelecionado = element.value;
        document.getElementById("selectedDay").value = calcularDataPorDiaSemana(diaSelecionado);
    }

    function calcularDataPorDiaSemana(diaSemana) {
        const hoje = new Date();
        const diasDaSemana = {
            "Segunda-feira": 1,
            "Terça-feira": 2,
            "Quarta-feira": 3,
            "Quinta-feira": 4,
            "Sexta-feira": 5,
            "Sábado": 6,
            "Domingo": 0
        };
        const diasParaAdicionar = (diasDaSemana[diaSemana] - hoje.getUTCDay() + 7) % 7;
        const novaData = new Date(hoje);
        novaData.setDate(hoje.getDate() + diasParaAdicionar);
        return novaData.toISOString().split('T')[0];
    }

function abrirModalHorarios() {
    fecharModalDias();
    document.getElementById("timeModal").style.display = "block";
    document.getElementById("timeModalOverlay").style.display = "block";

    const selectedDay = document.getElementById("selectedDay").value;
    const selectedProfissional = document.getElementById("selectedProfissional").value;

    // Envia a solicitação para buscar horários disponíveis e indisponíveis
    fetch("config/config.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `professional=${encodeURIComponent(selectedProfissional)}&day=${encodeURIComponent(selectedDay)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Erro ao buscar horários no servidor.");
        }
        return response.json();
    })
    .then(data => {
        const timeButtonsContainer = document.getElementById("timeButtonsContainer");
        timeButtonsContainer.innerHTML = ''; // Limpa os botões anteriores

        if (data.message) {
            // Exibe a mensagem de ordem de chegada caso enviada pelo backend
            Swal.fire({
                title: 'Informação',
                text: data.message,
                icon: 'info',
                confirmButtonText: 'Ok'
            });
        } else {
            const availableHours = data.availableHours;

            console.log("Horários disponíveis recebidos:", availableHours);
            console.log("Horários indisponíveis recebidos:", data.unavailableHours);

            // Exibe apenas os horários disponíveis recebidos do backend
            availableHours.forEach(horario => {
                const btn = document.createElement("input");
                btn.type = "button";
                btn.value = horario;
                btn.className = "btn btn-secondary m-1";
                btn.onclick = () => selecionarHorario(btn);
                timeButtonsContainer.appendChild(btn);
            });
        }
    })
    .catch(error => {
        console.error("Erro ao buscar horários:", error);
        Swal.fire({
            title: 'Erro',
            text: 'Erro ao buscar horários. Por favor, tente novamente.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
}


    function fecharModalHorarios() {
        document.getElementById("timeModal").style.display = "none";
        document.getElementById("timeModalOverlay").style.display = "none";
    }

    function selecionarHorario(btn) {
        const time = btn.value;

        if (selectedTimes.includes(time)) {
            selectedTimes = selectedTimes.filter(t => t !== time);
            btn.classList.remove("btn-primary");
            btn.classList.add("btn-secondary");
        } else {
            if (selectedTimes.length >= 2) {
                Swal.fire({
                    title: 'Aviso',
                    text: 'Você só pode selecionar até dois horários.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return;
            }
            selectedTimes.push(time);
            btn.classList.remove("btn-secondary");
            btn.classList.add("btn-primary");
        }

        document.getElementById("selectedTime").value = selectedTimes.join(", ");
    }

    function selecionarServico(btn) {
        const service = btn.value;

        if (selectedServices.includes(service)) {
            selectedServices = selectedServices.filter(s => s !== service);
            btn.classList.remove("btn-primary");
            btn.classList.add("btn-secondary");
        } else {
            if (selectedServices.length >= 2) {
                Swal.fire({
                    title: 'Aviso',
                    text: 'Você só pode selecionar até dois serviços.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return;
            }
            selectedServices.push(service);
            btn.classList.remove("btn-secondary");
            btn.classList.add("btn-primary");
        }

        document.getElementById("selectedService").value = selectedServices.join(", ");
    }

    function calcularTempoTotal() {
        if (selectedTimes.length === 2) {
            const time1 = new Date(`1970-01-01T${selectedTimes[0]}:00Z`);
            const time2 = new Date(`1970-01-01T${selectedTimes[1]}:00Z`);
            const diffInMinutes = Math.abs((time2 - time1) / (1000 * 60));
            return (diffInMinutes / 60).toFixed(2);
        }
        return "";
    }

 function enviarAgendamento() {
    const nome = document.getElementById("inputNome").value;
    const telefone = document.getElementById("inputTelefone").value;
    const profissional = document.getElementById("selectedProfissional").value;
    const servico = document.getElementById("selectedService").value;
    const dia = document.getElementById("selectedDay").value;

    const horario = selectedTimes[0];
    const horario_final = selectedTimes.length === 2 ? selectedTimes[1] : ""; // Definido apenas se houver dois horários
    const tempoTotal = selectedTimes.length === 2 ? calcularTempoTotal() : ""; // Calculado apenas para dois horários

    if (!nome || !telefone || !profissional || !servico || !dia || !horario) {
        let errorMessage = "Por favor, preencha todos os campos obrigatórios.";
        if (!nome) errorMessage += "\n- Nome está vazio.";
        if (!telefone) errorMessage += "\n- Telefone está vazio.";
        if (!profissional) errorMessage += "\n- Profissional não selecionado.";
        if (!servico) errorMessage += "\n- Serviço não selecionado.";
        if (!dia) errorMessage += "\n- Dia não selecionado.";
        if (!horario) errorMessage += "\n- Horário inicial não selecionado.";

        Swal.fire({
            title: 'Campos Incompletos',
            text: errorMessage,
            icon: 'warning',
            confirmButtonText: 'Ok'
        });
        return;
    }

    // Corpo de dados comum para ambas as requisições
    const bodyData = `nome=${encodeURIComponent(nome)}&telefone=${encodeURIComponent(telefone)}&profissional=${encodeURIComponent(profissional)}&servico=${encodeURIComponent(servico)}&dia=${encodeURIComponent(dia)}&horario=${encodeURIComponent(horario)}&horario_final=${encodeURIComponent(horario_final)}&tempo_total=${encodeURIComponent(tempoTotal)}`;

    // Primeira requisição para config.php
    fetch("config/config.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: bodyData
    })
    .then(response => response.text())
    .then(result => {
        // Mostra o alerta e aguarda o usuário clicar em "Ok"
        return Swal.fire({
            title: 'Agendamento Enviado',
            text: result,
            icon: 'success',
            confirmButtonText: 'Ok'
        });
    })
    .then(() => {
        // Segunda requisição para envioemail.php só acontece após o clique em "Ok"
        return fetch("config/envioemail.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: bodyData
        });
    })
    .then(emailResponse => emailResponse.text())
    .then(emailResult => {
        // Removido o alerta de e-mail enviado
        console.log("E-mail enviado:", emailResult); // Apenas um log no console
        location.reload(); // Ainda recarrega a página após o envio
    })
    .catch(error => {
        Swal.fire({
            title: 'Erro',
            text: 'Erro ao enviar agendamento ou e-mail. Por favor, tente novamente.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
        console.error("Erro ao enviar agendamento ou e-mail:", error);
    });
}

function mostrarAgendamentos() {
        fetch('agendamentos.php')
            .then(response => response.json())
            .then(data => {
                const modalContent = document.querySelector('.modal-content');
                modalContent.innerHTML = '<h2>Meus Agendamentos</h2>';

                if (data.error) {
                    modalContent.innerHTML += `<p>${data.error}</p>`;
                } else if (data.length === 0) {
                    modalContent.innerHTML += '<p>Nenhum agendamento encontrado.</p>';
                } else {
                    data.forEach(agendamento => {
                        modalContent.innerHTML += `
                            <p>Agendamento: Nº ${agendamento.id_agendamento}</p>
                            <p>Data: ${agendamento.dia}</p>
                            <p>Horário: ${agendamento.horario}</p>
                            <p>Profissional: ${agendamento.profissional}</p>
                            <p>Status: ${agendamento.status}</p>
                            <p>Serviço: ${agendamento.servico}</p>
                            <button onclick="cancelarAgendamento(${agendamento.id_agendamento}, '${agendamento.dia}', '${agendamento.horario}', '${agendamento.profissional}', '${agendamento.servico}')">Cancelar Agendamento</button>
                            <hr>
                        `;
                    });
                }

                modalContent.innerHTML += '<button onclick="fecharAgendamentos()">Fechar</button>';
                document.querySelector('.modal-container').style.display = 'block';
                document.querySelector('.modal-overlay').style.display = 'block';
            })
            .catch(error => {
                console.error('Erro ao buscar agendamentos:', error);
                Swal.fire({
                    title: 'Erro',
                    text: 'Erro ao buscar agendamentos. Por favor, tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
    }

    function fecharAgendamentos() {
        document.querySelector('.modal-container').style.display = 'none';
        document.querySelector('.modal-overlay').style.display = 'none';
    }

    // Função para cancelar agendamento
    function cancelarAgendamento(id_agendamento, dia, horario) {
        // Obter a data e hora atual no fuso horário de Brasília
        const hojeBrasilia = new Date().toLocaleString("en-US", { timeZone: "America/Sao_Paulo" });
        const agora = new Date(hojeBrasilia);
        const dataAgendamento = new Date(`${dia}T${horario}:00`);

        const umHoraEmMilissegundos = 60 * 60 * 1000;

        if (dataAgendamento - agora < umHoraEmMilissegundos) {
            Swal.fire({
                title: 'Erro!',
                text: 'Você só pode cancelar agendamentos com mais de uma hora de antecedência.',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
            return; // Interrompe a função se não puder cancelar
        }

        Swal.fire({
            title: 'Tem certeza?',
            text: `Você deseja cancelar o agendamento Nº ${id_agendamento}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, cancelar!',
            cancelButtonText: 'Não, manter'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(`Tentando cancelar agendamento: ${id_agendamento}`);
                
                // Log das informações que estão sendo enviadas
                const bodyData = `id_agendamento=${id_agendamento}`;
                console.log("Dados enviados para excluir_agendamento.php:", bodyData);

                fetch(`excluir_agendamento.php`, {
                    method: 'POST',
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: bodyData
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Erro na resposta da requisição:', response);
                        throw new Error('Erro na requisição ao servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('Cancelado!', 'Seu agendamento foi cancelado.', 'success');
                        mostrarAgendamentos(); // Atualiza a lista de agendamentos
                    } else {
                        Swal.fire('Erro!', 'Não foi possível cancelar o agendamento.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro ao cancelar agendamento:', error);
                    Swal.fire('Erro!', 'Erro ao cancelar o agendamento. Tente novamente.', 'error');
                });
            }
        });
    }
</script>

<div class="container mb-4">
    <img src="assets/planos.png" alt="Tabela de Valores" class="responsive-image values-image">
</div>





<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
