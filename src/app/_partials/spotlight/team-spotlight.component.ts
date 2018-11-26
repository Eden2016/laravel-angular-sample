import {Component, OnInit, Input} from '@angular/core';
import {ApiServices} from "../../services/api-services.service";
import { TranslateService } from 'ng2-translate';

@Component({
  selector: 'app-team-spotlight',
  templateUrl: 'team-spotlight.component.html',
  styles: [`
    .stat-container .stat-in .heroes .heroes-cont .hero-text > .row div {
        display:inline-block;
        width:33%;  
        vertical-align:top;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .stat-container .stat-in {
        min-height: 350px;
    }           
  `]
})

export class TeamSpotlightComponent implements OnInit {

  options: Object;
  @Input() teamId;
  categories = [];
  translatedCategories = [];
  chart1: any;
  wins = [];
  loses = [];
  winPercentage = [];
  monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

  team = {
    monthly_performance: []
  };

  constructor(
    private _apiService: ApiServices,
    private _translateService: TranslateService
  ) { }

  ngOnInit() {
    this._apiService.getTeam(this.teamId).subscribe((res)=>{
      this.team = res;
      var currentYear = this.team.monthly_performance[this.team.monthly_performance.length - 1].year;
      // looping true months to get categories for chart
      for(var i = this.team.monthly_performance.length - 1; i >= 0; i--){
        // calculating for last five months
        if(i > this.team.monthly_performance.length - 7){
          this.wins.push(this.team.monthly_performance[i].wins);
          this.loses.push(-this.team.monthly_performance[i].loses);
          this.categories.push(this.monthNames[(+this.team.monthly_performance[i].month)-1]);
          this.winPercentage.push(Math.round((this.team.monthly_performance[i].wins * 100) / (this.team.monthly_performance[i].wins + this.team.monthly_performance[i].loses + this.team.monthly_performance[i].draws)));
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
          height: 136,
          marginTop: 0,
          marginBottom: 0,
          marginLeft: 0,
          marginRight: 0
        },
        title: {
          text: ''
        },

        xAxis: {
          categories: this.categories,
          offset: -20,
          opposite: true,
          lineColor: '#FFF',
          tickLength: 0,
          labels: {
            rotation: 0,
            style: {
              fontSize: '9px',
              fontFamily: 'Arial, sans-serif',
              fontWeight: '900 !important'
            }
          },
        },

        credits: {
          enabled: false
        },

        yAxis: {
          title: {
            text: ''
          },
          style: {
            fontSize: '8',
            color: '#000'
          },
          labels: {
            rotation: 0,
            style: {
              fontSize: '9',
              fontFamily: 'Arial, sans-serif',
              fontWeight: '900 !important',
              color: '#000',
            }
          },
          gridLineColor: '#fff'
        },

        plotOptions: {
          series: {
            borderWidth: 0,
            point: {
              stickyTracking: false
            }
          },
          column: {
            stacking: 'normal'
          },
          line: {
            lineWidth: 2.5,
            marker: {
              enabled: false
            },
            states: {
              hover: {
                enabled: false
              }
            }
          }
        },

        tooltip: {
          yDecimals: false,
          formatter: function () {
            return Math.abs(this.y) + ' ' + this.series.name;
          }

        },

        series: [{
          name: "Wins",
          data: this.wins,
          dragMinY: 0,
          type: 'column',
          minPointLength: 2,
          showInLegend: false,
          pointWidth: 28,
          color: "#ff9200"
        }, {
          name: "Losses",
          data: this.loses,
          dragMinY: 0,
          type: 'column',
          minPointLength: 2,
          color: "#808080",
          showInLegend: false,
          pointWidth: 28,
        },
        //   {
        //   name: "%",
        //   data: this.winPercentage,
        //   color: "#000",
        //   showInLegend: false,
        // }
        ]
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
