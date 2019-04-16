jQuery(document).ready(function(){
  var el = $('#aqi-gauge');

  if (el.length > 0) {
    var AQIChart = Highcharts.chart('aqi-gauge', {
      chart: {
        type: 'gauge',
        events: {
          load: function () {
            // Display the chart label
            $('#aqi-gauge .highcharts-label').css('opacity', 1);
          },
        },
      },
      title: {
        text: ''
      },
      plotOptions: {
        gauge: {
          dataLabels: {
            y: 80,
            defer: true,
            verticalAlign: 'middle',
          },
          dial: {
            radius: '60%',
            borderWidth: 0,
            baseWidth: 30,
            topWidth: 1,
            baseLength: '10%', // of radius
            rearLength: '0'
          },
          pivot: {
            radius: 16, //make circle
            borderWidth: 0,
            backgroundColor: '#787878'
          }
        }
      },
      pane: {
        startAngle: -140,
        endAngle: 140,
        background: [{
          backgroundColor: '#1175cd',
          borderWidth: 50,
          className: 'highcharts-outer-pane'
        }, {
          backgroundColor: '#ffffff',
          borderWidth: 0,
          className: 'highcharts-inner-pane'
        }]
      },
      yAxis: {
        min: 1,
        max: 300,
        plotBands: [{
          from: 0,
          to: 50,
          thickness: 45,
          className: 'highcharts-color-green',
        },{
          from: 51,
          to: 100,
          thickness: 45,
          className: 'highcharts-color-yellow',
        },{
          from: 101,
          to: 150,
          thickness: 45,
          className: 'highcharts-color-orange',
        },{
        from: 151,
        to: 200,
        thickness: 45,
        className: 'highcharts-color-red',
      },{
        from: 201,
        to: 250,
        thickness: 45,
        className: 'highcharts-color-purple',
      },{
        from: 251,
        to: 300,
        thickness: 45,
        className: 'highcharts-color-maroon',
        }]
      },
      series: [{
        animation: false,
        data: [0],
      }]
    }, function showData(chart){
      
      if (local.aqi == '') {
        $("<p>We're sorry, this service is currently unavailable.</p>").appendTo(".aqi-status-description");
      }
      var point = chart.series[0].points[0];
      point.update(local.aqi.AQI);
    
      var number = local.aqi.Category.Number
    
      $(".aqi-status-time").text(local.aqi.parsedDate);
    
      $(".aqi-status-name").text(local.aqi.Category.Name);
    
      switch ( number ){
        case 1:
          $( ".aqi-status-good").clone().appendTo(".aqi-status-description")
          break
        case 2:
          $( ".aqi-status-moderate").clone().appendTo(".aqi-status-description")
          break
        case 3:
          $(".aqi-status-unhealthy-for-sensitive-groups").clone().appendTo(".aqi-status-description")
          break
        case 4:
          $(".aqi-status-unhealthy").clone().appendTo(".aqi-status-description")
          break
        case 5:
          $(".aqi-status-very-unhealthy").clone().appendTo(".aqi-status-description")
          break
        case 6:
          $(".aqi-status-hazardous").clone().appendTo(".aqi-status-description")
          break
      }
    
    });
  }
  
});
