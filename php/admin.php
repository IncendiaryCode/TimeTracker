<?php
session_start();
if(!isset($_SESSION['user'])){
    header("location:index.php");
    die();
}
$admin=$_SESSION['user'];
    echo "Hello ".$admin;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            TimeTracker
        </title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/new.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">

    </head>
    <body >
        <header class="admin-border">
          <div class="container ">
            <div class="row padding-around ">
                <div class="col-6  text-left">
                    <h5><i class="fas fa-clock "></i>
                        <span class="font-weight-light">Time</span><b>Tracker</b></h5>
                </div>
                <div class="col-2 offset-4 ">
                <div class="row">
                    <div class="col-5 text-right">
                    <i class="  fas fa-user-circle size"></i></div>
                       <div class="col-7 text-left">
                        admin <i class=" click fas fa-angle-down"></i>
                    </div>
                    <div class="info animated slideInDown">
                        
                <i class="  fas fa-user-circle size"><span class="col-7 text-right">admin</span></i>
                <span class="email">admin@printgreener.com</span><hr>
                <a href="index.php">
                <i class="  fas fa-power-off"></i> Logout</a>
            </div>
                </div>
            </div>
        </div>
        </div>
            
            </div>
      </header>


<div class="container">
<div class="col-md-12">
<div class="row">
<div class="col-md-4 col-6">
<h3>Project Table</h3>
</div>
<div class="col-md-4 offset-md-4 col-6 text-right">
<button type="button" class="btn btn-success">Add project</button>
</div>
</div>

<div class="table-responsive table-responsive-data2">
<table class="table table-data2">
<thead>
<tr>
<th>
<label class="au-checkbox">
<input type="checkbox">
<span class="au-checkmark"></span>
</label>
</th>
<th>name</th>
<th>email</th>
<th>description</th>
<th>date</th>
<th>status</th>
<th>price</th>
<th></th>
</tr>
</thead>
<tbody>
<tr class="tr-shadow">
 <td>
<label class="au-checkbox">
<input type="checkbox">
<span class="au-checkmark"></span>
</label>
</td>
<td>Lori Lynch</td>
<td>
<span class="block-email">lori@example.com</span>
</td>
<td class="desc">Samsung S8 Black</td>
<td>2018-09-27 02:12</td>
<td>
<span class="status--process">Processed</span>
</td>
<td>$679.00</td>
<td>
<div class="table-data-feature">
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Send">
<i class="zmdi zmdi-mail-send"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
<i class="zmdi zmdi-edit"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
<i class="zmdi zmdi-delete"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="More">
<i class="zmdi zmdi-more"></i>
</button>
</div>
</td>
</tr>
<tr class="spacer"></tr>
<tr class="tr-shadow">
<td>
<label class="au-checkbox">
<input type="checkbox">
<span class="au-checkmark"></span>
</label>
</td>
<td>Lori Lynch</td>
<td>
<span class="block-email">john@example.com</span>
</td>
<td class="desc">iPhone X 64Gb Grey</td>
<td>2018-09-29 05:57</td>
<td>
<span class="status--process">Processed</span>
</td>
<td>$999.00</td>
<td>
<div class="table-data-feature">
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Send">
<i class="zmdi zmdi-mail-send"></i>
</button>
 <button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
<i class="zmdi zmdi-edit"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
<i class="zmdi zmdi-delete"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="More">
<i class="zmdi zmdi-more"></i>
</button>
</div>
</td>
</tr>
<tr class="spacer"></tr>
<tr class="tr-shadow">
<td>
<label class="au-checkbox">
<input type="checkbox">
<span class="au-checkmark"></span>
</label>
</td>
<td>Lori Lynch</td>
<td>
<span class="block-email">lyn@example.com</span>
</td>
<td class="desc">iPhone X 256Gb Black</td>
<td>2018-09-25 19:03</td>
<td>
<span class="status--denied">Denied</span>
</td>
<td>$1199.00</td>
<td>
<div class="table-data-feature">
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Send">
<i class="zmdi zmdi-mail-send"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
<i class="zmdi zmdi-edit"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
<i class="zmdi zmdi-delete"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="More">
<i class="zmdi zmdi-more"></i>
</button>
</div>
</td>
</tr>
<tr class="spacer"></tr>
<tr class="tr-shadow">
<td>
<label class="au-checkbox">
<input type="checkbox">
<span class="au-checkmark"></span>
</label>
</td>
 <td>Lori Lynch</td>
<td>
<span class="block-email">doe@example.com</span>
</td>
<td class="desc">Camera C430W 4k</td>
<td>2018-09-24 19:10</td>
<td>
<span class="status--process">Processed</span>
</td>
<td>$699.00</td>
<td>
<div class="table-data-feature">
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Send">
<i class="zmdi zmdi-mail-send"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
<i class="zmdi zmdi-edit"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
<i class="zmdi zmdi-delete"></i>
</button>
<button class="item" data-toggle="tooltip" data-placement="top" title="" data-original-title="More">
<i class="zmdi zmdi-more"></i>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="javascript/addTask.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    </body>
</html>	