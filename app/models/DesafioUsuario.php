<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;
use Incentiv\Models\Desafio,
 Incentiv\Models\UsuarioPontuacaoCredito;

/**
 * DesafioUsuario
 * Este modelo registra os usuários que participam do desafio lançado
 */
class DesafioUsuario extends Model
{
     public static $_instance;
     
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
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario'
        ));
        $this->belongsTo('desafioId', 'Incentiv\Models\Desafio', 'id', array(
            'alias' => 'desafios'
        ));
    }
    
    public function buscarDesafiosUsuario(\stdClass $objDesafio){
        $desafios = $this::query()->columns(
                         array( 'Incentiv\Models\DesafioUsuario.id',
                                'Incentiv\Models\DesafioUsuario.usuarioResposta',
                                'd.desafio',
                                'd.pontuacao', 
                                'inicioDt' => "DATE_FORMAT( d.inicioDt , '%d/%m/%Y' )",
                                'fimDt' => "DATE_FORMAT( d.fimDt , '%d/%m/%Y' )",
                                'd.premiacao'));
        
        $desafios->innerjoin('Incentiv\Models\Desafio', 'Incentiv\Models\DesafioUsuario.desafioId = d.id', 'd');
        $desafios->andwhere( "Incentiv\Models\DesafioUsuario.usuarioId = {$objDesafio->usuarioId}");
        $desafios->andwhere( "Incentiv\Models\DesafioUsuario.usuarioResposta != 'N' OR Incentiv\Models\DesafioUsuario.usuarioResposta IS NULL");
        $desafios->andwhere( "Incentiv\Models\DesafioUsuario.envioAprovacaoDt IS NULL");
        $desafios->andwhere( "d.ativo = 'Y'");
        
        $desafios->orderBy('d.desafio');

        return $desafios->execute();
    }
    
    public function responderDesafioUsuario(\stdClass $objDesafio){
        
        $desafioUsuario = $this->findFirst("id = ".$objDesafio->id);
        
        $desafioUsuario->assign(array(
            'usuarioResposta'            => $objDesafio->resposta,
            'usuarioRespostaMotivo'      => $objDesafio->motivo,
            'usuarioRespostaDt'          => date('Y-m-d H:i:s')
        ));

        if (!$desafioUsuario->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível descartar o desafio, tente mais tarde!!!');
        }else{
            if($objDesafio->resposta == 'N'){
               return array('status' => 'ok', 'message'=>'Desafio descartado com sucesso!!!'); 
            }else{
                return array('status' => 'ok', 'message'=>'Desafio aceito com sucesso!!!');
            }  
        }
    }
    
    public function desafioCumpridoUsuario(\stdClass $objDesafio){
        
        $desafioUsuario = $this->findFirst("id = ".$objDesafio->id);
        
        $desafioUsuario->assign(array(
            'envioAprovacaoDt'            => date('Y-m-d H:i:s')
        ));

        if (!$desafioUsuario->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível enviar o desafio, tente mais tarde!!!');
        }else{
            return array('status' => 'ok', 'message'=>'Desafio enviado para aprovação com sucesso!!!');  
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
                    $objCredito = new \stdClass();
                    $objCredito->usuarioId = $usuarioDesafio['usuarioId'];
                    $objCredito->empresaId = $dados->empresaId;
                    $objCredito->pontuacao = $desafio->pontuacao;
                    $objCredito->pontuacaoTipo = UsuarioPontuacaoCredito::PONTUACAO_DESAFIO_APROVADO;
                    
                    UsuarioPontuacaoCredito::creditarUsuario($objCredito);
                }
            }
        }
        
        $desafio->observacaoAnalise = $dados->observacao;
        $desafio->save();
        
        if($dados->resposta == 'N'){
            return array('status' => 'ok', 'message' => 'Desafio reprovado com sucesso!!!');
        }else{
            return array('status' => 'ok', 'message' => 'Desafio aprovado com sucesso!!!');
        }  
    }
}
