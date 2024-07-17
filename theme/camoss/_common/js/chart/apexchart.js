var title_color = '';
var circle_color = [];
var pie_value_color = '';

if(getCookie('mode')){
  var Theme = getCookie('mode'); 
}else{
  var Theme = thisTheme;
}

if(Theme == 'white') {
    circle_color = ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'];
    title_color = '#333';
    pie_value_color = '#333';
} else if(Theme == 'dark') {
    circle_color = ['#2d7dcb', '#508a9f', '#5b6066', '#db2eb3','#266099'];
    title_color = '#fff'
    pie_value_color = '#fff';
}

function fix_value (val){
    var point = val/100;
    return Number(point.toFixed(2));
}

var options = {
series: [chart_data],
chart: {
    height: '360px',
    type: 'radialBar',
},
// colors: circle_color,
labels: ['Bonus'],
plotOptions: {
  radialBar: {
    startAngle: -135,
    endAngle: 225,
     hollow: {
      margin: 0,
      size: '60%',
      background: '#fff',
      image: undefined,
      imageOffsetX: 0,
      imageOffsetY: 0,
      position: 'front',
      dropShadow: {
        enabled: true,
        top: 3,
        left: 0,
        blur: 4,
        opacity: 0.24
      }
    },
    track: {
      background: '#e5e5e5',
      strokeWidth: '97%',
      margin: 0, // margin is in pixels
      dropShadow: {
        enabled: true,
        top: -3,
        left: 0,
        blur: 4,
        opacity: 0.35
      }
    },

    dataLabels: {
      show: true,
      name: {
        offsetY: -10,
        show: true,
        fontSize: '17px'
      },
      value: {
        /* formatter: function(val) {
          return parseInt(val);
        }, */
        color: '#111',
        fontSize: '36px',
        fontWeight: 600,
        show: true,
      }
    }
  }
},
fill: {
  type: 'gradient',
  gradient: {
    shade: 'dark',
    type: 'horizontal',
    shadeIntensity: 0.5,
    gradientToColors: ['#ABE5A1'],
    inverseColors: true,
    opacityFrom: 1,
    opacityTo: 1,
    stops: [0, 100]
  }
},
stroke: {
  lineCap: 'round'
},

responsive: [{
  breakpoint: 1000,
  options: {
    chart: {
    },
    legend: {
        offsetY: 8,
        position: 'bottom'
    }
  }
}]
};