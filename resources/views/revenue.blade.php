<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Office Revenue</title>
     

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/jquery.sidr/2.2.1/stylesheets/jquery.sidr.dark.min.css">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<hr>

 <div class="container">
    <form class="form-inline" action="/calculate">

        <div class=" ui-widget form-group">
            <input class="form-control" placeholder="YYYY-MM"  name="start_date">
        </div>
        <button type="submit" class="btn btn-green">Calculate</button>
    </form>
    <div class="text-center">
       @if($revenue !== "")
        <h4><strong>expected revenue: $ {{ $revenue }}</strong></h4><h4><strong>expected total capacity of the unreserved offices:{{ $capacity }}</strong></h4>
       @endif
    </div>
</div>