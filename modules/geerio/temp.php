<!DOCTYPE html>
<?php
$actual_link = $_SERVER['REQUEST_URI'];
?>
<html>
<body>
<?php 
    $list_page_value = 'start_namnguyen';
            $list_page_value = substr($list_page_value,6);
            $b = '/ps-v3/qưert';
            $a = $_SERVER['REQUEST_URI'];
            echo $a . '<br/>';
            $c = substr($a,  strlen($b));
            $d= 'modules';
            echo 'a: '. $a.'<br> b: '.$b.'<br> c:'.$c.'<br>';
            echo '222:'. stripos($c, $d).'<br>';
            $a = strrev($a);
            $b = strrev($b);
            $c=strrev($c);
            $d= 'php.pmet';
            echo $a.'<br>'.$b.'<br>'.$c.'<br>';
            echo '222:'. stripos($c, $d).'<br>';
?>

</body>
</html>