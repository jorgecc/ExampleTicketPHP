<?php

include "app/app.php";

if (router()->getType()=="controller") {
    try {
        router()->callObject('eftec\exampleticket\controller\%sController', true);
    } catch (Exception $e) {
        echo $e->getTraceAsString();
        echo "<hr>";
        echo "try /Ticket/List to show the table<br>";
        echo "Or /Ticket/Index to insert a new ticket<br>";
    }
}
