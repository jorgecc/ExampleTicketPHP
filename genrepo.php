<?php

include "app/app.php";
echo "<h1>Compiling repository classes</h1>";

pdoOne()->log=3;
$errors=pdoOne()->generateAllClasses(
    ['tickets'=>'TicketsRepo'] // table->class repository
    ,'ExampleTicketBase' // base class (for the database
    ,'eftec\exampleticket\repo' //namespace of the class repository
    ,__DIR__.'/repo'  // folder of the class repository
);

var_dump($errors);