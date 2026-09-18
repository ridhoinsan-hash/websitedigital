<?php
echo "MYSQLHOST = " . (getenv('MYSQLHOST') ?: '(kosong)') . "<br>";
echo "MYSQLUSER = " . (getenv('MYSQLUSER') ?: '(kosong)') . "<br>";
echo "MYSQLDATABASE = " . (getenv('MYSQLDATABASE') ?: '(kosong)') . "<br>";
echo "MYSQLPORT = " . (getenv('MYSQLPORT') ?: '(kosong)') . "<br>";
echo "MYSQLPASSWORD = " . (getenv('MYSQLPASSWORD') ? '(ada)' : '(kosong)') . "<br>";
?>
