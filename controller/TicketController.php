<?php

namespace eftec\exampleticket\controller;

use eftec\exampleticket\dao\TicketDao;
use eftec\exampleticket\factory\TicketFactory;

class TicketController
{
    public static function HomeAction($id="",$idparent="",$event="") {
        header('Location: Ticket/List');
    }
    public static function IndexActionGet($id="",$idparent="",$event="") {
        $ticket=TicketFactory::Factory(); // we create a new ticket.
        echo blade()->run('ticket.index',['ticket'=>$ticket]);
    }
    public static function IndexActionPost($id="",$idparent="",$event="") {
        $ticket=TicketFactory::Fetch(); // we read the information obtained from the user
        if (valid()->messageList->errorcount===0) {
            if (TicketDao::insert($ticket)) {
                // ticket inserted correctly, let's go to the list
                header('Location: ../../Ticket/List');
                exit();
            }
        }
        echo blade()->run('ticket.index',['ticket'=>$ticket]);
    }
    public static function ListAction($id="",$idparent="",$event="") {
        $tickets=TicketDao::list();
        echo blade()->run('ticket.list',['tickets'=>$tickets]);
    }
}