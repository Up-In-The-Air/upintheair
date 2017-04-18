var CLASS_MAP = {
  0: 'Economy',
  1: 'Economy+',
  2: 'Business',
  3: 'First'
};
var PURPOSE_MAP = {
  0: 'Leisure',
  1: 'Business',
  2: 'Crew',
  3: 'Others'
};
var SEAT_MAP = {
  0: 'Window',
  1: 'Middle',
  2: 'Aisle'
};

var BAR_CHART_OPTION = {
  title: { text: '' },
  tooltip: {
    trigger: 'axis',
    axisPointer: { type: 'shadow' }
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '3%',
    containLabel: true
  },
  yAxis: {
    type: 'value',
    boundaryGap: [0, 0.01]
  },
  xAxis: {
    type: 'category',
    data: []
  },
  series: [{
    name: '',
    type: 'bar',
    data: []
  }],
  itemStyle: {
    normal: {
      color: '#ffb74d',
      shadowBlur: 200,
      shadowColor: 'rgba(0, 0, 0, 0.2)'
    }
  }
};

var PIE_CHART_OPTION = {
  title: {
    text: '',
    left: 'center',
    top: 0,
    textStyle: { color: '#222' }
  },
  tooltip: {
    trigger: 'item',
    formatter: "{a} <br/>{b} : {c} ({d}%)"
  },
  visualMap: {
    show: false,
    min: 0,
    max: 150,
    inRange: { colorLightness: [0, 1] }
  },
  series: [{
    name: '',
    type: 'pie',
    radius: '60%',
    center: ['50%', '50%'],
    data: [],
    roseType: 'angle',
    label: { normal: { textStyle: { color: 'rgba(0, 0, 0, 0.3)' } } },
    labelLine: {
      normal: {
        lineStyle: { color: 'rgba(0, 0, 0, 0.3)' },
        smooth: 0.2,
        length: 10,
        length2: 20
      }
    },
    itemStyle: {
      normal: {
        color: '#00bcd4',
        shadowBlur: 200,
        shadowColor: 'rgba(0, 0, 0, 0.5)'
      }
    },
    animationType: 'scale',
    animationEasing: 'elasticOut',
    animationDelay: function (idx) { return Math.random() * 200; }
  }]
};

var LINE_CHART_OPTION = {
  title: { text: '' },
  tooltip: { trigger: 'axis' },
  xAxis:  {
    type: 'category',
    data: []
  },
  yAxis: { type: 'value' },
  series: [{
    name: '',
    type: 'line',
    data: [],
    markPoint: {
      data: [
        {type: 'max', name: 'Maximum'},
        {type: 'min', name: 'Minimum'}
      ]
    },
    markLine: {
      data: [{type: 'average', name: 'Average'}]
    }
  }]
};

// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
      totalFlight: 0,
      totalDistance: 0
    }
  },
  ready: function() {
    // Auto login
    var cookies = document.cookie.split('; ');
    var cookieObj = {};
    cookies.forEach(function(cookieStr) {
      var cookieName = cookieStr.split('=')[0];
      var cookieContent = cookieStr.split('=')[1];
      cookieObj[cookieName] = cookieName === 'upintheairAuth' ? JSON.parse(cookieContent) : cookieContent;
    });

    var _this = this;
    if (cookieObj.hasOwnProperty('upintheairAuth')) {
      $.ajax({
        method: 'POST',
        url: 'api/auto_login.php',
        data: cookieObj['upintheairAuth'],
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            location.href = '/';
            return;
          }
          _this.user = {
            firstName: resp.data.first_name,
            lastName: resp.data.last_name,
            email: resp.data.email,
            id: resp.data.id
          };

          _this.getDashboardStat();

          _this.drawDistributionChart('class', CLASS_MAP);
          _this.drawDistributionChart('seat', SEAT_MAP);
          _this.drawDistributionChart('purpose', PURPOSE_MAP);

          _this.drawRankingChart('airport');
          _this.drawRankingChart('airline');
          _this.drawRankingChart('aircraft');

          _this.drawFlightPerYear();
        },
        error: function() { location.href = '/'; }
      });
    }
  },
  methods: {
    getDashboardStat: function() {
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/get_basic_stat.php',
        data: { user_id: this.user.id },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          var increaseFlightJob = setInterval(function() {
            if (_this.totalFlight === parseInt(resp.data.total_flight)) {
              clearInterval(increaseFlightJob);
            } else {
              _this.totalFlight += 1;
            }
          }, 50);
          var totalDistance = (resp.data.total_distance * 0.000621371192).toFixed(0);
          var increaseDistanceJob = setInterval(function() {
            if (_this.totalDistance > parseInt(totalDistance) - 131) {
              _this.totalDistance = totalDistance;
              clearInterval(increaseDistanceJob);
            } else {
              _this.totalDistance += 131;
            }
          }, 1);
        }
      });
    },
    drawDistributionChart: function(name, map) {
      $.ajax({
        method: 'GET',
        url: 'api/flight_num_' + name + '.php',
        data: { user_id: this.user.id },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          PIE_CHART_OPTION.title.text = name.toUpperCase();
          PIE_CHART_OPTION.series[0].name = name[0].toUpperCase() + name.substring(1);
          var chartData = [];
          var count = 0;
          resp.data.forEach(function(category) {
            chartData.push({
              value: category.count,
              name: map[parseInt(category.category)]
            });
            count += category.count;
          });
          PIE_CHART_OPTION.series[0].data = chartData;
          PIE_CHART_OPTION.visualMap.max = count;
          var myChart = echarts.init(document.getElementById(name + '-chart'));
          myChart.setOption(PIE_CHART_OPTION);
        }
      });
    },
    drawRankingChart: function(name) {
      $.ajax({
        method: 'GET',
        url: 'api/get_' + name + '_ranking.php',
        data: {
          user_id: this.user.id,
          limit: 10
        },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          BAR_CHART_OPTION.title.text = name.toUpperCase() + ' RANKING';
          BAR_CHART_OPTION.series[0].name = 'Frequency';
          var categories = [];
          var frequencies = []
          resp.data.forEach(function(row) {
            categories.push(row.iata)
            frequencies.push(row.frequency)
          });
          BAR_CHART_OPTION.xAxis.data = categories.reverse();
          BAR_CHART_OPTION.series[0].data = frequencies.reverse();
          var myChart = echarts.init(document.getElementById(name + '-chart'));
          myChart.setOption(BAR_CHART_OPTION);
        }
      });
    },
    drawFlightPerYear: function() {
      $.ajax({
        method: 'GET',
        url: 'api/get_flight_per_year.php',
        data: {
          user_id: this.user.id,
          limit: 10
        },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          LINE_CHART_OPTION.title.text = 'FLIGHT PER YEAR';
          LINE_CHART_OPTION.series[0].name = 'Flights';
          var categories = [];
          var flights = []
          for (var i = 0; i < resp.data.length; i++) {
            categories.push(resp.data[i].category);
            flights.push(resp.data[i].count);
            while (i + 1 < resp.data.length && parseInt(resp.data[i+1].category) != parseInt(categories[categories.length-1]) + 1) {
              categories.push((parseInt(categories[categories.length-1]) + 1).toString());
              flights.push('0');
            }
          }
          categories = categories.slice(Math.max(categories.length - 10, 1))
          flights = flights.slice(Math.max(flights.length - 10, 1))
          LINE_CHART_OPTION.xAxis.data = categories;
          LINE_CHART_OPTION.series[0].data = flights;
          var myChart = echarts.init(document.getElementById('flight-year-chart'));
          myChart.setOption(LINE_CHART_OPTION);
        }
      });
    },
    onLogoutClick: function() {
      $.ajax({
        method: 'POST',
        url: 'api/log_out.php',
        data: { user_id: this.user.id },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          document.cookie = 'upintheairAuth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
          location.href = '/';
        }
      });
    }
  }
});
