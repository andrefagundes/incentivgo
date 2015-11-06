<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;
use Incentiv\Models\Desafio,
    Incentiv\Models\UsuarioPontuacaoCredito,
    Incentiv\Models\DesafioPontuacao;

/**
 * DesafioUsuario
 * Este modelo registra os usuários que participam do desafio lançado
 */
class DesafioUsuario extends Model
{
     public static $_instance;
     private $_lang = array();
     
     const DESAFIO_APROVADO  = 'Y';
     const DESAFIO_REPROVADO = 'N';

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $usuarioId;
    
    /**
     *
     * @var integer
     */
    public $desafioId;
    
    /**
     *
     * @var varchar
     */
    public $usuarioResposta;
    
    /**
     *
     * @var varchar
     */
    public $usuarioRespostaMotivo;
    
    /**
     *
     * @var date
     */
    public $usuarioRespostaDt;
    
    /**
     *
     * @var date
     */
    public $envioAprovacaoDt;
    
    /**
     *
     * @var char
     */
    public $desafioCumprido;
    
    public static function build()
    {
        if( !isset( self::$_instance ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function initialize()
    {
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario'
        ));
        $this->belongsTo('desafioId', 'Incentiv\Models\Desafio', 'id', array(
            'alias' => 'desafios'
        ));
    }
    
    public function buscarDesafiosUsuario(\stdClass $objDesafio){
        
        $formatDate = ($this->_lang['lang'] == 'pt-BR')?'%d/%m/%Y':'%m/%d/%Y';
        
        $desafios = $this::query()->columns(
                         array( 'Incentiv\Models\DesafioUsuario.id',
                                'Incentiv\Models\DesafioUsuario.usuarioResposta',
                                'Incentiv\Models\DesafioUsuario.desafioId',
                                'd.desafio',
                                'd.tipo',
                                'd.usuarioResponsavelId',
                                'usuarioResponsavel'=>'usuarioResponsavel.nome',
                                'DesafioPontuacao.pontuacao', 
                                'inicioDt' => "DATE_FORMAT( d.inicioDt , '{$formatDate}' )",
                                'fimDt' => "DATE_FORMAT( d.fimDt , '{$formatDate}' )",
                                'd.premiacao'));
        
        $desafios->innerjoin('Incentiv\Models\Desafio', 'Incentiv\Models\DesafioUsuario.desafioId = d.id', 'd');
        $desafios->innerjoin('Incentiv\Models\DesafioPontuacao', "d.empresaId = DesafioPontuacao.empresaId AND d.desafioTipoId = DesafioPontuacao.desafioTipoId AND DesafioPontuacao.ativo = 'Y'", 'DesafioPontuacao');
        $desafios->leftjoin('Incentiv\Models\Usuario', "d.usuarioResponsavelId = usuarioResponsavel.id", 'usuarioResponsavel');
        $desafios->andwhere( "Incentiv\Models\DesafioUsuario.usuarioId = {$objDesafio->usuarioId}");
        $desafios->andwhere( "Incentiv\Models\DesafioUsuario.usuarioResposta != 'N' OR Incentiv\Models\DesafioUsuario.usuarioResposta IS NULL");
        $desafios->andwhere( "Incentiv\Models\DesafioUsuario.envioAprovacaoDt IS NULL");
        $desafios->andwhere( "d.ativo = 'Y'");
        $desafios->orderBy('d.desafio');
        
        $desafiosUsuario = $desafios->execute()->toArray();
        
        foreach ($desafiosUsuario as $key => $desafio){
            $usuariosParticipantes = "";
            if($desafio['tipo'] == Desafio::DESAFIO_TIPO_EQUIPE){
                $desafiosUsuariosParticipantes = $this->find("desafioId = {$desafio['desafioId']}");
                foreach ($desafiosUsuariosParticipantes as $participante){
                    $usuariosParticipantes .= $participante->usuario->nome.', ';   
                }
                $desafiosUsuario[$key]['usuariosParticipantes'] = substr($usuariosParticipantes,0, strlen($usuariosParticipantes)-2);
            }else{
                $desafiosUsuario[$key]['usuariosParticipantes'] = '';
            }
        }
        return $desafiosUsuario;
    }
    
    public function responderDesafioUsuario(\stdClass $objDesafio){
        
        $desafioUsuario = $this->findFirst("id = ".$objDesafio->id);
        
        $desafioUsuario->assign(array(
            'usuarioResposta'            => $objDesafio->resposta,
            'usuarioRespostaMotivo'      => $objDesafio->motivo,
            'usuarioRespostaDt'          => date('Y-m-d H:i:s')
        ));

        if (!$desafioUsuario->save()) {
            return array('status' => 'error', 'message'=> $this->_lang['MSG19']);
        }else{
            if($objDesafio->resposta == 'N'){
               return array('status' => 'ok', 'message'=> $this->_lang['MSG20']); 
            }else{
                return array('status' => 'ok', 'message'=> $this->_lang['MSG21']);
            }  
        }
    }
    
    public function desafioCumpridoUsuario(\stdClass $objDesafio){
        
        $desafioUsuario = $this->findFirst("id = ".$objDesafio->id);
        
        $desafioUsuario->assign(array(
            'envioAprovacaoDt'            => date('Y-m-d H:i:s')
        ));

        if (!$desafioUsuario->save()) {
            return array('status' => 'error', 'message'=> $this->_lang['MSG25']);
        }else{
            return array('status' => 'ok', 'message'=> $this->_lang['MSG22']);  
        }
    }
    
    public function aprovarReprovarDesafio(\stdClass $dados){

        $usuariosDesafio = $this->find("desafioId = {$dados->id} AND usuarioResposta = 'Y'")->toArray();
                $desafio = Desafio::build()->findFirst("id = {$dados->id}");
 
        foreach ($usuariosDesafio as $usuarioDesafio){
            $resultDesafio = $this->findFirst("id = {$usuarioDesafio['id']}");
            $resultDesafio->assign(array(
                'desafioCumprido'         => $dados->resposta
            ));
            
            if (!$resultDesafio->save()) {
                foreach ($resultDesafio->getMessages() as $mensagem) {
                  $message =  $mensagem;

                  return array('status' => 'error', 'message' => $message);
                  break;
                }
            }else{
                if($dados->resposta == DesafioUsuario::DESAFIO_APROVADO){
                    
                    $desafioPontuacao = DesafioPontuacao::build()->findFirst("empresaId = {$dados->empresaId} AND desafioTipoId = {$desafio->desafioTipoId} AND ativo = 'Y'");
                    
                    $objCredito = new \stdClass();
                    $objCredito->usuarioId = $usuarioDesafio['usuarioId'];
                    $objCredito->empresaId = $dados->empresaId;
                    $objCredito->pontuacao = $desafioPontuacao->pontuacao;
                    $objCredito->pontuacaoTipo = UsuarioPontuacaoCredito::PONTUACAO_DESAFIO_APROVADO;
                    
                    UsuarioPontuacaoCredito::creditarUsuario($objCredito);
                }
            }
        }
        
        $desafio->observacaoAnalise = $dados->observacao;
        $desafio->save();
        
        if($dados->resposta == 'N'){
            return array('status' => 'ok', 'message' => $this->_lang['MSG23']);
        }else{
            return array('status' => 'ok', 'message' => $this->_lang['MSG24']);
        }  
    }
}
