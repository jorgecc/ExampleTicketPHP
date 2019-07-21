<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>
<body>


<form class="border border-light p-5" method="post">

    <div class="text-center">
        <p class="h4 mb-4">Add a new ticket</p>
    </div>

    <input type="text" name="User" class="form-control mb-4" placeholder="User" value="{{$ticket['User']}}">
    @if(valid()->getMessageId('User')->countError())
        <div class="text-danger">{{valid()->getMessageId('User')->first()}}<br></div>
    @endif()
    
    <input type="text" name="Title" class="form-control mb-4" placeholder="Title" value="{{$ticket['Title']}}">
    @if(valid()->getMessageId('Title')->countError())
        <div class="text-danger">{{valid()->getMessageId('Title')->first()}}<br></div>
    @endif()


    <textarea name="Description" class="form-control mb-4" placeholder="Textarea">{{$ticket['Description']}}</textarea>
    @if(valid()->getMessageId('Description')->countError())
        <div class="text-danger">{{valid()->getMessageId('Description')->first()}}<br></div>
    @endif()
    
    
    <button class="btn btn-info btn-block" type="submit">Add Ticket</button>

    <br/><hr/>
    <ul class="list-group">
        @foreach(valid()->messageList->allArray() as $k)
            <li class="list-group-item">{{$k}}</li>
        @endforeach
    </ul>
</form>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>