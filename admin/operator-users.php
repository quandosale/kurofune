<?php
    require_once("../backend/db.php");
    authenticate();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kurofune</title>
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
        <link rel="icon" href="../favicon.png" type="image/png" >
        <link href="../css/animate.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
        <link href="../css/responsive.css" rel="stylesheet">
    
        <style type="text/css">
            .alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
       padding: 15px;
    margin-bottom: 20px;
   
    border-radius: 4px;
}


.alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}
.deleteRecord{
    cursor: pointer;
}
.suspendRecord{
    cursor: pointer;
}
        </style>
    </head>
    <body class="top-navigation">
        <div id="wrapper">
            <div id="page-wrapper" class="gray-bg">
                <div class="col-md-12">
                    <div class="logo-wrapper clearfix">
                        
                        <div class="left-logo">
                            <a href="<?php echo TOOL_URL; ?>"><img src="../img/logo.png"  alt=""/> Administration</a>
                        </div>
                    </div>
                </div>
                
                <div class="row border-bottom white-bg">
                    <div class="menu-main">
                        <nav class="navbar navbar-static-top" role="navigation">
                            <div class="navbar-header">
                                <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                                <i class="fa fa-reorder"></i>
                                </button>
                                <!--<a href="#" class="navbar-brand">Inspinia</a>-->
                            </div>
                            <div class="navbar-collapse collapse" id="navbar">
                                <ul class="nav navbar-nav">
                                    <li class="active">
                                        <a aria-expanded="false" role="button" href="operator-users.php"> User</a>
                                    </li>
                                    
                                    <li>
                                        <a aria-expanded="false" role="button" href="operator-sales.php">Sales</a>
                                    </li>
                                    
                                    
                                    <li>
                                        <a aria-expanded="false" role="button" href="operator-selling.php">Selling</a>
                                    </li>
                                    
                                    <!--<li class="dropdown">
                                        <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                                        <ul role="menu" class="dropdown-menu">
                                            <li><a href="#">Menu item</a></li>
                                            <li><a href="#">Menu item</a></li>
                                            <li><a href="#">Menu item</a></li>
                                            <li><a href="#">Menu item</a></li>
                                        </ul>
                                    </li>-->
                                </ul>
                                <ul class="nav navbar-top-links navbar-right">
                                    <li>
                                        <a href="login.html">
                                            <i class="fa fa-sign-out"></i> Log out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                    
                    <div class="top-btn">
                        <div class="col-md-12">
                            <button class="btn-download btn-sm btn-default" id="export" data-export="export">CSV File Download</button>
                            <button class="btn btn-sm btn-primary AddRecord" >Add</button>
                            <button class="btn btn-sm btn-primary SaveRecords">Save</button>
                        </div>
                    </div>
                    
                    
                    
                </div>
                <div class="main-inner">
                    <div class="container-fluid">
                        <div class="section-operator">
                            <div class="table-main">
                                <div class="table-responsive">
                                    <form>
                                        <div class=" alert " style="display:none">Test msg</div>    
                                        <table width="100%" class="table table-bordered text-center  DataTable">
                                            <tr>
                                                <th style="width:167px;">REGISTRATION DATE</th>
                                                <th style="width:247px;">NAME</th>
                                                <th style="width:167px;">ID</th>
                                                <th style="width:213px"> Password</th>
                                                <th style="width:213px">EMAIL ADDRESS</th>
                                                <th colspan="2" align="center">ACCOUNT</th>
                                            </tr>  
                                            <?php  
                                              $res = mysqli_query($link, "SELECT * from users ");
                                              while($row = mysqli_fetch_array($res)){
                                            ?>
                                            <tr>
                                            <td><input type="text" name="registration_date[]" value="<?php echo date('m/d/Y' ,strtotime($row['created_at'])); ?>" class="form-control datepicker"></td>
                                            <td><input type="text" name="name[]"  value="<?php echo $row['name']; ?>" class="form-control"></td>
                                            <td><input type="text" name="id[]"  value="<?php echo $row['id']; ?>" class="form-control"></td>
                                            <td><input type="password" name="password[]" value="<?php echo $row['password']; ?>"  class="form-control"></td>
                                            <td><input type="email" name="email[]" value="<?php echo $row['email']; ?>" class="form-control"></td>
                                            <td>
                                                    <?php if($row['status']==1){?>
                                                    <span class="suspendRecord" data-record="2">SUSPEND</span>
                                                    <?php    }else if($row['status']==2){ ?>
                                                    <span class="suspendRecord" data-record="1">ACTIVATE</span>
                                                    <?php } ?>
                                            </td>
                                            <td><span class="deleteRecord">DELETE</span></td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    
                    <div class="text-center">
                        <strong>Copyright</strong> &copy 2016 | Kurofune . All Rights Reserved
                    </div>
                </div>
            </div>
        </div>
        <!-- Mainly scripts -->
        <script src="js/jquery-2.1.1.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript">
        var suspend;
        var id_del;
        $("#export").click(function(){
            $("table").tableToCSV();
        });
            $(document).ready(function(){
                $( ".datepicker" ).datepicker();
                $(".AddRecord").click(function (){
                    $('.DataTable').append('<tr>'+
                                            '<td><input type="text" name="registration_date[]"  class="form-control datepicker"></td>'+
                                            '<td><input type="text" name="name[]"  class="form-control"></td>'+
                                            '<td><input type="text" name="id[]"  class="form-control"></td>'+
                                            '<td><input type="password" name="password[]"  class="form-control"></td>'+
                                            '<td><input type="email" name="email[]"  class="form-control"></td>'+
                                            '<td><span class="suspendRecord" data-record="2">SUSPEND</span></td>'+
                                            '<td><span class="deleteRecord">DELETE</span></td>'+
                                        '</tr>');
                $( ".datepicker" ).datepicker();
                })

                 $('body').on('click','.suspendRecord', function(){
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: "backcode.php",
                        data: { data: 'suspendRecord',actionType : $(this).data('record') ,id:$(this).parents('tr').find('input[name*="id"]').val()},
                        dataType: "json",
                        success: function(data) {
                            suspend = data.service_no;

                        },
                        error: function() {

                        }
                    });
                    if(suspend){  
                        if($(this).data('record')==1){
                            $(this).data('record',2);
                            $(this).html('SUSPEND');
                            $('.alert').css('display','block');
                            $('.alert').addClass('alert-success');
                            $('.alert').html('User suspended successfully');
                        }else if($(this).data('record')==2){
                            $(this).data('record',1);
                            $(this).html('ACTIVATE');
                            $('.alert').css('display','block');
                            $('.alert').addClass('alert-success');
                            $('.alert').html('User activated successfully');
                        }
                        setTimeout(function(){
                            $('.alert').css('display','none');
                            $('.alert').removeClass('alert-success');
                            $('.alert').removeClass('alert-danger');
                            $('.alert').html('');   
                        },3000)
                    }
                });

                $('body').on('click','.deleteRecord', function(){
                    if(confirm('Are you sure want to delete')){
                        
                        var c = $.ajax({
                            type: "POST",
                            url: "backcode.php",
                            data: { data: 'delRecord', id:$(this).parents('tr').find('input[name*="id"]').val()},
                            dataType: "json",
                            success: function(data) {
                                if(data.service_no){
                                    id_del = true;
                                }else {
                                    id_del = false;
                                }
                            },
                            error: function() {
                            }
                        });
                        $(this).closest('tr').remove();
                        $('.alert').css('display','block');
                        $('.alert').addClass('alert-success');
                        $('.alert').html('User deleted successfully');
                        setTimeout(function(){
                            $('.alert').css('display','none');
                            $('.alert').removeClass('alert-success');
                            $('.alert').removeClass('alert-danger');
                            $('.alert').html('');   
                        },2000)
                    }
                  
                });
                 $('body').on('click','.SaveRecords', function(){
                     var str = $( "form" ).serialize();
                     console.log(str);
                     $.ajax({
                        type: "POST",
                        url: "backcode.php",
                        data: str,
                        dataType: "json",
                        success: function(data) {
                            if(data.service_no){

                                $('.alert').html('User added successfully');
                                $('.alert').css('display','block');
                                $('.alert').addClass('alert-success');
                            }else{
                                $('.alert').html('User cannot be added');
                                $('.alert').css('display','block');
                                $('.alert').addClass('alert-danger');
                            }
                            setTimeout(function(){
                                $('.alert').css('display','none');
                                $('.alert').removeClass('alert-success');
                                $('.alert').removeClass('alert-danger');
                                $('.alert').html('');   
                            },2000)
                        },
                        error: function() {
                            alert('error handing here');
                        }
                    });
                });
            });
        </script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="js/jquery-2.1.1.js"></script>

        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="js/jquery.tabletoCSV.js"></script>
    </body>
    <style type="text/css">
        .deleteRecord{
            cursor: pointer;
        }
    </style>
</html>