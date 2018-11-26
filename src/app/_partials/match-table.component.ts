import { Component, OnInit, ViewChild, ElementRef, Input } from '@angular/core';
import {ApiServices} from '../services/api-services.service';
import { SharedService } from '../services/shared.service';
import {ActivatedRoute} from '@angular/router';
import { TranslateService } from 'ng2-translate';
import 'rxjs/add/operator/map';
import {forEach} from '@angular/router/src/utils/collection';
import {SbkService} from '../services/sbk.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-match-table',
  templateUrl: './match-table.component.html',
  styles: [ `
     .right-cont .right-part .bets-cont .bets-text {
          margin: 8px 0 0 7px;
          width:calc(100% - 7px);
     }

     .right-cont .right-part .bets-cont .bets-match {
          width:100%;
     }

     .right-cont .right-part .bets-cont .bets-match span {
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          -webkit-flex:1;
          -moz-flex:1;
          flex:1;
    }

    .right-cont .right-part .bets-cont h4:hover {
        text-overflow: clip;
     }

    .right-cont .right-part .bets-cont h4 {
          overflow: hidden;
          line-height: 11px;
          text-overflow: ellipsis;
    }

    .right-cont .left-part.match .play .time {
      white-space: nowrap;
      letter-spacing: -.5px;
    }

    .right-cont .right-part .bets-cont .bets-match img {
      -webkit-flex:1;
      -moz-flex:1;
      flex:1;
      max-width:22px;
    }

    .right-cont .in-play-matches-cont:after {
      content:'';
      display:block;
      clear:both;
    }

    .right-cont .in-play-matches-cont,
    .right-cont .starts-in-matches-cont{
      margin-bottom:17px;
    }

    .right-cont .main-title.top {
      margin-top:0 !important;
    }

    .right-cont .bets-math {
      height:auto !important;
    }

    .right-cont .bets-hand {
      width: 100%;
      padding-right: 10px;
      padding-left: 3px;
    }

    .right-cont .right-part .bets-cont .bets-hand .bets-first-hand,
    .right-cont .right-part .bets-cont .bets-hand .bets-second-hand {
      /*-webkit-justify-content:space-between;*/
      /*-moz-justify-content:space-between;*/
      /*justify-content:space-between;*/
      padding-left:0px;
    }

    .right-cont .right-part .bets-cont .bets-hand span,
    .right-cont .right-part .bets-cont .bets-ml {
      font-size: 0.6875em;
    }
    .orange {
      color:#ff9200;
    }

    a:hover {
      text-decoration: none;
    }

    @media (max-width: 768px) {
      #available-types {
        left: auto;
        right:0;
      }
    }
  `]
})
export class MatchTableComponent implements OnInit {

  @ViewChild ('selectedOdds') selectOdds: any;
  dateNow = Math.floor(Date.now());

  firstLoad: boolean = true;
  counter: number[] = [30, 90];
  showUpcomingBets = false;
  showLiveBets = false;
  tournamentId: number;
  furtherMatches;
  inPlayMatches;
  oddsTypeOptions: Array<any>;
  selectPlaceholder: string = '';
  selectLabel1: string = 'Decimal odds (EUR)';
  selectLabel2: string = 'Hong Kong odds';
  selectLabel3: string = 'Malay odds';
  selectLabel4: string = 'Indo odds';

  // for language parameters
  @Input() queryParams;

  constructor(
    private _apiService: ApiServices,
    private _route: ActivatedRoute,
    private _sharedService: SharedService,
    private _sbkService: SbkService,
    private _translateService: TranslateService
  ) {}

  // on init load default odds
  ngOnInit() {

    this.loadOdds(1, true, true);
    // interval for refreshing odds , counter 0 for live and 1 for upcoming matches
    let that = this;
    let oddsSelectedValue = this.selectOdds.value === undefined ? 1 : this.selectOdds.value;
    setInterval(function(){
      if (that.counter[0] > 1) {
        that.counter[0] -= 1;
      }else {
        that.counter[0] = 30;
        that.loadOdds(oddsSelectedValue, true, false);
      }

      if (that.counter[1] > 1) {
        that.counter[1] -= 1;
      }else {
        that.counter[1] = 90;
        that.loadOdds(oddsSelectedValue, false, true);
      }
    }, 1000);
    // translating odds menu
    this._translateService.get([this.selectLabel1, this.selectLabel2, this.selectLabel3, this.selectLabel4]).subscribe((res) => {
      this.selectLabel1 = res['Decimal odds (EUR)'] ? res['Decimal odds (EUR)'] : this.selectLabel1;
      this.selectLabel2 = res['Hong Kong odds'] ? res['Hong Kong odds'] : this.selectLabel2;
      this.selectLabel3 = res['Malay odds'] ? res['Malay odds'] : this.selectLabel3;
      this.selectLabel4 = res['Indo odds'] ? res['Indo odds'] : this.selectLabel4;
      this.selectPlaceholder = this.selectLabel1;
      // inittialasing odds menu
      this.oddsTypeOptions = [
        {value: '1', label: this.selectLabel1},
        {value: '2', label: this.selectLabel2},
        {value: '3', label: this.selectLabel3},
        {value: '4', label: this.selectLabel4}
      ];        
    });
  }

  loadOdds(value: number, inplay:boolean, further:boolean) {
    // on any dropdown change load selected odds
    // reset time
    this.dateNow = Math.floor(Date.now());
    // inplay is for retreiving odds for live and further for upcoming matches
    this._apiService.getMatches(value).subscribe((res) => {

      //  Get tournament id
      this._route.params.subscribe(params => {
        let id = (!isNaN(params['id'])) ? +params['id'] : 0;

        if (further) {

          let matches1 = [];

          forEach(res.further, function (obj, key) {
            if ((obj.dummy_match !== null && id === obj.dummy_match.stage_round.stage_format.stage.tournament.masked_id) || id === 0) {
              matches1.push(obj);
            }
          });

          this.furtherMatches = matches1;


        }
        if (inplay) {

          let matches2 = [];

          forEach(res.inPlay, function (obj, key) {
            if (obj.dummy_match !== null && id === obj.dummy_match.stage_round.stage_format.stage.tournament.masked_id || id === 0){
              matches2.push(obj);
            }
          });

          this.inPlayMatches = matches2;
          // if this is first time load setup first stream to play
          if(this.firstLoad){
            for(var i = 0; i < this.inPlayMatches.length; i++){
              if(this.inPlayMatches[i].dummy_match != null){
                this.setStream(this.inPlayMatches[i]);
                this.firstLoad = false;
                return true;
              }
            }
            this.firstLoad = false;
          }

        }

      });

    });
  }

  refreshOdds(counter:number){
    // refreshing odds on click or on interval
    // counter 0 is for live odds and counter 1 for upcoming
    let oddsSelectedValue = this.selectOdds.value === undefined ? 1 : this.selectOdds.value;

    if(counter == 0){
      this.counter[counter] = 30;
      this.loadOdds(oddsSelectedValue, true, false);
    }else {
      this.counter[counter] = 90;
      this.loadOdds(oddsSelectedValue, false, true);
    }

  }

  setStream(stream: Object) {
    this._apiService.setLiveStream(stream);
  }

  //Place a bet in the betslip
  public bet = (matchId, selectionId, odd, handicap, betTeam, title) => {
    var item;

    forEach(this.inPlayMatches, (obj, key) => {
      forEach(obj.events, (event, key) => {
        if (event.id === matchId) {
          item = event;
        }
      });
    });

    if (!item) {
      forEach(this.furtherMatches, (obj, key) => {
        forEach(obj.events, (event, key) => {
          if (event.id === matchId) {
            item = event;
          }
        });
      });
    }

    if (item) {
      item.selectionId = selectionId;
      item.odd = odd;
      item.handicap = handicap;
      item.betTeam = betTeam;
      item.stake = "";
      item.toWin = "";
      item.maxBet = 0;
      item.minBet = 0;
      item.title = title;

      item.odds = null;
      item.new_odds = null;
      item.streams = null;

      this._sbkService.storeSelection(item);
    }
    else {
      return false;
    }
  }
}
