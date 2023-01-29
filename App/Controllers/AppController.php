<?php
namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;


class AppController extends Action{

    public function timeline(){
   

            $this->validarAutenticacao();

            $tweet = Container::getModel('Tweet');
            $tweet->__set('id_usuario' , $_SESSION['id']);

            $tweets = $tweet->getAll();
            $this->view->tweets = $tweets;

            $this->getInfoUser();

            $this->render('timeline');
            

    }

    public function tweet(){
        session_start();
        $this->validarAutenticacao();
            $tweet = Container::getModel('Tweet');

            $tweet->__set('tweet', $_POST['tweet']);
            $tweet->__set('id_usuario', $_SESSION['id']);

            $tweet->salvar();
            header('Location: /timeline');
    }

    public function validarAutenticacao(){
        session_start();
        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '' ){
            header('Location: /?login=erro');
        }
    }

    public function quemSeguir(){
        $this->validarAutenticacao();


        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';
        
            $usuario = Container::getModel('Usuarios');
            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios =$usuario->getAll();

        
        $this->view->usuarios = $usuarios;
        

        $this->getInfoUser();

        $this->render('quemSeguir');
    }

    public function acao(){
        $this->validarAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuarios');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir'){
            $usuario->seguirUsuario($id_usuario_seguindo);
        }
        if($acao == 'deixar_de_seguir'){
            $usuario->deixarSeguirUsuario($id_usuario_seguindo);
        }

        header('Location: \quemSeguir');

    }

    public function remover_tweet(){
        $this->validarAutenticacao();

        $id_tweet = isset($_GET['id']) ? $_GET['id'] : '';
        $id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : '';

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id', $id_tweet);

        $tweet->remover($id_usuario);
        header('Location: \timeline');
    }

    public function getInfoUser(){
            //informacoes do user
            $usuarios = Container::getModel('Usuarios');
            $usuarios->__set('id',$_SESSION['id']);

            //dados do user
            $this->view->info_usuario = $usuarios->getInfoUser();
            $this->view->total_tweets = $usuarios->getTotalTweets();
            $this->view->total_seguidores = $usuarios->getTotalSeguidores();
            $this->view->total_seguindo = $usuarios->getTotalSeguindo();

    }

}


?>