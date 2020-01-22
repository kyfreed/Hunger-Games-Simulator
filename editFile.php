<?php
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
print_r2($_REQUEST);
file_put_contents($_REQUEST['fileName'], $_REQUEST['castObject']);
echo $_REQUEST['castObject'];
