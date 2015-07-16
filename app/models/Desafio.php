<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\Desafio
 * Modelo de registro de desafios
 */
class Desafio extends Model
{
    const DELETED                   = 'N';
    const NOT_DELETED               = 'Y';
    const DESAFIO_TIPO_INDIVIDUAL   = 1;
    const DESAFIO_TIPO_EQUIPE       = 2;
    
    public static $_instance;
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $empresaId;
    
    /**
     * @var integer
     */
    public $usuarioId;
    
    /**
     * @var integer
     */
    public $tipo;
    
    /**
     * @var integer
     */
    public $usuarioResponsavelId;
    
    /**
     * @var string
     */
    public $desafio;
    
    /**
     * @var integer
     */
    public $desafioTipoId;
    
    /**
     * @var integer
     */
    public $premiacao;
    
    /**
     * @var integer
     */
    public $criacaoDt;
    
    /**
     * @var integer
     */
    public $inicioDt;
    
    /**
     * @var integer
     */
    public $fimDt;
   
    /**
     * @var integer
     */
    public $modificacaoDt;
        
    /**
     * @var integer
     */
    public $ativo;
    
    public static function build()
    {
        if( !isset( self::$_instance ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Antes de criar o desafio atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de cadastro
        $this->criacaoDt = time();

        // Seta status do desafio para ativo
        $this->ativo = Desafio::NOT_DELETED;
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'desafio',
          'message' => 'A descrição do desafio é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'desafioTipoId',
          'message' => 'A pontuação do desafio é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'inicioDt',
          'message' => 'A data de início do desafio é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'fimDt',
          'message' => 'A data de fim do desafio é obrigatória!!!'
        )));

        return $this->validationHasFailed() != true;
    }

    /**
     * Define a data e hora antes de atualizar o desafio
     */
    public function beforeValidationOnUpdate()
    {
        // Data de alteração
        $this->modificacaoDt = time();
    }

    public function initialize()
    {
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuarioCadastro',
            'reusable' => true
        ));
        
        $this->belongsTo('usuarioResponsavelId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuarioResponsavel',
            'reusable' => true
        ));
        
        $this->hasMany('id', 'Incentiv\Models\DesafioUsuario', 'desafioId', array(
            'alias' => 'desafioUsuario',
            'foreignKey' => array(
                'message' => 'O desafio não pode ser excluído porque ele possui usuarios.'
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Desafio::DELETED
            )
        ));
    }
    
    public function fetchAllDesafios(\stdClass $objDesafio) {
        
        $desafios = Desafio::query()->columns(
                         array( 'Incentiv\Models\Desafio.id',
                                'Incentiv\Models\Desafio.usuarioId',
                                'Incentiv\Models\Desafio.desafio',
                                'Incentiv\Models\Desafio.tipo',
                                'desafioTipoId', 
                                'inicioDt' => "DATE_FORMAT( Incentiv\Models\Desafio.inicioDt , '%d/%m/%Y' )",
                                'fimDt' => "DATE_FORMAT( Incentiv\Models\Desafio.fimDt , '%d/%m/%Y' )",
                                'Incentiv\Models\Desafio.premiacao',
                                'status' => 'Incentiv\Models\Desafio.ativo'));
        
        $desafios->innerjoin('Incentiv\Models\DesafioUsuario', 'Incentiv\Models\Desafio.id = DesafioUsuario.desafioId AND Incentiv\Models\Desafio.usuarioResponsavelId = DesafioUsuario.usuarioId', 'DesafioUsuario');
    
        $desafios->where("Incentiv\Models\Desafio.empresaId = {$objDesafio->empresaId}");
        
        if($objDesafio->perfilId == Perfil::GERENTE){
            $desafios->andwhere("Incentiv\Models\Desafio.usuarioId = {$objDesafio->usuarioId}");
        }
        
        $desafios->andwhere("DesafioUsuario.envioAprovacaoDt IS NULL");
        
        if($objDesafio->filter)
        {
           $desafios->andwhere( "Incentiv\Models\Desafio.desafio LIKE('%{$objDesafio->filter}%')");
        }
        if($objDesafio->ativo && $objDesafio->ativo != 'T' )
        {
            $desafios->andwhere("Incentiv\Models\Desafio.ativo = '{$objDesafio->ativo}'");
        }
        
        $desafios->orderBy('Incentiv\Models\Desafio.id desc');
        
        return $desafios->execute();
    }
    
    public function buscarDesafiosCumpridos(\stdClass $objDadosDesafioCumprido){
        $desafiosCumpridos = $this::query()->columns(
                         array( 
                                'Incentiv\Models\Desafio.id',
                                'Incentiv\Models\Desafio.tipo',
                                'Incentiv\Models\Desafio.desafio',
                                'desafioTipoId', 
                                'inicioDt' => "DATE_FORMAT( Incentiv\Models\Desafio.inicioDt , '%d/%m/%Y' )",
                                'fimDt' => "DATE_FORMAT( Incentiv\Models\Desafio.fimDt , '%d/%m/%Y' )",
                                'Incentiv\Models\Desafio.premiacao'));
        
        $desafiosCumpridos->innerjoin('Incentiv\Models\DesafioUsuario', 'Incentiv\Models\Desafio.id = DesafioUsuario.desafioId AND Incentiv\Models\Desafio.usuarioResponsavelId = DesafioUsuario.usuarioId', 'DesafioUsuario');
        $desafiosCumpridos->andwhere( "Incentiv\Models\Desafio.empresaId = {$objDadosDesafioCumprido->empresaId}");
        $desafiosCumpridos->andwhere( "Incentiv\Models\Desafio.ativo = 'Y'");
        $desafiosCumpridos->andwhere( "DesafioUsuario.envioAprovacaoDt IS NOT NULL");
        $desafiosCumpridos->andwhere( "DesafioUsuario.desafioCumprido IS NULL");

        return $desafiosCumpridos->execute()->toArray();
    }
    
    public function salvarDesafio($dados){

        //traz os serviços funcoes e db para o model
        $funcoes = $this->getDI()->getShared('funcoes');
        $db      = $this->getDI()->getShared('db');
        
        $db->begin();

        if($dados['id']){
           $desafio = $this->findFirst("id = ".$dados['id']);
           $desafio->desafioUsuario->delete();
        }else{
           $desafio = $this;
        }
        
        if($dados['tipo_desafio'] == Desafio::DESAFIO_TIPO_INDIVIDUAL){
            $dados['colaborador-responsavel'] = $dados['colaboradores-participantes'];
        }

        $desafio->assign(array(
            'usuarioId'             => $dados['usuarioId'],
            'empresaId'             => $dados['empresaId'],
            'desafio'               => $dados['desafio'],
            'tipo'                  => $dados['tipo_desafio'],
            'usuarioResponsavelId'  => $dados['colaborador-responsavel'],
            'desafioTipoId'     => (int) $dados['desafio_tipo_id'],
            'premiacao'     => $dados['premiacao'],
            'inicioDt'      => $funcoes->formatarData($dados['data_inicio']),
            'fimDt'         => $funcoes->formatarData($dados['data_fim'])
        ));
        
        $colaboradores = explode(',', $dados['colaboradores-participantes']);
        $desafioUsuario = array();
        
        //grava os usuarios participantes
        $usuarios_email = array();
        foreach ($colaboradores as $id){
            $desafioUsuario[$id]             = new DesafioUsuario();
            $desafioUsuario[$id]->usuarioId  = $id;
            $desafio->desafioUsuario         = $desafioUsuario;
            
            $usuario = Usuario::build()->findFirst("id = ".$id);
            $usuarios_email[$id]['email'] = $usuario->email;
            $usuarios_email[$id]['nome']  = $usuario->nome;
        }

        if (!$desafio->save()) {
            $db->rollback();
            return array('status' => 'error', 'message'=>'Não foi possível salvar o desafio!!!');
        }else{
            //envio dos emails para os usuários que participam do desafio criado.
            foreach ($usuarios_email as $usuario){
                $this->getDI()
                    ->getMail()
                    ->send($usuario['email'], "Novo Desafio", 'novo_desafio', array(
                    'nome'      => $usuario['nome'],  
                    'loginUrl' => '/session/login'
                ));
            } 
        }

        $db->commit();
        return array('status' => 'ok','message'=>'Desafio salvo com sucesso!!!');
        
    }
    
    public function ativarInativarDesafio(\stdClass $dados){

        $desafio = $this->findFirst("id = ".$dados->id);
        
        $desafio->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$desafio->save()) {
            foreach ($desafio->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => 'Desafio inativado com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Desafio ativado com sucesso!!!');
            }  
        }
    }
}