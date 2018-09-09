<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', 'password', 'curso_angular4');

//Configuracion de cabeceras de HTTP
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

$app->get("/pruebas", function() use($app, $db){
  echo "Hola mundo desde slim";
});

$app->get("/probando", function() use($app){
  echo "Otro texto";
});

//LISTAR TODOS LOS PRODUCTOS
$app->get('/productos', function() use($app, $db){ //Crear funcion
  $sql = 'SELECT * FROM productos ORDER BY id DESC;'; //Hacer la query
  $query = $db->query($sql); //Hacer la query en la bse de datos

  $productos = array(); //Crear variable del array de productos
  while ($producto = $query->fetch_assoc()) { //Sacar producto por producto con fetch_assoc
    $productos[] = $producto; //Ir metiendo la variable de producto dentro del array de productos
  }

  // Comprobacion si el array es NULL y regresar algo
  $result = array(
    'status' => 'error',
    'code'   => 404,
    'message'=> 'Producto no solicitado correctamente'
  );

  // Comprobacion si el array esta bien
  if($query){
    $result = array(
      'status' => 'success',
      'code'   => 200,
      'message'=> $productos //regresar el array lleno de productos dentro de un json
    );
  }

  echo json_encode($result); //Regresar el json
});

//DEVOLVER UN SOLO PRODUCTO
$app->get('/productos/:id', function($id) use($app, $db){
  $sql = 'SELECT * FROM productos WHERE id = '.$id; //Regresar un
  $query = $db->query($sql);

  $result = array(
    'status' => 'error',
    'code'   => 404,
    'message'=> 'Producto no disponible'
  );

  if($query->num_rows == 1){
    $producto = $query->fetch_assoc();
    $result = array(
      'status' => 'success',
      'code'   => 200,
      'data'=> $producto
    );}

  echo json_encode($result);
});

//ELIMINAR UN PRODUCTO
$app->get('/delete-producto/:id', function($id) use($app, $db){
  $sql = 'DELETE FROM productos WHERE id = '.$id; //Regresar un
  $query = $db->query($sql);

  if($query){
    $result = array(
      'status' => 'success',
      'code'   => 200,
      'message'=> 'El producto se ha eliminado correctamente'
    );
  } else {
      $result = array(
        'status'  => 'error',
        'code'    => 404,
        'message' => 'El producto no se pudo eliminar'
      );
    }

  echo json_encode($result);
});

//ACTUALIZAR UN PRODUCTO
$app->post('/update-producto/:id', function($id) use($app, $db){
  $json = $app->request->post('json');
  $data = json_decode($json, true);

  $sql = "UPDATE productos SET ".
         "nombre = '{$data["nombre"]}', ".
         "descripcion = '{$data["descripcion"]}', ";

  if (isset($data['imagen'])) {
    $sql .= "imagen = '{$data["imagen"]}', ";
  }

  $sql .= "precio = '{$data["precio"]}' WHERE id = {$id}";

  $query = $db->query($sql);

  if($db->affected_rows>0){
    $result = array(
      'status' => 'success',
      'code'   => 200,
      'message'=> 'El producto se ha actualizado correctamente'
    );
  } else {
      $result = array(
        'status'  => 'error',
        'code'    => 404,
        'message' => 'El producto no se ha actualizado correctamente'
      );
    }

   echo json_encode($result);
});

//SUBIR UNA IMAGEN A UN PRODUCTO
$app->post('/upload-file', function() use($db, $app){
  $result = array(
    'status'  => 'error',
    'code'    => 404,
    'message' => 'El archivo no ha podido subirse'
  );

  if (isset($_FILES['uploads'])) {
    $piramideUploader = new PiramideUploader();

    $upload = $piramideUploader->upload('image', "uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));
    $file = $piramideUploader->getInfoFile();
    $file_name = $file['complete_name'];

    if (isset($upload) && $upload["uploaded"] == false) {
      $result = array(
        'status'  => 'error',
        'code'    => 404,
        'message' => 'El archivo no ha podido subirse'
      );
    } else {
      $result = array(
        'status'  => 'success',
        'code'    => 200,
        'message' => 'El archivo ha podido subirse',
        'filename'=> $file_name
      );
    }
  }

  echo json_encode($result);
});

//GUARDAR PRODUCTO
$app->post('/productos', function() use($app, $db){
  $json = $app->request->post('json');
  $data = json_decode($json, true);

  if(!isset($data['nombre'])){
    $data['nombre']=null;
  }

  if(!isset($data['descripcion'])){
    $data['descripcion']=null;
  }

  if(!isset($data['precio'])){
    $data['precio']=null;
  }

  if(!isset($data['imagen'])){
    $data['imagen']=null;
  }

  $query = "INSERT INTO productos VALUES(NULL,".
           "'{$data['nombre']}',".
           "'{$data['descripcion']}',".
           "'{$data['precio']}',".
           "'{$data['imagen']}'".
           ");";

   $insert = $db->query($query);

   $result = array(
     'status' => 'error',
     'code'   => 404,
     'message'=> 'Producto no se ha creado correctamente'
   );

   if($insert){
     $result = array(
       'status' => 'success',
       'code'   => 200,
       'message'=> 'Producto creado correctamente'
     );
   }

   echo json_encode($result);
});

$app->run();
