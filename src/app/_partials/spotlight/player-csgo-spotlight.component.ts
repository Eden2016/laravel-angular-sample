import {Component, OnInit, Input} from '@angular/core';
import {ApiServices} from "../../services/api-services.service";
import { TranslateService } from 'ng2-translate';

@Component({
  selector: 'app-player-csgo-spotlight',
  templateUrl: './player-csgo-spotlight.component.html',
  styles: []
})
export class PlayerCsgoSpotlightComponent implements OnInit {

  @Input() playerId;
  @Input() game;
  @Input() color;
  chart1: any;
  options: Object;
  categories = [];
  translatedCategories = [] ;
  currentYearWins:number = null;
  currentYearLosses:number = null;
  wins = [];
  loses = [];
  winPercentage = [];
  monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

  player = {
    monthly_performance: []
  };

  constructor(
    private _apiService: ApiServices,
    private _translateService: TranslateService
  ) { }

  ngOnInit() {

    this._apiService.getPlayer(this.game, this.playerId).subscribe((res)=>{

      this.player = res.result;

      if(this.player.monthly_performance.length){
        var currentYear = this.player.monthly_performance[this.player.monthly_performance.length - 1].year;
      }

      // looping true months to get categories for chart
      for(var i = this.player.monthly_performance.length - 1; i >= 0; i--){
        // calculating for last five months
        if(i>this.player.monthly_performance.length - 6){
          this.wins.push(this.player.monthly_performance[i].wins);
          this.loses.push(-this.player.monthly_performance[i].loses);
          this.categories.push(this.monthNames[(+this.player.monthly_performance[i].month)-1]);
          this.winPercentage.push(Math.round((this.player.monthly_performance[i].wins * 100) / (this.player.monthly_performance[i].wins + this.player.monthly_performance[i].loses)));
        }
        // calculating for whole year
        if(currentYear == this.player.monthly_performance[i].year){
          this.currentYearWins += this.player.monthly_performance[i].wins;
          this.currentYearLosses += this.player.monthly_performance[i].loses;
        }
      }

      this.wins.reverse();
      this.loses.reverse();
      this.categories.reverse();
      this.winPercentage.reverse();
      // calling highchart plugin
      this.options = {

        chart: {
          animation: false,
          height: 180,
          backgroundColor: '#e6e6e6',
          padding: 20
        },

        labels: {
          enabled: false
        },

        title: {
          text: ''
        },

        xAxis: {
          lineWidth: 0,
          tickLength: 0,
          minPadding: .12,
          maxPadding: .12,
          minorTickLength: 0,
          minorGridLineWidth: 0,
          lineColor: 'transparent',
          gridLineDashStyle: 'ShortDash',
          gridLineColor: '#f2f2f2',
          gridLineWidth: 3,
          tickInterval: 1,

          labels: {
            enabled: false
          },

          crosshair: {
            width: 3,
            color: '#b3b3b3',
            dashStyle: 'ShortDash'
          },
        },

        yAxis: [{

          tickInterval: 10,
          tickAmount: 10,
          max: 40,
          min: -40,
          title: {
            text: null
          },
          labels: {
            style: {
              fontSize: '8',
              color: '#000'
            },
            formatter: function() {
              return Math.abs(this.value) + ' -';
            },
            x: -6,
            y: 3
          }
        },
          {
            gridLineColor: '#f2f2f2',
            gridLineWidth: 3,
            opposite: true,
            offset: 0,
            title: {
              align: 'high',
              offset: 5,
              text: '%',
              rotation: 0,
              y: -1,
              x: 15,
              style: {
                color: '#000',
                position: 'relative',
                top:'-3px'
              }
            },

            labels: {
              style: {
                fontSize: '8',
                color: '#000'
              },
              formatter: function() {
                return Math.abs(this.value);
              }
            },
            showLastLabel: false,
            tickAmount: 7,
            //tickInterval: 10,
            max: 100
          }],

        plotOptions: {
          line: {
            dataLabels: {
              enabled: true
            },
          }
        },

        credits: {
          enabled: false
        },

        tooltip: {
          yDecimals: false,
          enabled: false
        },

        series: [{
          gapSize: 1,
          dataLabels: {
            enabled: true,
            format: '{point.name}'
          },
          data: this.wins,
          showInLegend: false,
          color: this.color,
          marker: {
            symbol: 'circle'
          }
        },{
          gapSize: 1,
          data: this.loses,
          showInLegend: false,
          color: '#000',
          dataLabels: {
            enabled: false
          },
          marker: {
            symbol: 'circle'
          },
        },{
          name:"%",
          yAxis:1,
          gapSize: 1,
          data: this.winPercentage,
          showInLegend: false,
          color: '#808080',
          dataLabels: {
            enabled: false
          },
          marker: {
            symbol: 'circle'
          }

        }]
      };

    });

      // watch for lang changes so it can update labels on charts
      this._translateService.onLangChange.subscribe((event) => {
        if (this.chart1) {
          this.translateChartLabels();
          this.updateChartCategories(this.chart1, this.translatedCategories);
        }
      });

  }

// translating dynamic content
  getTranslation(word: string): string {
    let result;
    this._translateService.get(word).subscribe((res: string) => {
      result = res;
    });
    return result;
  }

  // saving instances of charts so we can access them and apply translations
  saveInstance(chartInstance) {
      this.chart1 = chartInstance;
      this.translateChartLabels();
      this.updateChartCategories(this.chart1, this.translatedCategories);
   }
  // translating labels on chart
  translateChartLabels() {
    for (let i = 0; i < this.categories.length; i++) {
      this.translatedCategories[i] = this.getTranslation(this.categories[i]);
    }
  }

  // updating  chart series after translations
  updateChartCategories(chart: any, update) {
      let that = this;
      // translating month labels
      chart.axes[0].update({
          categories: update
      });
      // translating tooltip
      chart.tooltip.options.formatter = function() {
          return Math.abs(this.y) + ' ' + that.getTranslation(this.series.name);
      };
  }

}
