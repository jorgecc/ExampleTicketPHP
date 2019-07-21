<?php

namespace eftec\exampleticket\dao;

class TicketDao
{
    public static function insert($ticket) {
        try {
            database()
                ->set('User=?', $ticket['User'])
                ->set('Title=?', $ticket['Title'])
                ->set('Description=?', $ticket['Description'])
                ->from('Tickets')
                ->insert();
            return true;
        } catch (\Exception $e) {
            valid()->addMessage('ERRORINSERT','Unable to insert. '.database()->lastError(),'error');
        }
        return false;
    }
    public static function list() {
        try {
            $tickets = database()->select('*')
                ->from('Tickets')
                ->toList();
        } catch (\Exception $e) {
            valid()->addMessage('ERRORINSERT','Unable to list. '.database()->lastError(),'error');
            $tickets=[];
        }
        return $tickets;
    }
}