<?php
@session_start();

include "app/app.php";

if (router()->getType() === "controller") {
    try {
        router()->callObject('eftec\exampleticket\controller\%sController', true);
    } catch (Exception $e) {
        echo "<h3>Exceptions</h3>";
        echo $e->getMessage();
        echo $e->getTraceAsString();
        echo "<hr>";
        echo "try <a href='Ticket/List'>Ticket/List</a> to show the table<br>";
        echo "Or <a href='Ticket/Index'>Ticket/Index</a> to insert a new ticket<br>";
    }
}
