var Cadastro = {
    init: function() {
        $('#telefone').mask(Cadastro.SPMaskBehavior, Cadastro.spOptions);
    },
    SPMaskBehavior: function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    spOptions:  {
        onKeyPress: function(val, e, field, options) {
            field.mask(Cadastro.SPMaskBehavior.apply({}, arguments), options);
          }
    }
};