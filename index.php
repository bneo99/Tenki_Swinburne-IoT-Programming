<!DOCTYPE html>
<!--
Tenki dashboard, built with adminlte starter page
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tenki | Dashboard</title>

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
        <a href="#" onclick="getSensorData()" class="nav-link">Refresh Sensor Data</a>
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
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
           
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Simple Link
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
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
      <div class="row mb-2">
          <div class="col-sm-6">
            <h5 class="m-0 text-dark">Current Sensor Data</h5>
          </div><!-- /.col -->
        </div><!-- /.row -->
        <!-- Info boxes -->
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-thermometer-half"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Temperature</span>
                <span class="info-box-number">
                  <span id="curr_temperature">-</span>
                  <small>°C</small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-tint"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Relative Humidity</span>
                <span class="info-box-number">
                  <span id="curr_humidity">-</span>
                  <small>%</small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-umbrella"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Rain</span>
                <span class="info-box-number">
                <span id="curr_rain">-</span>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-plane"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Barometric Pressure</span>
                <span class="info-box-number">
                  <span id="curr_pressure">-</span>
                  <small>kPa</small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      
        <div class="row">
          <div class="col-lg-6">
            <!-- Temp chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="far fa-chart-bar"></i>
                  Temperature History (past 5 hours)
                </h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div id="temperature-chart" style="height: 300px;"></div>
              </div>
              <!-- /.card-body-->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col-lg-6 -->
          <div class="col-lg-6">
            <!-- Humidity chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="far fa-chart-bar"></i>
                  Humidity History (past 5 hours)
                </h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div id="humidity-chart" style="height: 300px;"></div>
              </div>
              <!-- /.card-body-->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col-lg-6 -->
        </div>
        <!-- /.row -->
        
        <div class="row">
          <div class="col-lg-6">
            <!-- Temp chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="far fa-chart-bar"></i>
                  Rain History (past 5 hours)
                </h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div id="rain-chart" style="height: 300px;"></div>
              </div>
              <!-- /.card-body-->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col-lg-6 -->
          <div class="col-lg-6">
            <!-- Humidity chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="far fa-chart-bar"></i>
                  Barometric Pressure History (past 5 hours)
                </h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div id="pressure-chart" style="height: 300px;"></div>
              </div>
              <!-- /.card-body-->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col-lg-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
<!-- FLOT CHARTS -->
<script src="../../plugins/flot/jquery.flot.js"></script>
<script src="../../plugins/flot-old/jquery.flot.time.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../../plugins/flot-old/jquery.flot.resize.min.js"></script>

<script>


var sensorHistoryArray = [];

//fetch sensor data from server
function getSensorData(count = 1800) {
  $.getJSON( "fetch_sensor.php", { count: count } )
  .done(function( data ) {
  
  //shove the data into the main array, so other functions can use it
  //we're changing the format while we're at it, so more useable
    sensorHistoryArray = data.map(item => {
      var newObj = new Object();
      newObj.datetime = new Date(item.datetime+"Z"); //adding a Z to indicate this is a UTC time 
      newObj.temperature = item.temperature.toFixed(2);
      newObj.humidity = item.humidity.toFixed(2);
      newObj.rain = item.rain;
      newObj.pressure = (item.pressure/1000).toFixed(2);
      return newObj;
    });
    
    //update sensor data when result is back
    updateCurrentSensor();
    loadCharts();
  })
}

//display first(latest) sensor data
function updateCurrentSensor() {
//rounding them to 2dp and pressure convert to kPa
  $('#curr_temperature').html(sensorHistoryArray[0].temperature);
  $('#curr_humidity').html(sensorHistoryArray[0].humidity);
  $('#curr_pressure').html(sensorHistoryArray[0].pressure);
  $('#curr_rain').html(sensorHistoryArray[0].rain);
}

function loadCharts(){
  loadTempChart();
  loadHumidityChart();
  loadRainChart();
  loadPressureChart();
}

function loadTempChart(){

  var tempArr = sensorHistoryArray.map(item => [item.datetime, item.temperature]);

  var temp_data = {
    data : tempArr,
    color: '#3c8dbc'
  }

  $.plot('#temperature-chart', [temp_data], {
    grid  : {
      hoverable  : true,
      borderColor: '#f3f3f3',
      borderWidth: 1,
      tickColor  : '#f3f3f3'
    },
    series: {
      shadowSize: 0,
      lines     : {
        show: true
      },
      points: {
        show: false
      }
    },
    lines : {
      fill : false,
      color: ['#3c8dbc']
    },
    yaxis : {
      show: true
    },
    xaxis : {
      show: true,
      mode: "time",
      timeformat: "%H:%M", // 24hr format
      tickSize: [1, "hour"], // tick every hour
      timezone: "browser"
    }
  })
  
  //Initialize tooltip on hover
  $('<div class="tooltip-inner" id="temp-chart-tooltip"></div>').css({
    position: 'absolute',
    display : 'none',
    opacity : 0.8
  }).appendTo('body')
  $('#temperature-chart').bind('plothover', function (event, pos, item) {

    if (item) {
      var x = new Date(item.datapoint[0]).toString(),
          y = item.datapoint[1].toFixed(2)

      $('#temp-chart-tooltip').html(x + '<br/>Temperature = ' + y + '°C')
        .css({
          top : item.pageY + 5,
          left: item.pageX + 5
        })
        .fadeIn(200)
    } else {
      $('#temp-chart-tooltip').hide()
    }

  })
}

function loadHumidityChart(){

  var humiArr = sensorHistoryArray.map(item => [item.datetime, item.humidity]);

  var humi_data = {
    data : humiArr,
    color: '#3c8dbc'
  }

  $.plot('#humidity-chart', [humi_data], {
    grid  : {
      hoverable  : true,
      borderColor: '#f3f3f3',
      borderWidth: 1,
      tickColor  : '#f3f3f3'
    },
    series: {
      shadowSize: 0,
      lines     : {
        show: true
      },
      points: {
        show: false
      }
    },
    lines : {
      fill : false,
      color: ['#3c8dbc']
    },
    yaxis : {
      show: true
    },
    xaxis : {
      show: true,
      mode: "time",
      timeformat: "%H:%M", // 24hr format
      tickSize: [1, "hour"], // tick every hour
      timezone: "browser"
    }
  })
  
  //Initialize tooltip on hover
  $('<div class="tooltip-inner" id="humi-chart-tooltip"></div>').css({
    position: 'absolute',
    display : 'none',
    opacity : 0.8
  }).appendTo('body')
  $('#humidity-chart').bind('plothover', function (event, pos, item) {

    if (item) {
      var x = new Date(item.datapoint[0]).toString(),
          y = item.datapoint[1].toFixed(2)

      $('#humi-chart-tooltip').html(x + '<br/>Humidity = ' + y + '%')
        .css({
          top : item.pageY + 5,
          left: item.pageX + 5
        })
        .fadeIn(200)
    } else {
      $('#humi-chart-tooltip').hide()
    }

  })
}

function loadRainChart(){

  var rainArr = sensorHistoryArray.map(item => [item.datetime, item.rain]);

  var rain_data = {
    data : rainArr,
    color: '#3c8dbc'
  }

  $.plot('#rain-chart', [rain_data], {
    grid  : {
      hoverable  : true,
      borderColor: '#f3f3f3',
      borderWidth: 1,
      tickColor  : '#f3f3f3'
    },
    series: {
      shadowSize: 0,
      lines     : {
        show: true
      },
      points: {
        show: false
      }
    },
    lines : {
      fill : false,
      color: ['#3c8dbc']
    },
    yaxis : {
      show: true
    },
    xaxis : {
      show: true,
      mode: "time",
      timeformat: "%H:%M", // 24hr format
      tickSize: [1, "hour"], // tick every hour
      timezone: "browser"
    }
  })
  
  //Initialize tooltip on hover
  $('<div class="tooltip-inner" id="rain-chart-tooltip"></div>').css({
    position: 'absolute',
    display : 'none',
    opacity : 0.8
  }).appendTo('body')
  $('#rain-chart').bind('plothover', function (event, pos, item) {

    if (item) {
      var x = new Date(item.datapoint[0]).toString(),
          y = item.datapoint[1]

      $('#rain-chart-tooltip').html(x + '<br/>Rain = ' + y)
        .css({
          top : item.pageY + 5,
          left: item.pageX + 5
        })
        .fadeIn(200)
    } else {
      $('#rain-chart-tooltip').hide()
    }

  })
}

function loadPressureChart(){

  var presArr = sensorHistoryArray.map(item => [item.datetime, item.pressure]);

  var pres_data = {
    data : presArr,
    color: '#3c8dbc'
  }

  $.plot('#pressure-chart', [pres_data], {
    grid  : {
      hoverable  : true,
      borderColor: '#f3f3f3',
      borderWidth: 1,
      tickColor  : '#f3f3f3'
    },
    series: {
      shadowSize: 0,
      lines     : {
        show: true
      },
      points: {
        show: false
      }
    },
    lines : {
      fill : false,
      color: ['#3c8dbc']
    },
    yaxis : {
      show: true
    },
    xaxis : {
      show: true,
      mode: "time",
      timeformat: "%H:%M", // 24hr format
      tickSize: [1, "hour"], // tick every hour
      timezone: "browser"
    }
  })
  
  //Initialize tooltip on hover
  $('<div class="tooltip-inner" id="pres-chart-tooltip"></div>').css({
    position: 'absolute',
    display : 'none',
    opacity : 0.8
  }).appendTo('body')
  $('#pressure-chart').bind('plothover', function (event, pos, item) {

    if (item) {
      var x = new Date(item.datapoint[0]).toString(),
          y = item.datapoint[1].toFixed(2)

      $('#pres-chart-tooltip').html(x + '<br/>Pressure = ' + y + 'kPa')
        .css({
          top : item.pageY + 5,
          left: item.pageX + 5
        })
        .fadeIn(200)
    } else {
      $('#pres-chart-tooltip').hide()
    }

  })
}

// this runs when page is ready
$(function () {
  
    //run stuff when page loads
    getSensorData();

  })

</script>
</body>
</html>
