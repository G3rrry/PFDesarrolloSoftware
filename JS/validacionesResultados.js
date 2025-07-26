function validarTenacidad(input) {
    var valor = parseInt(input.value);
    if (isNaN(valor)) {
        input.value = '';
    } else {
        if (valor < 50) {
            input.value = '50';
        } else if (valor > 55) {
            input.value = '55';
        }
    }
}
