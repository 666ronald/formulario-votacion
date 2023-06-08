<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="scripts/scripts.js"></script>
    <title>FORMULARIO DE VOTACION</title>
</head>

<body>
    <h2>FORMULARIO DE VOTACION</h2>
    <div class="container">
        <form id="form-votacion">
            <div class="form-div">
                <label for="nombre" class="form-label">Nombre y Apellido</label>
                <input class="form-input" type="text" name="nombre" id="nombre">
            </div>
            <div class="form-div">
                <label for="alias" class="form-label">Alias</label>
                <input class="form-input" type="text" name="alias" id="alias">
            </div>
            <div class="form-div">
                <label for="rut" class="form-label">RUT</label>
                <input class="form-input" type="text" name="rut" id="rut">
            </div>
            <div class="form-div">
                <label for="email" class="form-label">Email</label>
                <input class="form-input" type="email" name="email" id="email">
            </div>
            <div class="form-div">
                <label for="region" class="form-label">Region</label>
                <select class="form-input" name="region" id="region">
                    <option value="0">Seleccione una Region</option>
                </select>
            </div>
            <div class="form-div">
                <label for="comuna" class="form-label">Comuna</label>
                <select class="form-input" name="comuna" id="comuna"></select>
            </div>
            <div class="form-div">
                <label for="candidato" class="form-label">Candidato</label>
                <select class="form-input" name="candidato" id="candidato"></select>
            </div>
            <div>
                <label>Como se entero de Nosotros</label>
                <input type="checkbox" name="web" id="web">
                <label for="web">Web</label>
                <input type="checkbox" name="tv" id="tv">
                <label for="tv">TV</label>
                <input type="checkbox" name="redes" id="redes">
                <label for="redes">Redes Sociales</label>
                <input type="checkbox" name="amigo" id="amigo">
                <label for="amigo">Amigo</label>
            </div>
            <br>
            <button type="submit">Votar</button>
        </form>

    </div>
    <div class="centrar">
        <div id="alerta">

        </div>
    </div>
</body>

</html>