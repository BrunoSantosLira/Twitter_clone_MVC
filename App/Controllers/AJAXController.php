<?php
namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;


class AJAXController extends Action{
    public function foto_edit(){
        
        if($_FILES){

            
            $arquivo = $_FILES['arquivo'];
            if(move_uploaded_file($arquivo['tmp_name'], $arquivo['name'])){
                echo 'Arquivo enviado com sucesso!!';
            }else{
                echo 'falha ao enviar!!!';
            }
        }

    }
    
}

?>