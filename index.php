<?php
/* CONEXION BBDD*/
    $servidor = 'localhost'; // 127.0.0.1
    $usuario = 'root';
    $password = '';
    try {
        /*  mysql:host=localhost;port=3307;dbname=testdb */
        $conexion =new PDO("mysql:host=localhost;port=3307;dbname=album",$usuario,$password); //pdo clase que me permite conectar a la base de datos
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //caracteristicas propias de la conexion
        
        $sql="SELECT * FROM `fotos`;";
        $sentencia=$conexion->prepare($sql);//exec metodo prpopio de PDO
        $sentencia->execute();
        $resultado=$sentencia->fetchAll();

        // print_r($resultado) ;
        foreach($resultado as $foto){
            print_r($foto['nombre'].'<br>');   
        }

    } catch (PDOException $error) {
        echo 'conexion erronea'.$error;
    }


    /* CRUD */

    switch($accion){

      case 'agregar':
        $sentenciaSQL = $conexion->prepare("INSERT INTO productos (nombre,imagen) VALUES (:nombre, :imagen);");
        $sentenciaSQL->bindParam(':nombre',$txtnombre);
        $fecha = new DateTime();
        $nombreArchivo = ($txtimagen != '') ? $fecha->getTimestamp().'_'.$_FILES['txtimagen']['name']:'persona.png' ;
        $tmpimagen=$_FILES['txtimagen']['tmp_name']; //aqui desde $txtimagen, accedo a la propiedad ya preestabecidas por PHP llamada ['tmp_name']
        if($tmpimagen != ''){
          move_uploaded_file($tmpimagen,"../../img/".$nombreArchivo);
        }
    
        $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
        $sentenciaSQL->execute();    
    
        header('location:products.php');
        break;
      case 'modificar':
        $sentenciaSQL = $conexion->prepare("UPDATE productos SET nombre=:nombre  WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtid);
        $sentenciaSQL->bindParam(':nombre',$txtnombre);
        $sentenciaSQL->execute();
    
        if($txtimagen !=''){ // En caso a no modificar la imagen, no se utiliza el campo imgen, esto se lo hace para controlar.
          $fecha = new DateTime();
          $nombreArchivo = ($txtimagen != '') ? $fecha->getTimestamp().'_'.$_FILES['txtimagen']['name']:'persona.png' ;
          $tmpimagen = $_FILES['txtimagen']['tmp_name'];
          move_uploaded_file($tmpimagen,"../../img/".$nombreArchivo);
          
          $sentenciaSQL = $conexion->prepare("SELECT imagen FROM productos WHERE id=:id");
          $sentenciaSQL->bindParam(':id',$txtid);
          $sentenciaSQL->execute(); //(retorna 1). SI O SI EN ESTE LUGAR. 3ER LUGAR, sino No funca
          $libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);//recupera un registro para almacenarlo en $libro y luego mostrarlo.
    
          if(isset($libro['imagen']) && ($libro['imagen'] != ['persona.png'])){
            if(file_exists("../../img/".$libro['imagen'])){
              unlink("../../img/".$libro['imagen']) ;
            }
          }
          
          $sentenciaSQL = $conexion->prepare("UPDATE productos SET imagen=:imagen  WHERE id=:id");
          $sentenciaSQL->bindParam(':id',$txtid);
          $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
          $sentenciaSQL->execute();
    
        }
        header('location:products.php');
        break;
        
      case 'cancelar':
        header('location:products.php');
        echo 'diste en cancel';
        break;
      case 'seleccionar':
        $sentenciaSQL = $conexion->prepare("SELECT * FROM productos WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtid);
        $sentenciaSQL->execute();
        $producId = $sentenciaSQL->fetch(PDO::FETCH_LAZY);//recupera un registro para almacenarlo en $productoId y luego mostrarlo.
        $txtnombre = $producId['nombre'];
        $txtimagen = $producId['imagen'];
    
        break;
      case 'borrar':
        
        $sentenciaSQL = $conexion->prepare("SELECT imagen FROM productos WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtid);
        $sentenciaSQL->execute(); //(retorna 1). SI O SI EN ESTE LUGAR. 3ER LUGAR, sino No funca
        $libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);//recupera un registro para almacenarlo en $libro y luego mostrarlo.
        
        if(isset($libro['imagen']) && ($libro['imagen'] != ['persona.png'])){
          if(file_exists("../../img/".$libro['imagen'])){
            unlink("../../img/".$libro['imagen']) ;
          }
        }
        
        $sentenciaSQL = $conexion->prepare("DELETE FROM productos WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtid);
        $sentenciaSQL->execute();
    
        header('location:products.php');
        break;
    }
    
    json_encode($array_a_json);
    json_decode($deJSON_a_array);

    $domicilio = 'los charruas 3212';
    echo isset($domicilio);// True

?>