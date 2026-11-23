document.addEventListener("DOMContentLoaded", function () {

    const formulario = document.getElementById("formulario");

    const mensagem = document.getElementById("mensagem");

    const contador = document.getElementById("contador");

    const erroOficinas = document.getElementById("erro-oficinas");

    const oficinas = document.querySelectorAll(
        'input[name="oficinas[]"]'
    );

    // Contador de caracteres
    mensagem.addEventListener("input", function () {

        const limite = 200;

        const quantidade = mensagem.value.length;

        const restantes = limite - quantidade;

        contador.textContent = "Restam " + restantes + " caracteres";

    });

    // Validação antes do envio
    formulario.addEventListener("submit", function (event) {

        const selecionadas = document.querySelectorAll(
            'input[name="oficinas[]"]:checked'
        );

        if (selecionadas.length === 0) {

            event.preventDefault();

            erroOficinas.textContent =
                "Selecione pelo menos uma oficina para continuar.";

            oficinas[0].focus();

        } else {

            erroOficinas.textContent = "";

        }

    });

    // Limpar mensagem quando selecionar uma oficina
    oficinas.forEach(function (oficina) {

        oficina.addEventListener("change", function () {

            const selecionadas = document.querySelectorAll(
                'input[name="oficinas[]"]:checked'
            );

            if (selecionadas.length > 0) {
                erroOficinas.textContent = "";
            }

        });

    });

});