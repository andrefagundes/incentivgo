var Campanhas = {
    init: function(){
        Campanhas.pesquisarCampanhas();
    },
    pesquisarCampanhas: function(){
        $( "#pesquisarCampanhas" ).load("pesquisar-campanhas");
    }
};