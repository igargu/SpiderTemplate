<?php

    // Control de errores
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    
    require_once('settingsDB.php');
    require_once('DBConnection.php');
    
    // Conexión a la BBDD
    $dbc = new DBConnection($dbsettings);
    
    // URL de la ficha del usuario
    $url = '';
    
    getUser($dbc);
    
    /**
     * Obtener la ficha del usuario
     */
    function getUser($dbc) {
        $archivo = file_get_contents($url);
            if ($archivo === false) {
                echo "Error: file_get_contents no ha podido acceder a la URL<br>";
                exit(1);
            }
        
        // Abrimos el fichero de escritura
        $file = "";
        $fp = fopen($file,"w");
        fwrite($fp,$archivo);
        
        getAd($archivo,$dbc);
    }
    
    /**
     * Obtener las url de las fichas de un usuario mediante un bucle 
     */
    function getAd($archivo,$dbc) {
        
        $findme   = '"@type": "Product",';
        $extra = strlen($findme);
        $posInicio = strpos($archivo, $findme) + $extra;
        
        $numAd = substr_count($archivo,$findme);
    
        for ($i = 0; $i < $numAd; $i++) {
            
            $findme   = '"@type": "Product",';
            $extra = strlen($findme);
            $posInicio = strpos($archivo, $findme) + $extra;
            
            $findme   = '"name":';
            $posFinal = strpos($archivo, $findme);
            
            $urlAd = substr($archivo, $posInicio, $posFinal-$posInicio);
            $urlAd = str_replace('"url": "','',$urlAd);
            $urlAd = str_replace('",','',$urlAd);
            
            writeAd(trim($urlAd),$dbc);
            
            $findme   = '"description"';
            $posFinal = strpos($archivo, $findme);
            $archivo = substr($archivo, $posFinal+1);
            
        }
    }
    
    /**
     * Obtener los datos a partir de la url de la web
     */
    function writeAd($ad,$dbc) {
        $archivo = file_get_contents($ad);
            if ($archivo === false) {
                echo "Error: file_get_contents no ha podido acceder a la URL<br>";
                exit(1);
            }
        
        // Abrimos el fichero de escritura
        $file = "";
        $fp = fopen($file,"w");
        fwrite($fp,$archivo);
        
        // Cadenas delimitadoras para los datos
        $iniStrTitulo = '';
        $finStrTitulo = '';
        
        $titulo = getTitle($archivo, $iniStrTitulo, $finStrTitulo);
        
        insertAd($dbc,$titulo);
        
    }
    
    // titulo --------------
    function getTitle($archivo, $iniStrTitulo, $finStrTitulo) {
        $findme   = $iniStrTitulo;
        $extra = strlen($findme);
        $posInicio = strpos($archivo, $findme) + $extra;
        
        $findme   = $finStrTitulo;
        $posFinal = strpos($archivo, $findme);
    
        $titulo = substr($archivo, $posInicio, $posFinal-$posInicio);
        
        echo 'Titulo: '.$titulo;
        echo "<br/><br/>";
        
        return $titulo;
    }
    
    /**
     * Borramos el contenido de la BD e insertamos los nuevos datos
     */
    function insertAd($dbc,$titulo) {
        
        $sql = "DELETE FROM x";
        
        $afectedRows = $dbc -> runQuery($sql);
        
        $sql = "INSERT INTO x VALUES ( '".$titulo."' );";
        
        echo $sql;
        var_dump($totalImg);
        exit;
        
        $afectedRows = $dbc -> runQuery($sql);
    }
    
?>