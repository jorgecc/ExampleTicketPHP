<?php

namespace eftec\exampleticket\controller;


use eftec\exampleticket\factory\TicketFactory;
use eftec\exampleticket\repo\TicketsRepo;

class TicketController
{
    public static function HomeAction($id = "", $idparent = "", $event = "") {
        header('Location: Ticket/List');
    }

    public static function IndexActionGet($id = "", $idparent = "", $event = "") {
        $ticket = TicketFactory::Factory(); // we create a new ticket.
        blade()->regenerateToken(); // we create a new token
        echo blade()->run('ticket.index', ['ticket' => $ticket]);
    }

    public static function IndexActionPost($id = "", $idparent = "", $event = "") {
        $ticket = TicketFactory::Fetch(); // we read the information obtained from the user
        if (blade()->csrfIsValid()) {
            if (valid()->messageList->errorcount === 0) {
                if (TicketsRepo::insert($ticket)) {
                    // ticket inserted correctly, let's go to the list
                    header('Location: ../Ticket/List');
                    exit();
                }
                echo blade()->run('ticket.list', ['tickets' => $ticket]);
            } else {
                echo blade()->run('ticket.index', ['ticket' => $ticket]);
            }              
        } else {
            echo "csrf invalid";
        }
    }
    public static function ListAction($id = "", $idparent = "", $event = "") {
        $tickets = TicketsRepo::toList(); // we read the information obtained from the user
        echo blade()->run('ticket.list', ['tickets' => $tickets]);
    }
}