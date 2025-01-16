<?php
// Definir diretórios de upload
$uploadDir = 'uploads/';

// Função para limpar os dados do formulário
function cleanData($data) {
    return htmlspecialchars(trim($data));
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpar e pegar os dados do formulário    
    $fullName = str_replace(':' , ' ', cleanData($_POST['fullName']));
    $birthDate = cleanData($_POST['birthDate']);
    $address = cleanData($_POST['address']);
    $email = cleanData($_POST['email']);
    $phone = cleanData($_POST['phone']);
    $gender = cleanData($_POST['gender']);
    
    // Variáveis para os arquivos
    $photo = $_FILES['photo'];
    $idDocument = $_FILES['idDocument'];

    // Variáveis de configuração
    $sendEmail = isset($_POST['send-email']) ? true : false;

    // Validação de arquivos
    $errors = [];
    if ($photo['error'] != UPLOAD_ERR_OK) {
        $errors[] = 'Erro no upload da fotografia.';
    } else {
        $photoPath = $uploadDir . basename($photo['name']);
        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            $errors[] = 'Falha ao mover a fotografia para o diretório de uploads.';
        }
    }

    if ($idDocument['error'] != UPLOAD_ERR_OK) {
        $errors[] = 'Erro no upload do documento de identificação.';
    } else {
        $idDocPath = $uploadDir . basename($idDocument['name']);
        if (!move_uploaded_file($idDocument['tmp_name'], $idDocPath)) {
            $errors[] = 'Falha ao mover o documento de identificação para o diretório de uploads.';
        }
    }

    // Verificar se há erros
    if (count($errors) > 0) {
        // Exibir erros
        echo '<ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul>';
    } else {
        echo '<div class="profile-container">';
        echo '<h3 id=Titulo class="text-center mb-4">Dados do Utilizador</h3>';
        echo '<div class="profile-info">';
        echo '<div id="Name" ><strong>Nome Completo:</strong> ' . $fullName . '</div>';
        echo '<div id="Data" ><strong>Data de Nascimento:</strong> ' . $birthDate . '</div>';
        echo '<div id="Address" ><strong>Morada:</strong> ' . $address . '</div>';
        echo '<div id="Email" ><strong>Email:</strong> ' . $email . '</div>';
        echo '<div id="Phone" ><strong>Nº de Telefone:</strong> ' . $phone . '</div>';
        echo '<div id="Gender" ><strong>Género:</strong> ' . $gender . '</div>';
        echo '</div>';

        // Exibir a fotografia
        if ($photo['error'] == UPLOAD_ERR_OK) {
            echo '<div id=Photo class="profile-photo">';
            echo '<strong>Fotografia:</strong><br>';
            echo '<img src="' . $photoPath . '" alt="Fotografia" class="profile-img">';
            echo '</div>';
        }

        // Exibir o link do documento de identificação
        if ($idDocument['error'] == UPLOAD_ERR_OK) {
            echo '<div id="Doc" ><strong>Documento de Identificação:</strong> <a href="' . $idDocPath . '" target="_blank">Visualizar</a></div>';
        }

        // Se o usuário escolheu enviar por e-mail
        if ($sendEmail) {
            // Criar o conteúdo HTML da página
            $emailContent = '
                <html>
                <head>
                    <title>Dados Submetidos</title>
                </head>
                <body>
                    <h3>Dados Submetidos</h3>
                    <p><strong>Nome Completo: </strong>' . $fullName . '</p>
                    <p><strong>Data de Nascimento: </strong>' . $birthDate . '</p>
                    <p><strong>Morada: </strong>' . $address . '</p>
                    <p><strong>Email: </strong>' . $email . '</p>
                    <p><strong>Nº de Telefone: </strong>' . $phone . '</p>
                    <p><strong>Género: </strong>' . $gender . '</p>';

            // Adicionar a fotografia
            if ($photo['error'] == UPLOAD_ERR_OK) {
                $emailContent .= '<p><strong>Fotografia:</strong><br>';
                $emailContent .= '<img src="' . $photoPath . '" alt="Fotografia" style="max-width: 300px; max-height: 300px; margin-top: 10px; border: 1px solid #ddd; padding: 5px;"></p>';
            }

            // Adicionar o link do documento de identificação
            if ($idDocument['error'] == UPLOAD_ERR_OK) {
                $emailContent .= '<p><strong>Documento de Identificação:</strong> <a href="' . $idDocPath . '" target="_blank">Visualizar</a></p>';
            }

            $emailContent .= '</body></html>';

            // Definir os cabeçalhos para um e-mail HTML
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: no-reply@seusite.com" . "\r\n";

            // Enviar o e-mail com os dados processados
            if (mail($email, "Novo Registo de Utilizador", $emailContent, $headers)) {
                echo '<p>Email enviado com sucesso!</p>';
            } else {
                echo '<p>Erro ao enviar o e-mail.</p>';
            }
        }
        echo '</div>';
    }
} else {
    echo 'O formulário não foi enviado corretamente.';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo de Utilizador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            transition: background-color 0.3s, color 0.3s;
        }

        .profile-container {
            width: 80%;
            max-width: 900px;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .accessibility-popup {
            display: none;
            position: fixed;
            top: 1px;
            padding: 20px;
            border-radius: 8px;
        }

        .btn-accessibility {
            position: fixed;
            bottom: 50px;
            left: 20px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            z-index: 1050;
        }

        .profile-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #007bff;
        }

        .profile-photo {
            margin-top: 20px;
            text-align: center;
        }

        .profile-img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 50%;
            border: 3px solid #007bff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-info div {
            margin-bottom: 10px;
            font-size: 14px;
        }

        /* Estilos para modos de daltonismo */
        body.protanopia {
            filter: grayscale(80%) sepia(20%);
        }

        body.deuteranopia {
            filter: grayscale(50%) sepia(50%);
        }

        body.tritanopia {
            filter: hue-rotate(90deg);
        }

        body.acromatopsia {
            filter: grayscale(100%);
        }
        
        body.dark-mode {
            background-color: #4a4a4a;
            color: #ffffff;
        }

        .dark-mode {
            background-color: #4a4a4a !important;
            color: #ffffff !important;
        }

        .dark-mode .form-container, .dark-mode .accessibility-popup {
            background-color: #4a4a4a;
        }

        .dark-mode *:not(.form-container) {
            background-color: #4a4a4a !important;
            color: #ffffff !important;
        }

        .dark-mode ::placeholder {
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<!-- Popup de Acessibilidade -->
<div class="accessibility-popup" id="accessibility-popup">
    <h5 id="popup-title">Acessibilidade</h5>
    <div>
        <label id="label-font-size" for="font-size">Ajustar tamanho da fonte:</label>
        <select id="font-size" onchange="adjustFontSize()">
            <option value="14px">Pequeno</option>
            <option value="18px">Médio</option>
            <option value="22px">Grande</option>
        </select>
    </div>
    <div>
        <label for="language-select" id="label-language">Idioma:</label>
        <select id="language-select" onchange="translatePage()">
            <option value="pt">Português</option>
            <option value="en">Inglês</option>
            <option value="es">Espanhol</option>
        </select>
    </div>
    <div>
        <label for="colorblind-mode" id="label-colorblind-mode">Modo Daltonismo:</label>
        <select id="colorblind-mode" onchange="adjustColorblindMode()">
            <option value="normal">Normal</option>
            <option value="protanopia">Protanopia</option>
            <option value="deuteranopia">Deuteranopia</option>
            <option value="tritanopia">Tritanopia</option>
            <option value="acromatopsia">Acromatopsia</option>
        </select>
    </div>
    <div>
        <label for="dark-mode" id="label-dark-mode">Modo Escuro:</label>
        <input type="checkbox" id="dark-mode" onchange="toggleDarkMode()">
    </div>
    <div>
        <button class="btn btn-secondary" id= 'btn btn-secondary' onclick="closePopup()">Fechar</button>
    </div>
</div>

<!-- Botão de acessibilidade -->
<button id="btnAccessibility" class="btn-accessibility" onclick="togglePopup()">Acessibilidade</button>

<script>
// Função para abrir o popup
function togglePopup() {
    const popup = document.getElementById('accessibility-popup');                        
    popup.style.display = 'block';
}

// Função para fechar o popup
function closePopup() {    
    const popup = document.getElementById('accessibility-popup');            
    popup.style.display = 'none';
}

// Função para ajustar o tamanho da fonte
function adjustFontSize() {
    var fontSize = document.getElementById('font-size').value;
    document.body.style.fontSize = fontSize;
    document.querySelector('.profile-container').style.fontSize = fontSize;
    document.querySelectorAll('.profile-info div').forEach(function(el) {
        el.style.fontSize = fontSize;
    });
}

// Função para traduzir a página
function translatePage() {
    var language = document.getElementById('language-select').value;
    //['id','en','es','pt']
    var traducoes = [
        ['Name','Full Name:','Nombre Completo:','Nome Completo:'],
        ['Address','Address:','Vivienda:','Morada:'],
        ['Phone','Phone Number:','Numero de Telefono:','Numero de Telefone:'],
        ['Gender','Gender:','Genéro:','Genero:'],
        ['Photo','Photo:','Retrato:','Fotografia:'],
        ['Doc','Identification Document:','Documento de Identificación:','Documento de Identificaçao:'],
        ['Data','Birth Date:','Fecha de nacimiento:','Data de Nascimento:']];

    console.log(language);
    if (language == 'en') {
        document.getElementById('popup-title').innerText = 'Accessibility';
        document.getElementById('label-font-size').innerText = 'Font Size:';
        document.getElementById('label-language').innerText = 'Language:';
        document.getElementById('label-colorblind-mode').innerText = 'Colorblind Mode:';
        document.getElementById('label-dark-mode').innerText = 'Dark Mode:';
        document.getElementById('btn btn-secondary').innerText = 'Close';
        document.getElementById('btnAccessibility').innerText = 'Accessibility';   
        document.getElementById('Titulo').innerText = 'User Data';                     
        
            
    traducoes.forEach(item => {            
        var temp1 = document.getElementById(item[0]).getHTML();
        var temp1_index = temp1.indexOf('</strong>');
        var temp1_lixo = temp1.slice(0,(temp1_index + 9));
        var temp1_value = temp1.replace(temp1_lixo,'');
        document.getElementById(item[0]).innerHTML = '<strong>'+ item[1]+'</strong> '+ temp1_value;        
    })  
  


    } else if (language == 'es') {
        document.getElementById('popup-title').innerText = 'Accesibilidad';
        document.getElementById('label-font-size').innerText = 'Tamaño de Fuente:';
        document.getElementById('label-language').innerText = 'Idioma:';
        document.getElementById('label-colorblind-mode').innerText = 'Modo Daltonismo:';
        document.getElementById('label-dark-mode').innerText = 'Modo Oscuro:';
        document.getElementById('btn btn-secondary').innerText = 'Cerrar';
        document.getElementById('btnAccessibility').innerText = 'Accesibilidad'; 
        document.getElementById('Titulo').innerText = 'Datos del usuario';   

        traducoes.forEach(item => {            
            var temp1 = document.getElementById(item[0]).getHTML();
            var temp1_index = temp1.indexOf('</strong>');
            var temp1_lixo = temp1.slice(0,(temp1_index + 9));
            var temp1_value = temp1.replace(temp1_lixo,'');
            document.getElementById(item[0]).innerHTML = '<strong>'+ item[2]+'</strong> '+ temp1_value;        
        }) 
    } else {
        document.getElementById('popup-title').innerText = 'Acessibilidade';
        document.getElementById('label-font-size').innerText = 'Ajustar tamanho da fonte:';
        document.getElementById('label-language').innerText = 'Idioma:';
        document.getElementById('label-colorblind-mode').innerText = 'Modo Daltonismo:';
        document.getElementById('label-dark-mode').innerText = 'Modo Escuro:';
        document.getElementById('btn btn-secondary').innerText = 'Fechar';
        document.getElementById('btnAccessibility').innerText = 'Acessibilidade';
        document.getElementById('Titulo').innerText = 'Dados do Utilizador'; 
        

        traducoes.forEach(item => {            
            var temp1 = document.getElementById(item[0]).getHTML();
            var temp1_index = temp1.indexOf('</strong>');
            var temp1_lixo = temp1.slice(0,(temp1_index + 9));
            var temp1_value = temp1.replace(temp1_lixo,'');
            document.getElementById(item[0]).innerHTML = '<strong>'+ item[3]+'</strong> '+ temp1_value;        
        })
    }
}

// Função para ajustar o modo daltonismo
function adjustColorblindMode() {
    var mode = document.getElementById('colorblind-mode').value;

    // Remover todas as classes de daltonismo
    document.body.classList.remove('protanopia', 'deuteranopia', 'tritanopia', 'acromatopsia');

    // Adicionar a classe correspondente
    if (mode !== 'normal') {
        document.body.classList.add(mode);
    }
}

// Função para alternar o modo escuro
function toggleDarkMode() {
    var isChecked = document.getElementById('dark-mode').checked;
    if (isChecked) {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }
}
</script>

</body>
</html>
