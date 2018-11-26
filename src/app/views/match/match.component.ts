import { Component, OnInit } from '@angular/core';
import {ApiServices} from "../../services/api-services.service";
import {ActivatedRoute} from "@angular/router";
import {DomSanitizer} from "@angular/platform-browser";
import {PluginService} from "../../services/plugin.service";
import * as jQuery from 'jquery';

@Component({
  selector: 'match-page',
  templateUrl: './match.component.html',
  styles: [`
    .left-cont .video-info .game-info img,
    .left-cont .recent-performance .performance-info .game-info .flag img {
      width:22px;
      height:22px;
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
      width: auto;
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
      padding: 0 8px;
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
export class MatchComponent implements OnInit {
  firstLoad:boolean = true;
  //video
  stream:any;

  gameType: string = 'all';
  gameId: number;

  //chart

  showPlayoff = false;

  //past matches
  match = {
    streams: [],
    match: {
      opponent1id: 0,
      opponent2id: 0
    },
    past_matchups: {
      match_games: []
    },
  };

  counter = 30;
  prematchBets = {};


  constructor(
    private _apiServices: ApiServices,
    private _activateRoute: ActivatedRoute,
    private _sanitizer: DomSanitizer,
    private _pluginService: PluginService
  ) { }

  playoffDisplay (val: string) {
    this.showPlayoff = (val.toLowerCase() === 'playoffs') ? true : false;
  }

  ngOnInit() {
    // second parameter is optional if there is type of game
    this._activateRoute.params
      .map(params=>{
        this.gameId = params['id'];

      }).subscribe((par)=>{
        this._apiServices.getGeneralMatch(this.gameId, 'all').subscribe((res)=>{
        this.match = res;
          this.prematchBets = res.event;

        this._pluginService.slickSliderGroup();
        // stream

          if(this.match.streams.length){
            this.stream = this._sanitizer.bypassSecurityTrustHtml(this.match.streams[0].stream.embed);
          }
      });

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

      var that = this;
      setInterval(function(){
        if(that.counter > 1){
          that.counter -= 1;
        }else {
          that.counter = 30;
          that._apiServices.getGeneralMatch(that.gameId, 'all').subscribe((res)=>{
            that.prematchBets = res.event;
          });
        }
      },1000);
      });
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
