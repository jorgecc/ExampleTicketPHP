<?php

namespace eftec\exampleticket\dao;

/**
 * Class TicketDao
 * @package eftec\exampleticket\dao
 * @deprecated 
 */
class TicketDao
{
    public static function insert($ticket) {
        try {
            pdoOne()
                ->set('User=?', $ticket['User'])
                ->set('Title=?', $ticket['Title'])
                ->set('Description=?', $ticket['Description'])
                ->from('Tickets')
                ->insert();
            if (cache()->enabled) {
                cache()->invalidateGroup('tickets');
            }
            return true;
        } catch (\Exception $e) {
            valid()->addMessage('ERRORINSERT', 'Unable to insert. '.pdoOne()->lastError(), 'error');
        }
        return false;
    }
    public static function list() {
        try {
            if (cache()->enabled) {
                $result=cache()->get('tickets','list',true);
                if($result!==null) return json_decode($result,true);
            }
            $tickets = pdoOne()->select('*')
                               ->from('Tickets')
                               ->order('IdTicket desc')
                               ->limit('1,20')
                               ->toList();
            if (cache()->enabled) {
                cache()->set('tickets','list',$tickets,3600); // 3600 seconds
            }
            
        } catch (\Exception $e) {
            valid()->addMessage('ERRORINSERT', 'Unable to list. '.pdoOne()->lastError(), 'error');
            $tickets=[];
        }
        return $tickets;
    }
}