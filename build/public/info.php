
<?php

/** Zend_Application */
//require_once 'Zend/Application.php';


// resources.db.params.dbname = "db143792_live"
// resources.db.params.username = "db143792_vv_live"
// resources.db.params.password = "3jJuTQFr"
// resources.db.params.host = "internal-db.s143792.gridserver.com"

echo $_ENV['DATABASE_SERVER'];

// $link = mysql_connect(
//    'internal-db.s143792.gridserver.com', 
//    'db143792',
//    'sIEW7V9h'
//    // , 
//    // 'db143792_live'
// );

// if (!$link) {
// 	die('Could not connect: ' . mysql_error());
// }

// echo 'Connected successfully';
// mysql_close($link);

?>



<br><br>

<?php 


phpinfo();

?>