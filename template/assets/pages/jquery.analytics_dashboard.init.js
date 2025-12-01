/**
 * Theme: Dastyle - Responsive Bootstrap 4 Admin Dashboard
 * Author: Mannatthemes
 * Analytics Dashboard Js
 */

var naira = Intl.NumberFormat('en-NG');

var options = {
  chart: {
      height: 350,
      type: 'bar',
      toolbar: {
          show: false
      }
  },
  plotOptions: {
      bar: {
          horizontal: false,
          columnWidth: '30%',
      },
  },
  dataLabels: {
      enabled: false
  },
  stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
  },
  colors: ["rgba(42, 118, 244, .18)"],
  series: [{
      name: 'Sales',
      data: [1150234, 650765, 980555, 1234567, 5000000, 6000000, 205000]
  }],
  xaxis: {
      categories: ['Mon','Tue', 'Wed', 'Thr', 'Fri', 'Sat', 'Sun'],
      axisBorder: {
        show: false,
        color: '#bec7e0',
      },  
      axisTicks: {
        show: false,
        color: '#bec7e0',
      },    
  },
  legend: {
    offsetY: 6,
  },
  yaxis: {
      title: {
          text: 'Amount',
      },
      labels: {
        formatter: function (val) {
            return "N" + naira.format(val);
        }
      }
  },
  fill: {
      opacity: 1

  },
  grid: {
      row: {
          colors: ['transparent'], // takes an array which will be repeated on columns
          opacity: 0.2,           
      },
      strokeDashArray: 2.5,
  },
  tooltip: {
      y: {
          formatter: function (val) {
              return "N" + naira.format(val);
          }
      }
  }
}

var chart = new ApexCharts(
  document.querySelector("#ana_dash_1"),
  options
);

chart.render();