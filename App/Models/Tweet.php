<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model{
    private $id;
    private $id_usuario;
    private $data;
    private $tweet;
    private $path_img;

    public function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

    public function __get($atributo){
        return $this->$atributo;
    }

    //salvar
    public function salvar(){
        $query = "INSERT INTO tweets(id_usuario,tweet,path_imagem,curtidas)VALUES(:id_usuario, :tweet, :path_img, 0)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->bindValue(':path_img', $this->__get('path_img'));
        $stmt->execute();

        return $this;
    }

    public function remover($id_usuario){
        $query = "DELETE FROM tweets WHERE id = :id AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->bindValue(':id_usuario', $id_usuario);
        $stmt->execute();
    }

    //recuperar
    public function getAll(){
        $query = 
        "SELECT
        t.id, t.id_usuario, u.nome , u.foto_perfil , t.tweet, t.path_imagem, t.curtidas, DATE_FORMAT(t.data, '%d - %m -%Y- %H : %i') as data

        FROM 
        tweets as t
        LEFT JOIN usuarios as u on(t.id_usuario = u.id)

        WHERE
        id_usuario = :id_usuario
        OR t.id_usuario IN(SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)

        ORDER BY
        data desc";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPorPagina($limit,$offset){
        $query = 
        "SELECT
        t.id, t.id_usuario, u.nome , t.tweet, u.foto_perfil , t.path_imagem, t.curtidas, DATE_FORMAT(t.data, '%d - %m -%Y- %H : %i') as data

        FROM 
        tweets as t
        LEFT JOIN usuarios as u on(t.id_usuario = u.id)

        WHERE
        id_usuario = :id_usuario
        OR t.id_usuario IN(SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)

        ORDER BY
        data desc
        LIMIT
        $limit
        OFFSET
        $offset
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTotalRegistros(){
        $query = 
        "SELECT
            count(*) as total
        FROM 
        tweets as t
        LEFT JOIN usuarios as u on(t.id_usuario = u.id)

        WHERE
        id_usuario = :id_usuario
        OR t.id_usuario IN(SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function curtir_post(){
        $total_curtidas = $this->verificarCurtida();
        
        if($total_curtidas['total'] < 1){
            $this->adicionarCurtida();
            $query = "UPDATE tweets
                    SET curtidas = curtidas + 1
                    WHERE id= :id;";
    
            $stmt  = $this->db->prepare($query);
            $stmt->bindValue(':id',$this->__get('id'));
            $stmt->execute();
            
        }

    }

    public function adicionarCurtida(){
        $query = "INSERT INTO curtidas(id_usuario,id_tweet)VALUES(:id_usuario, :id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id',$this->__get('id'));
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();
    }

    public function verificarCurtida(){
        $query = 'SELECT COUNT(*) as total FROM curtidas WHERE id_tweet = :id AND id_usuario = :id_usuario;';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id',$this->__get('id'));
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function get_curtidas(){
        $query = 'SELECT curtidas FROM tweets WHERE id = :id_tweet';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_tweet', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}

?>