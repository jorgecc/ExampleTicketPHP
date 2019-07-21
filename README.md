# Why we won't want to use a framework?.

A framework is a crucial tool to develop a system quickly and fast. However, it has a cost, the performance. And once the performance is gone, then we must disarm a lot of code to recover the missing performance.

![https://miro.medium.com/max/1200/1*QbApXSaxgUvd6b1GM-29-A.png](https://miro.medium.com/max/1200/1*QbApXSaxgUvd6b1GM-29-A.png)

It is an old comparison, but it is still valid.

Also, a framework is, ahem, a frame, it marks some rules that could work generically, but those rules couldn't be the best for our project.

## Reinvent the wheel.

However, we won't want to reinvent the wheel.

In our case, we will use the following libraries:

* https://github.com/EFTEC/bladeone  It's a template library (for our VIEW). It's minimalist (one class and no dependency) yet complete.  Also, it uses the syntax of Laravel's Blade.

* https://github.com/EFTEC/pdpone. It's our persistence library (for the database). It's also minimalist; it's practically a wrapper over PDO (and it's fast).  We could use PDO too, but we don't want to reinvent the wheel.   

* https://github.com/EFTEC/routeone. It's our router library. Again, it's minimalist.

* https://github.com/EFTEC/routeone. It's our router library. Again, it's minimalist.

* https://github.com/EFTEC/validationone. It's our library to fetch values and validates the information. It's also a container for errors.  Validation and error message works in tandem.  It's also minimalist (3 classes and nothing more).

Also, we want a code that could grow, so it must be easy to maintenance (i.e. we don't want spaghetti code) but also we don't want an over-engineered code.

Since we are working on MVC, then it's easy to determine if a code is clean or not: The Controller. **The Controller must do some task but it must not contains the implementation of the task**. I explain.  Let's say our Controller must read a table, then our controller must call the function that read the table instead of creates the code to read the table inside the controller.

Right example:

```php
function listAction() {
   $r=SomeClassDao::list(); 
}
```

Bad example:

```php
function listAction() {
   $db=new ConnectionDatabase();
   $r=$db->query('select * from sometable'); 
}
```


## What we want to do?
A ticket system.

![](https://thepracticaldev.s3.amazonaws.com/i/46ikpa7faqvwre3yu7pj.jpg)

![](https://thepracticaldev.s3.amazonaws.com/i/qyc8wfg04m3w9e3wvwxk.jpg)

## What we need to?

### Structure

📜.htacess
📜 router.php
📜 composer.json (we use Composer)
📁app
.... 📜 app.php (our common code)
📁controller
.... 📜 TicketController.php
📁 dao
.... 📜 TicketDao.php
📁 factory
.... 📜 TicketFactory.php
📁 views
.... 📁 ticket
.... .... 📜 index.blade.php
.... .... 📜 list.blade.php


First, we need some common code and find a way to use it across the whole code. For example, the database. The database must be a singleton (we won't want to create more than one connection per request), and we must configure it once.
So, we will use this next function, that works as GLOBAL and singleton generator. It's pretty simple, and it works.

```php
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
```
This function is simple to the extend we could add more code inside it.  If the object database doesn't exist, then it creates one. It connects to 127.0.0.1, user root, password abc.123 y schema example.

And here some puritans could claim: 
> HERESY HERESY!.   This code is using global.

![](https://i.pinimg.com/originals/9a/aa/52/9aaa521195c5b2f30e3164e371128927.jpg)

And lets me explain something. There is nothing wrong with the use of global.    If something must be used or accessed everywhere, then it makes a sense that this something is global.     What are we skipping with global? Dependency injection, containers, and whatnot!    The use of global is bad if we use GLOBAL for variables or objects that they must not be shared globally, for example, local variables. So the use of global is not as absolute. Only sith think in absolute We could use globally if it is used correctly.  The 100% ban on the use of GLOBAL it's silly, and it's religion.

![](https://img.fireden.net/v/image/1446/92/1446920073255.png)

Now, I did the same with all the code that must be shared everywhere.
* valid() for the validation (and error container)
* database() for the database
* blade() for the template system
* router() for the router.

So this solution is simple (so it's easy to debug and to understand), scalable, testable and it's blazing fast!.
We could have store those function inside a class but MEH. We don't want to do that. Why?.  Because we are adding more code to call the function, nothing more.

## Router
```php
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
```
It is our router file. We use router() (our global function).  Our code converts an url into a call to a class/method.
For our example:
* somedomain/Ticket/List -> calls our Ticket Controller Class and the method listAction

* somedomain/Ticket/Index-> calls our Ticket Controller Class and the method indexActionGet or indexActionPost (if the call is via get or post).

* somedomain/ -> redirect to somedomain/Ticket/List

It also requires a .htacess file (for Apache)

```
...
RewriteRule ^(.*)$ router.php?req=$1 [L,QSA]
</IfModule>
```

### Considerations

* Is our router safe?  Yes, if the user enters an incorrect or malicious path, then it will show a message and it only could call a Controller Class, it could not execute any arbitrary code but controller (and methods that end with Action).

## Service class
Now, we need some service class. What is a service-class? A service class in short terms, a collection of methods.   Technically it could also contain the (business) logic of the project (business logic = method).

### TicketFactory
It's our service class dedicated to creating a new ticket.
* function factory() In this method we create an empty ticket. It's just an array.

```php
    public static function Factory() {
        return ['IdTicket'=>0,'User'=>'','Title'=>'','Description'=>''];
    }
```


* function Fetch() In this method we fetch the values entered by the user, we also validate the information, and we store the errors (if any), inside the validation class.  SRP speaking, this method is breaking the SRP. However, fetching->validating->storing messages it's something that works together so separating will not do any good.

```php
public static function Fetch() {
        $ticket=self::Factory(); // we star creating an empty ticket
        $ticket['IdTicket']=Valid()
            ->type('integer')
            ->required(false)
            ->def(0)
            ->condition('gte','The id can\'t be negative',0)
            ->fetch(INPUT_POST,'IdTicket');        
        // etc. with the other fields
        return $ticket;
    }
```

Our library Valid does the heavy lifting of validating the information of the user. We could have done a simple:

```php
$ticket['IdTicket']=$_POST['IdTicket'];
```

but what if the information is missing, or if it is not an integer, of it is a string but it is too long, etc. etc.


### TicketDao
It's our service class dedicated to every task involving persistence and tickets.
* function insert($ticket) This method inserts a new ticket into the database. If the operation fails, then we store a message (inside the validation class).

```php
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
```

We use our global function database(). It uses prepared-statement (hence the Column=? annotation.  So it's SQL-injection free.  And if it fails, then it will store the message into the valid() method.

* function list() This method returns all tickets from the database. If the operation fails, then we store a message (inside the validation class).


```php
    public static function list() {
        try {
            $tickets = database()->select('*')
                ->from('Tickets')
                ->toList(); // select * from tickets
        } catch (\Exception $e) {
            valid()->addMessage('ERRORINSERT','Unable to list. '.database()->lastError(),'error');
            $tickets=[];
        }
        return $tickets;
    }
```

### Considerations

* What if the database is down?. Then it will stop the execution of any code.
* What if the operation fails?. Then it will store an error message.
* What if the user enters a malicious code?. The library uses prepared-statement, so the code is safe from malicious entries. For example: if the name of the ticket is O'hara, the system will work correctly.

So the security and stability of the system are ensured.

## Views
Since we are using the library BladeOne, then we could use views (instead of duct & taping the html+php inside the same class.
In our case, we will use two views (for insert and list)
* /views/ticket/index
It is part of the view:
```php
    <input type="text" name="User" class="form-control mb-4" placeholder="User" value="\{\{$ticket['User']\}\}">
    @ if(valid()->getMessageId('User')->countError())
        <div class="text-danger">\{\{ valid()->getMessageId('User')->first()\}\}<br></div>
    @endif()

```
Here we are doing two operations: we are showing the $ticket (field User), and we are showing an error message (if any) using our global function called valid().   Each error is store into a container (in this case the container is called "User").  So it is possible to show more than one error at the same time (and this project does that).

* /views/ticket/list
It shows a list of tickets.

### Considerations

* Is the view safe?. Yes, It uses { { $variable} } to show a variable. It is converted into **htmlentities($variable)**

## Controller

What is a controller class?. In short, it's a class called by the router and it joins all the code. Finally, the controller class could call the view layer (and in our case, it's exactly what it does).


```php
<?php
namespace eftec\exampleticket\controller;

use eftec\exampleticket\dao\TicketDao;
use eftec\exampleticket\factory\TicketFactory;

class TicketController
{
    public static function HomeAction($id="",$idparent="",$event="") {
    }
    public static function IndexActionGet($id="",$idparent="",$event="") {
    }
    public static function IndexActionPost($id="",$idparent="",$event="") {
    }
    public static function ListAction($id="",$idparent="",$event="") {
    }
}
```

It is our controller (without implementation)
* HomeAction (is called for GET and POST) as a default action
* IndexActionGet (is called for GET) when we want to insert a new ticket 


```php
    public static function IndexActionGet($id="",$idparent="",$event="") {
        $ticket=TicketFactory::Factory(); // we create a new ticket.
        echo blade()->run('ticket.index',['ticket'=>$ticket]);
    }
```
This method is called when we want to show a new form to insert a new TICKET. So we create a new ticket (an empty ticket). How?  We use the TicketFactory::Factory() method.
Finally, we called the view ticket/index, and we send the ticket (we want to show a TICKET even if it is empty).  In our view, it looks like  :

> value="$ticket['User']">



> Note: what is the meaning of the arguments:  **($id="",$idparent="",$event="")** ?   Those arguments could be use by our code. They are filled by the router().  In this case, if we call the route : domain/Ticket/Index/1/2?_event=hi  then $id=1, $idparent=2 and $event=hi.

* IndexActionPOST (is called for POST) when we want to insert a new ticket and after we push the button

```php
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
```
We call this method after we push the button (POST).
* First, we read (fetch) the information entered by the user. If the information has errors, then the error messages are stored inside valid()
* Second, we check valid. If the error count is zero then we insert the ticket inside the database. And if the we insert it correctly then redirect to the list.
* Third, however if something fails, then we show the form again (and the view shows any error).


* ListAction (is called for GET and POST) when we want to show the list

## And finally, it is our code

![](https://thepracticaldev.s3.amazonaws.com/i/eev4qkh2rcabbhv7ijd0.gif)

The full code is here

https://github.com/jorgecc/ExampleTicketPHP

## How much minimalist is our code?

* 32 PHP files (including libraries and examples).
* 7000 lines of code (including lines of code of our libraries).
* Our code (excluding libraries and views) are 166 lines of code. :-3

![](http://images6.fanpop.com/image/answers/3032000/3032767_1349529235356.87res_500_281.jpg)

## What is missing?

* Since this project lacks users, then it misses cross-posting protection. 

So, let's add csrf protection.

It is our controller with csrf protection:

```php
    public static function IndexActionGet($id="",$idparent="",$event="") {
        // ....
        blade()->regenerateToken(); // we create a new csrf token
    }
    public static function IndexActionPost($id="",$idparent="",$event="") {
        // .....
        if (blade()->csrfIsValid()) { // we validated the token
           // ....
        } else {
            valid()->addMessage('TOKEN','Token invalid','error');
        }
        // ....
    }
```

and in our view

```php
<form class="border border-light p-5" method="post">
    @csrf()
   ....
```

* It also misses cache and pagination.  We won't add cache or pagination but we want to limit the number of tickets to show.

It is part of the code without limit

```php
$tickets = database()->select('*')
	->from('Tickets')
	->toList(); // select * from tickets

```

It is part of the code with limit, so the system will never overflow.

```php
$tickets = database()->select('*')
	->from('Tickets')
	->order('IdTicket desc')
	->limit('1,20')
	->toList(); // select * from tickets order by idticket desc limit 1,20
```





