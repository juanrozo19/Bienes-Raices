<?php

function conectarDB() : mysqli{
    $db = mysqli_connect(
        'localhost:3305',
        'root',
        '3224',
        'bienes_raicesdb'
    );

    if(!$db){
        echo "Error no se pudo conectar";
        exit;
    }

    return $db;
}