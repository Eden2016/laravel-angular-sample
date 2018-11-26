import { Component, OnInit } from '@angular/core';
import {ApiServices} from '../../services/api-services.service';
import {ActivatedRoute} from '@angular/router';
import {PluginService} from '../../services/plugin.service';
import { SharedService } from '../../services/shared.service';
import {DomSanitizer} from '@angular/platform-browser';

@Component({
  selector: 'tournament-page',
  templateUrl: './tournament.component.html',
  styles: [`
    .left-cont .video-info .game-info .flag img,
    .tournament-content .tour-desc img,
    .outright .teams-container .team .team-flag img{
       width:22px;
       height:22px;   
    }
    
   .participans .participans-slider .partic-row {
      display:block;
    }
    
   .participans .participans-slider .participan {
      text-align: center;
      background: #eaeaea;
      padding: 5px 0;
      width: 25%;
      display: inline-block;
      vertical-align: top;
   }
   
   .participans .participans-slider .participan span.team-name {
      padding-top: 27px;
      padding-bottom: 26px;
      display: inline-block;
      vertical-align: top;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      font-size: 12px;
   }
   .participans .participans-slider .participan img {
      max-width: 100%;
      display:inline;    
      width:115px;
      height:70px;
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
      margin-bottom: 3px;
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
export class TournamentComponent implements OnInit {

  dateNow = Math.floor(Date.now());
  firstLoad: boolean = true;
  stream: any;

  tournament = {
    team_accounts: []
  };

  matchStream: Object;
  gameSlug: string;
  tournamentId: number;
  teamsId = [];
  showPlayoff = false;
  queryParams: string;

  constructor(
    private _apiService: ApiServices,
    private _activatedRoute: ActivatedRoute,
    private _pluginService: PluginService,
    private _sanitizer: DomSanitizer,
    private _sharedService: SharedService
  ) { }

  playoffDisplay (val: string) {
    this.showPlayoff = (val.toLowerCase() === 'playoffs') ? true : false;
  }

  ngOnInit() {

    this._activatedRoute.params
      .map((params) => {
      this.tournamentId = params['id'];
      this.gameSlug = params['game'];
      }).subscribe((par) => {

        this._apiService.getTournament(this.gameSlug, this.tournamentId).subscribe((res) => {
          this.tournament = res;

          if (this.tournament.team_accounts.length) {
            this.teamsId[0] = this.tournament.team_accounts[0].id;
            this.teamsId[1] = this.tournament.team_accounts[1].id;
          }

          this._pluginService.slickSliderPlayer('.game-slide');
          this._pluginService.slickSliderOutrightMarkets('.outright-slider');
          this._pluginService.slickSliderGroup();

        });
    });

    this._activatedRoute.queryParams.subscribe((params) => {
    // taking default lang params
      if (params['lang'] !== undefined) {
         this.queryParams = '?lang=' + params['lang'];
      }
    });

    this._apiService.matchWithStream.subscribe(
      data => {
        this.matchStream = data;
        if (data.dummy_match) {
          if (data.dummy_match.streams !== '') {
            for (var i = 0; i < data.dummy_match.streams.length; i++) {
              if(data.dummy_match.streams[i].embed !== '') {
                this.stream = this._sanitizer.bypassSecurityTrustHtml(data.dummy_match.streams[i].embed);

                // making lang menu expand
                setTimeout(()=>{
                  jQuery('.leng-cont .lang ul li:first-child').addClass('active');
                  // only if it's first load or ul menu removed
                  if(this.firstLoad){
                    // toggling language bar
                    jQuery('.video-info .lang ul').click(function(){
                      jQuery(this).toggleClass('open')
                    });
                    this.firstLoad = false;
                  }
                }, 1000);
                return true;
              }
            }
          }
        }
        else if (data.events) {
          if(data.events[0].streams !== ''){
            this.stream = this._sanitizer.bypassSecurityTrustHtml(data.events[0].streams[0].embed);
            for(var i = 0; i < data.events[0].streams.length; i++){
              if(data.events[0].streams[i].embed != '') {
                this.stream = this._sanitizer.bypassSecurityTrustHtml(data.events[0].streams[i].embed);
                // making lang menu expand
                setTimeout(()=>{
                  jQuery('.leng-cont .lang ul li:first-child').addClass('active');

                  if(this.firstLoad){
                    // toggling language bar
                    jQuery('.video-info .lang ul').click(function(){
                      jQuery(this).toggleClass('open')
                    });
                    this.firstLoad = false;
                  }
                },1000);
                return true;
              }
            }
          } else {
            this.stream = '';
            this.firstLoad = true;
          }
        }
      }
    )
    
    // listening for change of links
    this._sharedService.getLanguageQuery().subscribe((query) => {
      this.queryParams = query;
    });
  }

  isNumber(val) { return typeof val === 'number'; }

    // adding active state to groups and stages 
  createActive(event: any) {
    jQuery(event.target).parents('.group').addClass('active');
    jQuery(event.target).parents('.group').parent('li').siblings('li').each(function(){
      jQuery(this).find('.group').removeClass('active');
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
