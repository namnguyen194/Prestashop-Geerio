<!DOCTYPE html>
<?php
setcookie("STATE", "YES", time()+3600);
var_dump($_COOKIE["STATE"]);
setcookie("STATE", "NO", time()-3600);
var_dump($_COOKIE["STATE"]);
?>
<html>
<body>


</body>
</html>