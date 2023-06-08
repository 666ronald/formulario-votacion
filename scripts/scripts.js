$(document).ready(function () {
   
    $.ajax({
        url: 'controlador.php',
        method: 'POST',
        dataType: 'json',
        data: { ruta: 'cargarRegiones' }, // se llama al metodo cargarRegiones dentro del controlador como si fuese una "ruta" 
        success: function (data) {
            if (data.success) {
                // se recorren los datos del objeto regiones y se agregan al select
                $.each(data.regiones, function (index, region) {
                    $('#region').append('<option value="' + region.id + '">' + region.ordinal_symbol + "  " + region.name + '</option>');
                });
                // se recorren los datos del objeto candidatos y se agregan al select
                $.each(data.candidatos, function (index, candidato) {
                    $('#candidato').append('<option value="' + candidato.id + '">' + candidato.nombre + '</option>');
                });
            } else {
                alert(data.msg)
            }
        }
    });

    $("#region").on("change", function () {
        var region = $(this).val();
        if (region !== "0") {
            $.ajax({
                url: 'controlador.php',
                method: 'POST',
                dataType: 'json',
                data: { ruta: 'cargarComunas', region: region }, // se llama al metodo cargarComunas dentro del controlador como si fuese una "ruta" y se envia el parametro "region" para obtener la lista de comunas
                success: function (data) {
                    if (data.success) {
                        //se limpia el select de comunas para resetearlo cada vez que se cambie la region
                        $('#comuna').empty();
                        //se llena  el select comuna con los datos obtenidos segun la region seleccionada
                        $.each(data.data, function (index, comuna) {
                            $('#comuna').append('<option value="' + comuna.id + '">' + comuna.name + '</option>');
                        });
                    }
                }
            });
        }
    });

    $("#form-votacion").on("submit", function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "controlador.php",
            method: "POST",
            data: { ruta: 'setVotacion', form: formData },//se llama al metodo setVotacion dentro del controlador y se envia el formulario serializado como "request"  
            dataType: "json",
            success: function (res) {
                if (res.status == 1) {
                    var lista = $("<ul>"); // se crea una lista que se llenara iterando el resultado de mensajes de error
                    $.each(res.msg, function (index, campo) {
                        var listItem = $("<li>").text(campo); // se crea un elemento li con el contenido del campo
                        lista.append(listItem); // se agrega un item a la lista por cada error mostrando el mensaje
                    });
                    $("#alerta").append(lista);
                }else{
                    $("#alerta").html(res.msg);
                }
                // se cambia el color de la alerta usando clases en base la respuesta del backend
                if (res.status != 0) {
                    $("#alerta").removeClass('alert-success');
                    $("#alerta").addClass('alert-danger');
                } else {
                    $("#alerta").removeClass('alert-danger');
                    $("#alerta").addClass('alert-success');
                }

            }
        });
    });

    $('#rut').on('input', function (event) {
        // se valida que cuando se ingrese un punto se remplace por texto vacio
        let filteredValue =  $(this).val().replace(/\./g, '');
        // se actualiza el nuevo valor "filtrado" sin de puntos 
        $(this).val(filteredValue);
    });
});

