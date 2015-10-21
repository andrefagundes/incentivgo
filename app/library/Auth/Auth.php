<?php
namespace Incentiv\Auth;

use Phalcon\Mvc\User\Component;

use Incentiv\Models\Usuario,
    Incentiv\Models\LembrarTokens,
    Incentiv\Models\SucessoLogin,
    Incentiv\Models\FalhaLogin;

/**
 * Incentiv\Auth\Auth
 * Gerencia Gestão de autenticação/identidade em Incentiv
 */
class Auth extends Component
{

    /**
     * Verifica as credenciais do usuário
     *
     * @param array $credentials
     * @return boolan
     */
    public function check($credentials)
    {
        $lang = $this->getDI()->getShared('lang');

        // Verifique se o usuário existe
        $user = Usuario::findFirstByEmail($credentials['email']);
        if ($user == false) {
            $this->registerUserThrottling(0);
            throw new Exception($lang['email_senha_invalidos']);
        }

        // Verifica a senha
        if (!$this->security->checkHash($credentials['senha'], $user->senha)) {
            $this->registerUserThrottling($user->id);
            throw new Exception($lang['email_senha_invalidos']);
        }

        // Verifique se o usuário está ativo
        $this->checkUserFlags($user);
        
        // Registra o login bem-sucedido
        $this->saveSuccessLogin($user);

        // Verifique se o se "lembra de mim" foi selecionado
        if (isset($credentials['remember'])) {
            $this->createRememberEnviroment($user);
        }

        $this->session->set('auth-identity', array(
            'id'        => $user->id,
            'avatar'    => $user->avatar,
            'nome'      => $user->nome,
            'perfilId'  => $user->perfilId,
            'perfil'    => $user->perfil->nome,
            'empresaId' => $user->empresaId
        ));
    }

    /**
     * Registra o sucesso do login do usuário
     *
     * @param Incentiv\Models\Usuario $user
     */
    public function saveSuccessLogin($user)
    {
        $sucessoLogin               = new SucessoLogin();
        $sucessoLogin->usuarioId    = $user->id;
        $sucessoLogin->ipAddress    = $this->request->getClientAddress();
        $sucessoLogin->userAgent    = $this->request->getUserAgent();
        if (!$sucessoLogin->save()) {
            $messages = $sucessoLogin->getMessages();
            throw new Exception($messages[0]);
        }
    }

    /**
     * Implementa o login estrangulamento 
     * Reduz a efetividade de ataques de força bruta
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin            = new FalhaLogin();
        $failedLogin->usuarioId   = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->tentativa = time();
        $failedLogin->save();

        $attempts = FalhaLogin::count(array(
            'ipAddress = ?0 AND tentativa >= ?1',
            'bind' => array(
                $this->request->getClientAddress(),
                time() - 3600 * 6
            )
        ));

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }

    /**
     * Cria o ambiente (lembra de mim) configurações os cookies relacionados e gera fichas
     *
     * @param Incentiv\Models\Usuario $user
     */
    public function createRememberEnviroment(Usuario $user)
    {
        $userAgent  = $this->request->getUserAgent();
        $token      = md5($user->email . $user->senha . $userAgent);

        $remember               = new LembrarTokens();
        $remember->usuarioId      = $user->id;
        $remember->token        = $token;
        $remember->userAgent    = $userAgent;

        if ($remember->save() != false) {
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, $expire);
            $this->cookies->set('RMT', $token, $expire);
        }
    }

    /**
     * Verifique se a sessão tem um cookie se lembrar de mim
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }

    /**
     * Logs sobre o uso da informação nos coookies
     *
     * @return Phalcon\Http\Response
     */
    public function loginWithRememberMe()
    {
        $userId         = $this->cookies->get('RMU')->getValue();
        $cookieToken    = $this->cookies->get('RMT')->getValue();

        $user = Usuario::findFirstById($userId);
        if ($user) {

            $userAgent  = $this->request->getUserAgent();
            $token      = md5($user->email . $user->senha . $userAgent);

            if ($cookieToken == $token) {

                $remember = LembrarTokens::findFirst(array(
                    'usuarioId = ?0 AND token = ?1',
                    'bind' => array(
                        $user->id,
                        $token
                    )
                ));
                if ($remember) {

                    // Verifica se o cookie não expirou
                    if ((time() - (86400 * 8)) < $remember->criacaoDt) {

                        // Verifique se o usuário foi marcado
                        $this->checkUserFlags($user);

                        // Registra identidade
                        $this->session->set('auth-identity', array(
                            'id'        => $user->id,
                            'avatar'    => $user->avatar,
                            'nome'      => $user->nome,
                            'perfilId'  => $user->perfilId,
                            'perfil'    => $user->perfil->nome,
                            'empresaId' => $user->empresaId
                        ));

                        // Registra o login bem-sucedido
                        $this->saveSuccessLogin($user);

                        return $this->response->redirect('usuario');
                    }
                }
            }
        }

        $this->cookies->get('RMU')->delete();
        $this->cookies->get('RMT')->delete();

        return $this->response->redirect('session/login');
    }

    /**
     * Verifica se o usuário foi banido / inativo / suspenso
     *
     * @param Incentiv\Models\Usuario $user
     */
    public function checkUserFlags(Usuario $user)
    {
        if ($user->ativo != 'Y') {
            throw new Exception('O usuário está inativo');
        }
    }

    /**
     * Retorna a identidade atual
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->session->get('auth-identity');
    }

    /**
     * Retorna o nome da identidade atual
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-identity');
        $usuario_logado = explode(' ', $identity['nome']);
        return $usuario_logado[0].' '.$usuario_logado[1];
    }

    /**
     * Remove as informações de identidade do usuário da sessão
     */
    public function remove()
    {
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }

        $this->session->remove('auth-identity');
    }

    /**
     * Retorno o usuário por seu ID
     *
     * @param int $id
     */
    public function authUserById($id)
    {
        $user = Usuario::findFirstById($id);
        if ($user == false) {
            throw new Exception('O usuário não existe');
        }

        $this->checkUserFlags($user);

        $this->session->set('auth-identity', array(
            'id'        => $user->id,
            'avatar'    => $user->avatar,
            'nome'      => $user->nome,
            'perfilId'  => $user->perfilId,
            'perfil'    => $user->perfil->nome,
            'empresaId' => $user->empresaId
        ));
    }

    /**
     * Retorna a entidade relacionada ao usuário na identidade ativa
     *
     * @return \Incentiv\Models\Usuario
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id'])) {

            $user = Usuario::findFirstById($identity['id']);
            if ($user == false) {
                throw new Exception('O usuário não existe');
            }

            return $user;
        }

        return false;
    }
}