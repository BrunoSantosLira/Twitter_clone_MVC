<?php

namespace App\Models;

use MF\Model\Model;

class Usuarios extends Model {
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $foto_perfil;

    public function __get($atributo){
        return $this->$atributo;
    }

    public function __set($atributo,$valor){
        $this->$atributo = $valor;
    }

     public function salvar(){
        $query = "INSERT INTO usuarios(nome,email,senha,foto_perfil)VALUES(:nome,:email,:senha,:foto_perfil)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('nome', $this->__get('nome'));
        $stmt->bindValue('email', $this->__get('email'));
        $stmt->bindValue('senha', md5($this->__get('senha')));
        $stmt->bindValue(':foto_perfil', $this->__get('foto_perfil'));
        $stmt->execute();
        return $this;
    }

    //validar se um cadastro pode ser feito
    public function validar(){
        $valido = true;

        //se o nome,senha ou email for menor que 3, validar recebe falso
        if(strlen($this->__get('nome')) < 3 ){
            $valido = false;
        }
        if(strlen($this->__get('email')) < 3 ){
            $valido = false;
        }
        if(strlen($this->__get('senha')) < 3 ){
            $valido = false;
        }
        return $valido;
    }

    //recuperar um usuario por email
    public function getusuarioporemail(){
        $query = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function autenticar(){
        $query = "SELECT id,nome,email FROM usuarios WHERE email= :email AND senha = :senha";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', md5($this->__get('senha')));

        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (isset($usuario['id']) && isset($usuario['nome'])) {
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
            } 

        return $this;

    }

    public function getAll(){
        $query = 
        "SELECT
         u.id,u.nome,u.email, u.foto_perfil,
         (
            SELECT
            count(*)
            FROM
            usuarios_seguidores as us
            WHERE
            us.id_usuario = :id_usuario AND us.id_usuario_seguindo = u.id
         ) AS seguindo_sn
        FROM 
        usuarios as u
        WHERE 
        nome 
        LIKE :nome AND id != :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function seguirUsuario($id_usuario_seguindo){
        $query = "INSERT INTO usuarios_seguidores(id_usuario, id_usuario_seguindo)VALUES(:id_usuario, :id_usuario_seguindo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();
        return true;
    }

    public function deixarSeguirUsuario($id_usuario_seguindo){
        $query = "DELETE FROM usuarios_seguidores WHERE id_usuario = :id_usuario AND id_usuario_seguindo = :id_usuario_seguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();
        return true;
    }
    
    //informações do usuario
    public function getInfoUser(){
        $query = "SELECT nome FROM usuarios WHERE id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    //selecionado e alterando imagem do usuario
    public function getImgUser(){
        
        $query = "SELECT foto_perfil FROM usuarios WHERE id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function editar_foto(){
        $query = "UPDATE usuarios SET foto_perfil = :foto_perfil WHERE id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('foto_perfil', $this->__get('foto_perfil'));
        $stmt->bindValue('id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }



    //Total de tweets do usuario
    public function getTotalTweets(){
        $query = "SELECT count(*) as total_tweets FROM tweets WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    //total seguindo
        public function getTotalSeguindo(){
        $query = "SELECT count(*) as total_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

      //total de seguidores
      public function getTotalSeguidores(){
        $query = "SELECT count(*) as total_seguindo FROM usuarios_seguidores WHERE id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }



}


?>