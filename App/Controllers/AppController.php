<?php
namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;


class AppController extends Action
{

    public function timeline()
    {

        $this->validarAutenticacao();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweets = $tweet->getAll();

        $total_registros_pagina = 10;
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $deslocamento = ($pagina - 1) * $total_registros_pagina; //expressão para calcular o deslocamento


        /*
        $total_registros_pagina = 10;
        $deslocamento = 10;
        $pagina = 2;
        
        $total_registros_pagina = 10;
        $deslocamento = 20;
        $pagina = 3;*/

        $tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);
        $total_tweets = $tweet->getTotalRegistros();
        $this->view->total_paginas = ceil($total_tweets['total'] / $total_registros_pagina);
        $this->view->pagina_ativa = $pagina;



        $this->view->tweets = $tweets;

        $this->getInfoUser();

        $this->render('timeline');


    }

    public function tweet()
    {
      
        $this->validarAutenticacao();
        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_GET['tweet']);
        $tweet->__set('id_usuario', $_GET['id']);


        $arquivo = $_FILES['arquivo2'];

        //salvando imagens
        echo '<pre>';
        print_r($arquivo);
        echo '</pre>';

        
        $pasta = "img/";
        $arquivo_nome = $arquivo['name'];
        $novoNomeArquivo = uniqid();
        $extensao = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION)); //formato do arquivo(.jpg,.pdf,.png ...)

        if ($extensao != 'png' && $extensao != 'jpg' && $arquivo['name'] != '' && $extensao != 'jpeg') {
            header('Location: /timeline?img_erro=formatoInvalido');

        } else if ($arquivo['size'] > 2097157 && $arquivo['name'] != '') {
            header('Location: /timeline?img_erro=tamanhoMaximoUltrapassado');
        } else {

            $caminho = "$pasta$novoNomeArquivo.$extensao";

            $deu_certo = move_uploaded_file($arquivo['tmp_name'], $pasta . $novoNomeArquivo . '.' . $extensao);
            if ($deu_certo) {
                $tweet->__set('path_img', $caminho);
            }

            //header("Location: /img/img.png"); endereço da imagem

            $tweet->salvar();
            $this->timeline();
            
        }
    }

    public function validarAutenticacao()
    {
        session_start();
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
        }
    }

    public function quemSeguir()
    {
        $this->validarAutenticacao();


        
        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuario = Container::getModel('Usuarios');
        $usuario->__set('nome', $pesquisarPor);
        $usuario->__set('id', $_SESSION['id']);
        $usuarios = $usuario->getAll();


        $this->view->usuarios = $usuarios;
       
        print_r(json_encode($usuarios));
        $this->getInfoUser();

        //$this->render('quemSeguir');*/
    }

    public function acao()
    {
        $this->validarAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

       
     
      
        $usuario = Container::getModel('Usuarios');
        $usuario->__set('id', $_SESSION['id']);

        if ($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario_seguindo);
        }
        if ($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($id_usuario_seguindo);
        }
        echo json_encode('oiii');
        //header('Location: \quemSeguir');

    }

    public function remover_tweet()
    {
        $this->validarAutenticacao();
        echo json_encode($_GET['id']);

        
        $id_tweet = isset($_GET['id']) ? $_GET['id'] : '';
        $id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : '';

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id', $id_tweet);

        $tweet->remover($id_usuario);
        //header('Location: \timeline');
    }

    public function getInfoUser()
    {
        //informacoes do user
        $usuarios = Container::getModel('Usuarios');
        $usuarios->__set('id', $_SESSION['id']);

        //dados do user
        
        $this->view->info_usuario = $usuarios->getInfoUser();
        $this->view->total_tweets = $usuarios->getTotalTweets();
        $this->view->total_seguidores = $usuarios->getTotalSeguidores();
        $this->view->total_seguindo = $usuarios->getTotalSeguindo();
        $this->view->foto_perfil = $usuarios->getImgUser();

    }


    public function getInfoUserAJAX(){
        $usuarios = Container::getModel('Usuarios');
        $usuarios->__set('id', $_GET['id']);

        $listaINFO = array(
            $usuarios->getTotalTweets(),
            $usuarios->getTotalSeguindo(),
            $usuarios->getTotalSeguidores()
            
        );

        print_r( json_encode($listaINFO));
        
  

    }

    public function foto_edit()
    {
        header("Content-Type: application/json");
        $usuarios = Container::getModel('Usuarios');
        $usuarios->__set('id', $_GET['id']);
        
 
        $arquivo = $_FILES['arquivo'];
        //salvando imagens
       
        
        $pasta = "img/";
        $arquivo_nome = $arquivo['name'];
        $novoNomeArquivo = uniqid();
        $extensao = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION)); //formato do arquivo(.jpg,.pdf,.png ...)

        if ($extensao != 'png' && $extensao != 'jpg' && $arquivo['name'] != '' && $extensao != 'jpeg') {
            header('Location: /timeline?img_erro=formatoInvalido');

        } else if ($arquivo['size'] > 2097157 && $arquivo['name'] != '') {
            header('Location: /timeline?img_erro=tamanhoMaximoUltrapassado');
        } else {

            $caminho = "$pasta$novoNomeArquivo.$extensao";

            $deu_certo = move_uploaded_file($arquivo['tmp_name'], $pasta . $novoNomeArquivo . '.' . $extensao);
            if($deu_certo){
                $usuarios->__set('foto_perfil', $caminho);
            }else{
				$usuarios->__set('foto_perfil', "user_img/user_empty.jpg");
			}

            $usuarios->editar_foto();
            $this->view->foto_perfil = $usuarios->getImgUser();
            print_r(json_encode($this->view->foto_perfil));
            //header('Location: \timeline');

        }

    }

    public function getTweets(){
        $this->validarAutenticacao();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweets = $tweet->getAll();

        $total_registros_pagina = 10;
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $deslocamento = ($pagina - 1) * $total_registros_pagina; //expressão para calcular o deslocamento


        /*
        $total_registros_pagina = 10;
        $deslocamento = 10;
        $pagina = 2;
        
        $total_registros_pagina = 10;
        $deslocamento = 20;
        $pagina = 3;*/

        $tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);
        $total_tweets = $tweet->getTotalRegistros();
        $this->view->total_paginas = ceil($total_tweets['total'] / $total_registros_pagina);
        $this->view->pagina_ativa = $pagina;



        $this->view->tweets = $tweets;
        print_r(json_encode($this->view->tweets));
    
        $this->getInfoUser();  
    }

    public function curtir_post(){
        $tweet = Container::getModel('Tweet');

        $tweet->__set('id', $_GET['id_post']);
        $tweet->__set('id_usuario', $_GET['id_usuario']);

        $tweet->curtir_post();
        echo json_encode('oii');
    }

    


}


?>