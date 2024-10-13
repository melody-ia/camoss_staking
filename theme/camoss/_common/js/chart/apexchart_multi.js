var options = {
    series: chart_data,
    chart: {
    height: 390,
    type: 'radialBar',
  },
  plotOptions: {
    radialBar: {
      offsetY: 0,
      startAngle: 0,
      endAngle: 270,
      hollow: {
        margin: 5,
        size: '30%',
        background: 'transparent',
        image: undefined,
      },
      track: {
            background: "#ededed",
            strokeWidth: '95%',
            margin: 5, // margin is in pixels
            
        },
      dataLabels: {
        name: {
          show: false,
        },
        value: {
          show: true,
          fontSize:'15px',
          offsetY: 0,
          formatter: function (w) {
            return w*2.5 + '%';
          },
        }
      },
      barLabels: {
        enabled: true,
        useSeriesColors: true,
        offsetX: -8,
        fontSize: '15px',
        formatter: function(seriesName, opts) {
          return seriesName + ":  " + (opts.w.globals.series[opts.seriesIndex]*2.5)+'%'
        },
      },
    }
  },
  colors: chart_color,
  labels: chart_label,
  responsive: [{
    breakpoint: 480,
    options: {
      legend: {
          show: false
      }
    }
  }]
  };
