<?php /** @noinspection PhpIllegalPsrClassPathInspection */

/** @noinspection AutoloadingIssuesInspection */

use eftec\bladeone\BladeOne;
use eftec\bladeonehtml\BladeOneHtml;
use eftec\CacheOneRedis;
use eftec\PdoOne;
use eftec\routeone\RouteOne;
use eftec\ValidationOne;

include __DIR__."/../vendor/autoload.php";

// validation

// Again, our container :-3
function valid()  {
    global $valid;
    if ($valid===null) {
        $valid=new ValidationOne();
    }
    return $valid;
}


// persistence

// Our container.
function pdoOne() {
    global $database;
    if ($database===null) {
        $database=new PdoOne('mysql','127.0.0.1','root','abc.123','example');
        $database->logLevel=3; // it shows all errors
        try {
            $database->connect(true);
        } catch(exception $ex) {
            die("The database is not available");
        }
    }
    return $database;
}





// cache

function cache() {
    global $cache;
    if ($cache===null) {
        $cache=new CacheOneRedis("127.0.0.1","example");
    } else {
        valid()->addMessage('REDIS','Unable to connect to Redis','info');
    }
    return $cache;
}

// view

class myBlade extends  BladeOne {
    use BladeOneHtml; // using the extension efec/BladeOneHtml
}



// our container. :-3 (it's a global function that is also a singleton
function blade() {
    global $blade;
    if ($blade===null) {
        $blade=new myBlade();
        $blade->setMode(BladeOne::MODE_DEBUG);
    }
    return $blade;
}


// route



// our container. :-3
function router() {
    global $router;
    
    if ($router===null) {
        $router=new RouteOne();
        $router->setDefaultValues('Ticket','Home'); // the default controller is Ticket and the default action is Index.
        $router->fetch(); // we process the current route.
    }
    
    return $router;
}

