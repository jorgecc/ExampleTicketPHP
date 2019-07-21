<?php

use eftec\bladeone\BladeOne;
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
function database() {
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


try {
    // we try to create the schema. The user must have permission to create schema.
    database()->runRawQuery('CREATE SCHEMA `example`');
} catch (Exception $e) {
    valid()->addMessage('CREATESCHEMA','Unable to create schema '.database()->lastError(),'info');
}
try {
    database()->createTable('tickets'
        ,[
            'IdTicket'=>'INT NOT NULL AUTO_INCREMENT'
            ,'User'=>'varchar(50) NOT NULL'
            ,'Title'=>'varchar(50) NOT NULL'
            ,'Description'=>'varchar(200) NOT NULL'
        ],'IdTicket'
    );
} catch (Exception $e) {
    valid()->addMessage('CREATETABLE','Unable to create table tickets '.$database->lastError(),'info');
    // we try to create the schema. The user must have permission to create schema.
}



// view



// our container. :-3 (it's a global function that is also a singleton
function blade() {
    global $blade;
    if ($blade===null) {
        $blade=new BladeOne();
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

