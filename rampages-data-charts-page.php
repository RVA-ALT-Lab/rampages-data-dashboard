<div class="wrap" id="rampages-data-dashbard">
  <h2>Rampages Data Dashboard</h2>

  <select name="" id="chart-type" v-model="chartType">
    <option value="Aggregate">Aggregate</option>
    <option value="Linear">Linear</option>
  </select>


  <h3>User Registrations</h3>
    <div id="user_registrations" style="height:500px;background:white; ">
    </div>
  <h3>Blog Registrations</h3>
    <div id="blog_registrations" style="height:500px; background:white; ">
    </div>
  <h3>Blog Posts</h3>
    <div id="blog_posts" style="height:500px;background:white; ">
    </div>
  <h3>Blog Comments</h3>
    <div id="blog_comments" style="height:500px;background:white; ">
    </div>
</div>

<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>

<script>

const app = new Vue({
  el: '#rampages-data-dashbard',
  data: {
      user_registrations: [],
      blog_registrations: [],
      blog_posts: [],
      blog_comments: [],
      chartType: 'Linear'
  },
  computed : {
    userRegistrations () {
      return this.user_registrations.map(item => {
        return {
          date: `${item.M.length === 1 ? "0" + item.M : item.M}-01-${item.Y}`,
          total: parseInt(item.total)
        }
      })
    },
    blogRegistrations () {
      return this.blog_registrations.map(item => {
        return {
          date: `${item.M.length === 1 ? "0" + item.M : item.M}-01-${item.Y}`,
          total: parseInt(item.total)
        }
      })
    },
    blogPosts () {
      return this.blog_posts
      .filter(item => parseInt(item.Y) >= 2014 )
      .map(item => {
        return {
          date: `${item.M.length === 1 ? "0" + item.M : item.M}-01-${item.Y}`,
          total: parseInt(item.total)
        }
      })
    },
    blogComments () {
      return this.blog_comments.map(item => {
        return {
          date: `${item.M.length === 1 ? "0" + item.M : item.M}-01-${item.Y}`,
          total: parseInt(item.total)
        }
      })
    }
  },
  watch: {
    user_registrations () {
      this.makeLineChart("user_registrations", this.userRegistrations)
    },
    blog_registrations () {
      this.makeLineChart("blog_registrations", this.blogRegistrations)
    },
    blog_posts () {
      this.makeLineChart("blog_posts", this.blogPosts)
    },
    blog_comments () {
      this.makeLineChart("blog_comments", this.blogComments)
    }
  },
  methods: {
    refreshData () {
      fetch('/wp-json/rampages-data/v1/data')
        .then(data => data.json())
        .then(json => {
          this.user_registrations = json['user_registrations']
          this.blog_registrations = json['blog_registrations']
          this.blog_posts = json['blog_posts']
          this.blog_comments = json['blog_comments']

        })
        .catch(error => console.log(error))
    },
    makeLineChart (chartdiv, data) {
      console.log(chartdiv)
      console.log(data)
      var chart = AmCharts.makeChart(chartdiv, {
        "type": "serial",
        "theme": "none",
        "marginRight": 40,
        "marginLeft": 40,
        "autoMarginOffset": 20,
        "mouseWheelZoomEnabled":true,
        "dataDateFormat": "MM-DD-YYYY",
        "valueAxes": [{
            "id": "v1",
            "axisAlpha": 0,
            "position": "left",
            "ignoreAxisWidth":true
        }],
        "balloon": {
            "borderThickness": 1,
            "shadowAlpha": 0
        },
        "graphs": [{
            "id": "g1",
            "balloon":{
              "drop":true,
              "adjustBorderColor":false,
              "color":"#ffffff"
            },
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "bulletSize": 5,
            "hideBulletsCount": 50,
            "lineThickness": 2,
            "title": "red line",
            "useLineColorForBulletBorder": true,
            "valueField": "total",
            "balloonText": "<span style='font-size:18px;'>[[value]]</span>"
        }],
        "chartScrollbar": {
            "graph": "g1",
            "oppositeAxis":false,
            "offset":30,
            "scrollbarHeight": 80,
            "backgroundAlpha": 0,
            "selectedBackgroundAlpha": 0.1,
            "selectedBackgroundColor": "#888888",
            "graphFillAlpha": 0,
            "graphLineAlpha": 0.5,
            "selectedGraphFillAlpha": 0,
            "selectedGraphLineAlpha": 1,
            "autoGridCount":true,
            "color":"#AAAAAA"
        },
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha":1,
            "cursorColor":"#258cbb",
            "limitToGraph":"g1",
            "valueLineAlpha":0.2,
            "valueZoomable":true
        },
        "valueScrollbar":{
          "oppositeAxis":false,
          "offset":50,
          "scrollbarHeight":10
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": true,
            "dashLength": 1,
            "minorGridEnabled": true
        },
        "export": {
            "enabled": true
        },
        "dataProvider": data
    });

    }
  },
  created () {
    this.refreshData()
  },
  mounted () {
    console.log('Vue is working')
    this.makeLineChart("user_registrations", this.userRegistrations)
  },
  beforeUpdate () {
    console.log('VM before update called')
    this.makeLineChart("user_registrations", this.userRegistrations)
  },
  updated () {
    console.log('VM updated')
    this.makeLineChart("user_registrations", this.userRegistrations)
  }
})


</script>