 <?php 
 //Importar la conexion 
 require 'includes/config/database.php';
 $db = conectarDB();

 //Crear un email & password
 $email = "juanrozop11@gmail.com";
 $password = "322400";

 $passwordHash = password_hash($password, PASSWORD_BCRYPT);

 //Query para crear el  usuario
 $query = "INSERT INTO usuarios (email, password) VALUES ('${email}', '${passwordHash}'); ";

 //Agregarlo a la base de datos
 mysqli_query($db, $query);