import { Component, OnInit } from '@angular/core';
import {DomSanitizer} from "@angular/platform-browser";
import {ActivatedRoute} from "@angular/router";
import {ApiServices} from "../../services/api-services.service";
import {PluginService} from "../../services/plugin.service";
import { TranslateService } from 'ng2-translate';

@Component({
  selector: 'match-dota2-page',
  templateUrl: './match-dota2.component.html',
  styles: [`
    .left-cont .video-info .game-info img,
    .left-cont .recent-performance .performance-info .game-info .flag img{
      width:22px;
      height:22px;
    }

   .game-slider .slide .score-container .guest-team img {
     margin-right: auto;
   }

   .game-slider .slide .score-container .guest-team:after {
     right:55px;
   }

   .game-slider .slide .score-container .guest-team span {
      position:inherit;
   }

    .group-stage {
      position:relative;
    }

    .group-stage .schedule .body.collapsing {
      padding-top: 13px;
    }

    .no-padding {
      padding:0;
    }

    .group-stage .group a:hover,
    .group-stage .group.active  a {
      color: #fff;
    }

    .tab-content > .tab-pane {
      height: 0;
      display: block;
      overflow: hidden;
    }

    .tab-content > .tab-pane.active {
      height: auto;
    }

    .group-stage .group {
      margin-right: 8px;
    }

    .group-stage .group.in-group {
      width: 100%;
      margin: 0;
    }

    .group-stage .group a {
      border: none;
      background: transparent;
      color: #999999;
      text-decoration: none;
      text-transform: uppercase;
      white-space: nowrap;
      font-size: 10px;
    }

    .group-stage .nav-tabs {
      border:none;
    }

    .group .group-content {
      padding-right: 0 !important;
      clear: both;
    }

    .group-stage .schedule > div {
       padding:0 !important;
    }

    .slick-slider .slick-list {
        padding-right: 0;
    }

    .full-width {
       width:100%;
    }

    .p-right-add {
      padding-right: 4px;
    }
    .p-right {
      padding-right: 0px !important;
    }
  `]
})
export class MatchDota2Component implements OnInit {

  firstLoad:boolean = true;
  stream: any;
  matchId: number;
  gameType: string = 'dota2';
  player1Id: number;
  player2Id: number;
  dateNow = Math.floor(Date.now());

  // chart
  options1: Object;
  options2: Object;
  chart1: any;
  chart2: any;

  // past matches
  match = {
    streams: [],
    match_games: [],
    match: {
      opponent1id: 0,
      opponent2id: 0
    },
    opponent1_performance: {
      length: 0
    },
    opponent2_performance: {
      length: 0
    },
    opponent1_map_breakdown: [],
    opponent2_map_breakdown: [],
    opponent1_members: [],
    opponent2_members: []
  };

  opp1Wins = [];
  opp1Loses = [];
  opp1WinPercentage = [];
  opp1currentYearWins = 0;
  opp1currentYearLosses = 0;
  opp1currentYearDraws = 0;
  opp2currentYearWins = 0;
  opp2currentYearLosses = 0;
  opp2currentYearDraws = 0;
  opp2Wins = [];
  opp2Loses = [];
  opp2WinPercentage = [];
  opp1winRate;
  opp2winRate;
  prematchBets = {};
  opponent1Score = 0;
  opponent2Score = 0;
  currentYear;
  showPlayoff = false;

  counter = 30;
  categories = [];
  categories2 = [];
  translatedCategories = [];
  translatedCategories2 = [];
  monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  heroes = [];

  constructor(
    private _apiServices: ApiServices,
    private _activatedRoute: ActivatedRoute,
    private _sanitizer: DomSanitizer,
    private  _pluginService: PluginService,
    private _translateService: TranslateService
  ) { }

  playoffDisplay (val: string) {
    this.showPlayoff = (val.toLowerCase() === 'playoffs') ? true : false;
  }

  ngOnInit() {

    this._activatedRoute.params
      .map(par => {
        this.matchId = par['id'];
      }).subscribe(par => {
      this._apiServices.getMatch(this.matchId, this.gameType).subscribe(res=>{
        this.match = res;
        // stream
          if(this.match.opponent1_members[0]){
            this.player1Id = this.match.opponent1_members[0].id;
          }
          if(this.match.opponent2_members[0]){
            this.player2Id = this.match.opponent2_members[0].id;
          }

        this._pluginService.slickSliderParticipantsGame('.game-slider.dota-slider');
        this._pluginService.slickSliderGroup();

        if(this.match.streams[0]){
          this.stream = this._sanitizer.bypassSecurityTrustHtml(this.match.streams[0].embed);
        }

        // getting final score
        var opp1Id = this.match.match.opponent1id;
        var opp2Id = this.match.match.opponent2id;
        var matchGamesRef = this.match.match_games;
        // looping true rounds
        if(matchGamesRef){
            for(var i = 0; matchGamesRef.length > i; i++){
              if(matchGamesRef[i].winner == opp1Id){
                this.opponent1Score++;
              }else if(matchGamesRef[i].winner == opp2Id) {
                this.opponent2Score++;
              }
            }
        }
        // looping true months to get categories for chart
        this.currentYear = this.match.opponent1_performance[this.match.opponent1_performance.length - 1].year;
        for(var i = this.match.opponent1_performance.length - 1; i > 0; i--){
          // calculating for last five months
          if(i > this.match.opponent1_performance.length - 6){
            // getting month out of timestamp
            this.categories.push(this.monthNames[this.match.opponent1_performance[i].month - 1]);
            this.opp1Wins.push(this.match.opponent1_performance[i].wins);
            this.opp1Loses.push(-this.match.opponent1_performance[i].loses);
            this.opp1WinPercentage.push(Math.round((this.match.opponent1_performance[i].wins * 100) / (this.match.opponent1_performance[i].wins + this.match.opponent1_performance[i].loses + this.match.opponent1_performance[i].draws)));
          }

          if(this.currentYear == this.match.opponent1_performance[i].year){
            // calculating for whole year
            this.opp1currentYearWins += this.match.opponent1_performance[i].wins;
            this.opp1currentYearLosses += this.match.opponent1_performance[i].loses;
            this.opp1currentYearDraws += this.match.opponent1_performance[i].draws;
          }
        }

        this.categories.reverse();
        this.opp1Wins.reverse();
        this.opp1Loses.reverse();
        this.opp1WinPercentage.reverse();

        // for second player
        for(var i = this.match.opponent2_performance.length - 1; i > 0; i--){
          // calculating for last five months
          if(i > this.match.opponent2_performance.length - 6){
            this.opp2Wins.push(this.match.opponent2_performance[i].wins);
            this.opp2Loses.push(-this.match.opponent2_performance[i].loses);
            this.categories2.push(this.monthNames[this.match.opponent2_performance[i].month - 1]);
            this.opp2WinPercentage.push(Math.round((this.match.opponent2_performance[i].wins * 100) / (this.match.opponent2_performance[i].wins + this.match.opponent2_performance[i].loses + this.match.opponent2_performance[i].draws)));
          }

          if(this.currentYear == this.match.opponent2_performance[i].year){
            // calculating for whole year
            this.opp2currentYearWins += this.match.opponent2_performance[i].wins;
            this.opp2currentYearLosses += this.match.opponent2_performance[i].loses;
            this.opp2currentYearDraws += this.match.opponent2_performance[i].draws;
          }
        }

        this.categories2.reverse();
        this.opp2Wins.reverse();
        this.opp2Loses.reverse();
        this.opp2WinPercentage.reverse();

        // win percentage for team charts
        this.opp1winRate = (this.opp1currentYearWins * 100) /(this.opp1currentYearWins + this.opp1currentYearLosses + this.opp1currentYearDraws);
        this.opp2winRate = (this.opp2currentYearWins * 100) /(this.opp2currentYearWins + this.opp2currentYearLosses + this.opp2currentYearDraws);

        // calling highchart plugin
        this.options1 = {
          chart: {
            animation: false,
            backgroundColor: '#e6e6e6',
            height: 205,
            marginTop: 25,
            marginBottom: 25,
            marginLeft: 35,
            marginRight: 35
          },
          title: {
            text: ''
          },

          xAxis: {
            categories: this.categories,
            offset: -4,
            opposite: true,
            lineColor: '#e6e6e6',
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

          yAxis: [{
            offset: -10,
            title: {
              text: ''
            },
            labels: {
              style: {
                fontSize: '8',
                color: '#000'
              },
              formatter: function() {
                return Math.abs(this.value) + '-';
              }
            },
            gridLineColor: '#f2f2f2',
            gridLineWidth: 3,
            tickAmount: 6
          }, {
            opposite: true,
            offset: -5,
            title: {
              align: 'high',
              offset: 0,
              text: '%',
              rotation: 0,
              y: -10,
              x: 21,
              style: {
                color: '#000'
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
            tickAmount: 5,
            max: 100,
            min:0
          }],

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
            data: this.opp1Wins,
            dragMinY: 0,
            type: 'column',
            minPointLength: 2,
            showInLegend: false,
            pointWidth: 28,
            color: "#ff9200",
          }, {
            name: "Losses",
            data: this.opp1Loses,
            dragMinY: 0,
            type: 'column',
            minPointLength: 2,
            color: "#808080",
            showInLegend: false,
            pointWidth: 28,
          },
            //   {
            //   yAxis:1,
            //   name: "%",
            //   data: this.opp1WinPercentage,
            //   color: "#000",
            //   showInLegend: false,
            // }
          ]
        };

        this.options2 = {
          chart: {
            animation: false,
            backgroundColor: '#e6e6e6',
            height: 205,
            marginTop: 25,
            marginBottom: 25,
            marginLeft: 35,
            marginRight: 35
          },
          title: {
            text: ''
          },

          xAxis: {
            categories: this.categories,
            offset: -4,
            opposite: true,
            lineColor: '#e6e6e6',
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

          yAxis: [{
            offset: -10,
            title: {
              text: ''
            },
            labels: {
              style: {
                fontSize: '8',
                color: '#000'
              },
              formatter: function() {
                return Math.abs(this.value) + '-';
              }
            },
            gridLineColor: '#f2f2f2',
            gridLineWidth: 3,
            tickAmount: 6
          }, {
            opposite: true,
            offset: -5,
            title: {
              align: 'high',
              offset: 0,
              text: '%',
              rotation: 0,
              y: -10,
              x: 21,
              style: {
                color: '#000'
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
            tickAmount: 5,
            max: 100,
            min:0
          }],

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
            data: this.opp2Wins,
            dragMinY: 0,
            type: 'column',
            minPointLength: 2,
            showInLegend: false,
            pointWidth: 28,
            color: "#ff9200"
          }, {
            name: "Losses",
            data: this.opp2Loses,
            dragMinY: 0,
            type: 'column',
            minPointLength: 2,
            color: "#808080",
            showInLegend: false,
            pointWidth: 28,
          },
            //   {
            //   yAxis:1,
            //   name: "%",
            //   data: this.opp2WinPercentage,
            //   color: "#000",
            //   showInLegend: false,
            // }
          ]
        };

        // making lang menu expand
        setTimeout(()=>{
          if(this.firstLoad){
            jQuery('.leng-cont .lang ul li:first-child').addClass('active');
            // toggling language bar
            jQuery('.video-info .lang ul').click(function(){
              jQuery(this).toggleClass('open');
            });
            this.firstLoad = false;
          }
        },1000);

      });

      this._apiServices.getMatchOdds(this.gameType, this.matchId).subscribe((res)=>{
        this.prematchBets = res;
      });

      this._apiServices.getHeros().subscribe((res)=>{
        this.heroes = res.heroes;
      });

      var that = this;
      setInterval(function(){
        if(that.counter > 1){
          that.counter -= 1;
        }else {
          that.counter = 30;
          that._apiServices.getMatchOdds(that.gameType, that.matchId).subscribe((res)=>{
            that.prematchBets = res;
          });
        }
      }, 1000);

    });

    // watch for lang changes so it can update labels on charts
      this._translateService.onLangChange.subscribe((event) => {
        if (this.chart1 || this.chart2) {
          let that = this;
          this.translateChartLabels(1);
          this.translateChartLabels(2);
          this.updateChartCategories(this.chart1, this.translatedCategories);
          this.updateChartCategories(this.chart2, this.translatedCategories2);
        }
      });

  }

  // adding active state to groups and stages
  createActive(event: any) {
    jQuery(event.target).parents('.group').addClass('active');
    jQuery(event.target).parents('.group').parent('li').siblings('li').each(function(){
      jQuery(this).find('.group').removeClass('active');
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
  saveInstance(chart, chartInstance) {
    if (chart === 'chart1') {
      this.chart1 = chartInstance;
      this.translateChartLabels(1);
      this.updateChartCategories(this.chart1, this.translatedCategories);
    } else {
      this.chart2 = chartInstance;
      this.translateChartLabels(2);
      this.updateChartCategories(this.chart2, this.translatedCategories2);
    }
   }
  // translating labels on chart
  translateChartLabels(catType: number) {
    if (catType === 1) {
      for (let i = 0; i < this.categories.length; i++) {
          this.translatedCategories[i] = this.getTranslation(this.categories[i]);
        }
    }
    if (catType === 2) {
      for (let y = 0; y < this.categories2.length; y++) {
          this.translatedCategories2[y] = this.getTranslation(this.categories2[y]);
        }
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

  // loading stream selected on lang menu
  loadStream(event:any, embed:string) {
    // only if li isn't active change stream, if it's active let it stay the same
    if(event.parentElement.className == 'lang-flag'){
      this.stream = this._sanitizer.bypassSecurityTrustHtml(embed);
      jQuery('.video-info .lang ul li').removeClass('active');
      event.parentElement.className = 'lang-flag active';
    }
  }
}
