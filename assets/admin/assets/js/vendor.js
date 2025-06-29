var ts2 = 1484418600000;
var dates = [];

var dataSeries = [
  [],
  Array.from({ length: 120 }, () => ({
    value: Math.floor(Math.random() * 50000000 + 20000000), // بين 20 و70 مليون
  })),
];

for (var i = 0; i < 120; i++) {
  ts2 += 86400000; // يوم بالمللي ثانية
  var innerArr = [ts2, dataSeries[1][i].value];
  dates.push(innerArr);
}

var options = {
  chart: {
    height: "auto",
    type: "area",
    stacked: false,
    zoom: {
      type: "x",
      enabled: true,
    },
    toolbar: {
      autoSelected: "zoom",
    },
  },
  dataLabels: {
    enabled: false,
  },
  series: [
    {
      name: "XYZ MOTORS",
      data: dates,
    },
  ],
  markers: {
    size: 0,
  },
  title: {
    text: "Stock Price Movement",
    align: "left",
  },
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.5,
      opacityTo: 0,
      stops: [0, 90, 100],
    },
  },
  yaxis: {
    min: 20000000,
    max: 250000000,
    labels: {
      formatter: function (val) {
        return (val / 1000000).toFixed(0);
      },
    },
    title: {
      text: "Price",
    },
  },
  xaxis: {
    type: "datetime",
  },
  tooltip: {
    shared: false,
    y: {
      formatter: function (val) {
        return (val / 1000000).toFixed(0);
      },
    },
  },
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
