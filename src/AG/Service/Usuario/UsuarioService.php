<?php
namespace AG\Service\Usuario;

use AG\Entity\Usuario\Usuario,
    AG\Utils\Validator\Usuario\UsuarioValidator;
use Doctrine\ORM\EntityManager;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UsuarioService
{
    private $usuarioValidator;
    private $em;
    private $usuario;

    public function __construct(Usuario $usuario, EntityManager $em, UsuarioValidator $usuarioValidator)
    {
        $this->usuario = $usuario;
        $this->em = $em;
        $this->usuarioValidator = $usuarioValidator;
    }

    public function insert(Request $request)
    {
        $this->usuario->setUsername($request->get('username'))
                      ->setEmail($request->get('email'))
                      ->setPassword($request->get('password'))
                      ->setRoles($request->get('role'));
        $isValid = $this->usuarioValidator->validate($this->usuario);
        if(true !== $isValid) {
            return $isValid;
        }

        $this->em->persist($this->usuario);
        $this->em->flush();

        return $this->usuario;
    }

    public function update(Request $request, $id)
    {
        $this->usuario = $this->em->getReference('AG\Entity\Usuario\Usuario', $id);

        $this->usuario->setUsername($request->get('username'))
            ->setEmail($request->get('email'))
            ->setPassword($request->get('password'))
            ->setRoles($request->get('role'));
        
        $isValid = $this->usuarioValidator->validate($this->usuario);
        if(true !== $isValid) {
            return $isValid;
        }

        $this->em->persist($this->usuario);
        $this->em->flush();

        return $this->usuario;
    }

    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        // busco pelo username
        $this->usuario = $repository->findOneByUsername($username);

        if ($this->usuario){
            // verifico se a senha do Request é igual do usuario
            if (password_verify($password, $this->usuario->getPassword())) {
                return $this->getData($this->usuario);
            }
        } else {
            return false;
        }
    }

    public function forgot(Request $request, Application $app)
    {
        $email = $request->get('email');
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        $this->usuario = $repository->findOneByEmail($email);

        if($this->usuario){
            //1 – Definimos Para quem vai ser enviado o email
            $username = $this->usuario->getUsername();
            $assunto = 'Redefinir Senha AG';

            $path = $app['asset.host'].'ag/user/reset_password/'.$this->usuario->getSalt();

            $mensagem = 'Você pode redefinir sua senha de acesso AG clicando no link abaixo:<br><br>';
            $mensagem .= '<a href="'.$path.'" target="_blank">'.$path.'</a><br><br>';
            $mensagem .= 'Se você não solicitou essa redefinição de senha, você pode ignora-la.<br><br>';
            $mensagem .= 'Atenciosamente,<br>AG Team.';

            //2 - resgatar o nome digitado no formulário e  grava na variavel $nome
            // 3 - resgatar o assunto digitado no formulário e  grava na variavel //$assunto
            //4 – Agora definimos a  mensagem que vai ser enviado no e-mail
            $msg_email = "<strong>".$username.",</strong>";
            $msg_email .= "<br/>".$mensagem;

            //5 – agora inserimos as codificações corretas e  tudo mais.
            $headers =  "Content-Type:text/html; charset=UTF-8\n";
            $headers .= "From: ".$username." <$email> Reply-to: $email \n"; //Vai ser //mostrado que  o email partiu deste email e seguido do nome
            /* $headers .= "X-Sender:  <sistema@dominio.com.br>\n"; //email do servidor //que enviou
             $headers .= "X-Mailer: PHP  v".phpversion()."\n";
             $headers .= "X-IP:  ".$_SERVER['REMOTE_ADDR']."\n";
             $headers .= "Return-Path:  <sistema@dominio.com.br>\n"; //caso a msg //seja respondida vai para  este email. */
            $headers .= "MIME-Version: 1.0\n";

            $envio = mail($email, $assunto, $msg_email, $headers);  //função que faz o envio do emai
            if ($envio){
                return [
                    'warning' => 'Lhe enviamos um email com as instruções para redefinir sua senha.',
                    'error' => null
                ];
            } else {
                return [
                    'warning' => null,
                    'error' => 'Ocorreu um erro ao tentar lhe enviar um email com as instruços para redefinir sua senha. tente novamente mais tarde.'
                ];
            }
        } else {
            return [
                'error' => 'O email informado não foi encontrado. Certifique-se de ter digitado corretamente seu email.',
                'warning' => null
            ];
        }
    }

    public function resetPassword(Request $request)
    {
        $salt = $request->get('salt');
        $password = $request->get('password');
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');
        $this->usuario = $repository->findOneBySalt($salt);
        if($this->usuario){
            $this->usuario->setPassword($password);
            $this->em->persist($this->usuario);
            $this->em->flush();
            return [
                'error' => null,
                'warning' => 'Sua senha foi redefinida com sucesso! ',
                'salt' => null
            ];
        } else {
            return [
                'error' => 'Chave de autenticação inválida.',
                'warning' => null,
                'salt' => $salt
            ];
        }
    }

    public function delete($id)
    {
        $this->usuario = $this->em->getReference('AG\Entity\Usuario\Usuario', $id);

        $this->em->remove($this->usuario);
        $this->em->flush();

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        $this->usuario = $repository->find($id);

        return $this->getData($this->usuario);
    }

    private function getData(Usuario $usuario)
    {
        $arrayUsuario = array();

        $arrayUsuario['id'] = $usuario->getId();
        $arrayUsuario['username'] = $usuario->getUsername();
        $arrayUsuario['password'] = $usuario->getPassword();
        $arrayUsuario['email'] = $usuario->getEmail();
        $arrayUsuario['roles'] = $usuario->getRoles();

        return $arrayUsuario;
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        return $this->toArray($repository->findAll());
    }

    public function toArray(array $usuarios)
    {
        $arrayUsuarios = array();
        foreach($usuarios as $key => $usuario)
        {
            $arrayUsuarios[$key]['id'] = $usuario->getId();
            $arrayUsuarios[$key]['username'] = $usuario->getUsername();
            $arrayUsuarios[$key]['password'] = $usuario->getPassword();
            $arrayUsuarios[$key]['email'] = $usuario->getEmail();
            $arrayUsuarios[$key]['roles'] = $usuario->getRoles();
        }
        return $arrayUsuarios;
    }

//    public function buscarUsuario($nome)
//    {
//        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');
//        return $repository->getBuscarUsuarios($nome);
//    }
//
//    public function fetchPagination($offset, $limit)
//    {
//        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');
//        return $repository->fetchPagination($offset, $limit);
//    }
}