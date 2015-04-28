<?php
class UserController extends AbstractController
{
    public function usuaris($request) {
        if (strtolower($request->method) == 'get' && count($request->url_elements) == 1) {
            try {
                $model = new Conexion;
                $conexion = $model->conectar();
                $sql = "SELECT * FROM users";
                $consulta = $conexion->prepare($sql);
                $consulta->execute();
                $array = array();
                while ($fila = $consulta->fetch()) {
                    array_push($array,$fila['idusers']." ".$fila['name']." ".$fila['email']." ".$fila['pass']." ".$fila['rol']) ;
                }
                return $array;
            }catch(PDOException $e) {
                return $e->getMessage();
            }

        } else if ( count($request->url_elements) > 1) {

            $funcion = $request->url_elements;
            return $this->$funcion[1]($request);

        }  
        else {
            return "El formato de la url no es correcto.";
        }
    }

    public function crearUsuari($request) {
        if (strtolower($request->method) == 'post' && count($request->url_elements) == 2) {

            $model = new Conexion();
            $conexion = $model->conectar();
            
            $sql = "INSERT INTO users (name,email,pass,rol) VALUES(:nombre,:email,:password,:rol)";
            $consulta = $conexion->prepare($sql);
            $consulta->bindParam(':nombre', $request->parameters['nombre']);
            $consulta->bindParam(':email', $request->parameters['email']);
            $consulta->bindParam(':password', $request->parameters['password']);
            $consulta->bindParam(':rol', $request->parameters['rol']);
            if(!$consulta){
                return $conexion->errorInfo();
            }else{
                if (!$consulta->execute()) {
                    return $consulta->errorInfo();
                }else{
                    return "Inserción ejecutada con exito";   
                }
            }

        } else if (strtolower($request->method) != 'post') {
            return "Sólo se puede usar el método post";
        } else {
            return "El formato de la url no es correcto. xxx";
        }
    }

    public function login($request) {
        if (strtolower($request->method) == 'post' && count($request->url_elements) == 2) {
            
                $model = new Conexion;
                $conexion = $model->conectar();
                $sql = "SELECT * FROM users WHERE ";
                $sql.= "name=:usuario AND pass=:clave";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(":usuario",$request->parameters['nombre'],PDO::PARAM_STR);
                $consulta->bindParam(":clave",$request->parameters['password'],PDO::PARAM_STR);
                $consulta->execute();
                $total = $consulta->rowCount();
                if($total==0){
                    return "Error al iniciar sesión";
                }else{
                    $fila = $consulta->fetch();
                    return "Bienvenido ".$fila['name'];
                }

        } else if (strtolower($request->method) != 'post') {
            return "Sólo se puede usar el método post";
        } else {
            return "El formato de la url no es correcto.";;
        }
    }
    public function actualitzarNom($request) {
        if (strtolower($request->method) == 'put' && count($request->url_elements) == 3) {
                $model = new Conexion;
                $conexion = $model->conectar();
                $sql = "UPDATE users SET name=:usuario WHERE idusers=:idusuario";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(":usuario",$request->parameters['nombre'],PDO::PARAM_STR);
                $consulta->bindParam(":idusuario",$request->url_elements[2],PDO::PARAM_STR);
                $consulta->execute();
                $total = $consulta->rowCount();
                if($total==0){
                   return "Error al actualizar el usuario.";
                }else{
                    return "Usuario actualizado correctamente.";
                }

        } else if (strtolower($request->method) != 'put') {
            return "Sólo se puede usar el método put";
        } else {
            return "El formato de la url no es correcto.";
        }
    }

    public function esborrarUsuari($request) {
        if (strtolower($request->method) == 'delete' && count($request->url_elements) == 3) {
            
                $model = new Conexion;
                $conexion = $model->conectar();
                $sql = "DELETE FROM users WHERE idusers=:idusuario";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(":idusuario",$request->url_elements[2],PDO::PARAM_STR);
                $consulta->execute();
                $total = $consulta->rowCount();
                if($total==0){
                   return "Error al borrar el usuario.";
                }else{
                    return "Usuario borrado correctamente.";
                }

        } else if (strtolower($request->method) != 'delete') {
             return "Sólo se puede usar el método delete";
        } else {
            return "El formato de la url no es correcto.";
        }
    }
}
