<!DOCTYPE html>
<!--
Tenki dashboard, built with adminlte starter page
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tenki | Watering Schedules</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" onclick="getSchedule()" class="nav-link">Refresh Schedules</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link">
      <i class="ml-2 mr-1 fas fa-umbrella"></i>
      <span class="brand-text font-weight-light">Tenki</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
           
          </li>
          <li class="nav-item">
            <a href="schedule.php" class="nav-link active">
              <i class="nav-icon fas fa-calendar"></i>
              <p>
                Manage Schedules
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Watering Schedules</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
      
        <!-- Current schedule -->
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <div class="card card-primary card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="far fa-chart-bar"></i>
                      Current Schedule
                    </h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" onclick="saveCurrentSchedule()" >
                        Save Schedule to Database
                      </button>
                    </div>
                  </div>
                  <!-- /.card-header-->
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="curr_table" class="table m-0">
                        <thead>
                        <tr>
                          <th></th>
                          <th>Hour</th>
                          <th>Minute</th>
                          <th>Remove</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                    <!-- /.table-responsive-->
                    <!-- input for new schedule -->
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Add new schedule (HH:MM)</span>
                      </div>
                      <input id="curr_new_schedule" type="time" class="form-control"/>
                      <div class="input-group-append">
                        <button class="btn btn-outline-success" type="button" onclick="currentScheduleAdd()">Add</button>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-body-->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->  

              <div class="col-md-6">
                <!-- watering duration -->
                <div class="row">
                  <div class="info-box">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-tint"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Water Duration</span>
                      <span class="info-box-number">
                        <div class="input-group">
                          <input type="number" id="curr_duration" class="form-control"></input>
                          <div class="input-group-append">
                            <span class="input-group-text">minute(s)</span>
                          </div>
                        </div>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.row --> 
                  
                <!-- schedule revision -->
                <div class="row">
                  <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Schedule Revision</span>
                      <span class="info-box-number">
                        <span id="curr_revision">-</span>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.row --> 
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        
        <!-- Default schedule -->
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <div class="card card-primary card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="far fa-chart-bar"></i>
                      Default Schedule
                    </h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" onclick="saveDefaultSchedule()" >
                        Save Schedule to Database
                      </button>
                    </div>
                  </div>
                  <!-- /.card-header-->
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="def_table" class="table m-0">
                        <thead>
                        <tr>
                          <th></th>
                          <th>Hour</th>
                          <th>Minute</th>
                          <th>Remove</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                    <!-- /.table-responsive-->
                    <!-- input for new schedule -->
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Add new schedule (HH:MM)</span>
                      </div>
                      <input id="def_new_schedule" type="time" class="form-control"/>
                      <div class="input-group-append">
                        <button class="btn btn-outline-success" type="button" onclick="defaultScheduleAdd()">Add</button>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-body-->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->  
            </div>
            <!-- /.row -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2020 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script>

var currentSchedule;
var defaultSchedule;

function getSchedule(){
  $.getJSON("get_config.php")
  .done(function( data ) {
  
  //id 1 is default
  //id 2 is current
  data.forEach(function (item){
    //update the revision into a date object
    item.revision = new Date(item.revision+"Z"); //adding a Z to indicate this is a UTC time 
    
    if (item.id == 1){
      defaultSchedule = item;
    }
    if (item.id == 2){
      currentSchedule = item;
    }
  });
    
    //update sensor data when result is back
    showSchedules();
  })
}

//update the displayed schedules
function showSchedules(){
  showCurrentSchedule();
  showDefaultSchedule();
}

function showCurrentSchedule(){
  //clear table
  $('#curr_table tbody').empty();

  //populate table
  currentSchedule.schedule.forEach(function (item, index){
  //need add 8hr to get correct timezone  
  //trash way to do this but idc
  local_hour = item.hour + 8;
  
  //overflow checking
  if (local_hour > 23){
    local_hour -= 24;
  }
  
    $('#curr_table').append('<tr><td></td><td>' + local_hour + '</td><td>' + item.minute + '</td><td><button class="btn bg-danger" onclick="currentScheduleRemove('+ index +')">Delete</button></td></tr>');
  })

  //update duration, revision
  $('#curr_duration').val(currentSchedule.duration);
  $('#curr_revision').html(currentSchedule.revision);
}

function currentScheduleRemove(index){
  //remove the selected entry from the array
  currentSchedule.schedule.splice(index, 1);
  
  //update the table
  showCurrentSchedule();
}

function currentScheduleAdd(){
  var inputTime = $('#curr_new_schedule').val().split(":");
  
  var newSchedule = new Object();
  
  //minus 8 hours so back to UTC time
  newSchedule.hour = inputTime[0] - 8;
  newSchedule.minute = parseInt(inputTime[1]);
  
  //underflow checking
  if (newSchedule.hour < 0){
    newSchedule.hour += 24;
  }
  
  currentSchedule.schedule.push(newSchedule);
  
  //update the table
  showCurrentSchedule();

  //clear the input
  $('#curr_new_schedule').val("");
}

//save current schedule to database, and push it to the control node
function saveCurrentSchedule(){
  $.post("update_config.php", { 
    "id": 2,
    "config": JSON.stringify(currentSchedule)
  },
  function (data) {
      //refetch schedule once saving to db is done
    getSchedule();
    
    //push changes to node
    pushCurrentScheduleToNode();
  });
}

//update the current duration when focus is lost
$("#curr_duration").focusout(function(){
  currentSchedule.duration = $('#curr_duration').val();
});

//call the api that invokes the lambda function
//this sends the current schedule on the database to the control node
function pushCurrentScheduleToNode(){
  $.getJSON("https://o98wl44xed.execute-api.us-east-1.amazonaws.com/default/PushCurrentScheduleToMQTT");
}

/**********************  Default schedule ***************************/

function showDefaultSchedule(){
  //clear table
  $('#def_table tbody').empty();

  //populate table
  defaultSchedule.schedule.forEach(function (item, index){
  //need add 8hr to get correct timezone  
  //trash way to do this but idc
  local_hour = item.hour + 8;
  
  //overflow checking
  if (local_hour > 23){
    local_hour -= 24;
  }
  
    $('#def_table').append('<tr><td></td><td>' + local_hour + '</td><td>' + item.minute + '</td><td><button class="btn bg-danger" onclick="defaultScheduleRemove('+ index +')">Delete</button></td></tr>');
  })

  //update duration, revision
  $('#def_duration').val(defaultSchedule.duration);
  $('#def_revision').html(defaultSchedule.revision);
}

function defaultScheduleRemove(index){
  //remove the selected entry from the array
  defaultSchedule.schedule.splice(index, 1);
  
  //update the table
  showDefaultSchedule();
}

function defaultScheduleAdd(){
  var inputTime = $('#def_new_schedule').val().split(":");
  
  var newSchedule = new Object();
  
  //minus 8 hours so back to UTC time
  newSchedule.hour = inputTime[0] - 8;
  newSchedule.minute = parseInt(inputTime[1]);
  
  //underflow checking
  if (newSchedule.hour < 0){
    newSchedule.hour += 24;
  }
  
  defaultSchedule.schedule.push(newSchedule);
  
  //update the table
  showDefaultSchedule();

  //clear the input
  $('#def_new_schedule').val("");
}

//save default schedule to database, and push it to the control node
function saveDefaultSchedule(){
  $.post("update_config.php", { 
    "id": 1,
    "config": JSON.stringify(defaultSchedule)
  },
  function (data) {
    //refetch schedule once saving to db is done
    getSchedule();
  });
}

// this runs when page is ready
$(function () {
  
  getSchedule();

})

</script>
</body>
</html>
