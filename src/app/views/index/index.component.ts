import {Component, OnInit} from '@angular/core';
import {PluginService} from '../../services/plugin.service';
import {DomSanitizer} from '@angular/platform-browser';
import {ApiServices} from '../../services/api-services.service';
import { SharedService } from '../../services/shared.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'index-page',
  templateUrl: './index.component.html',
  styles: [`
    .left-cont .video-info .game-info img {
      width:22px;
    }
    
    .tournament-content .tour-desc img {
      width:22px;
    }
  `]
})
export class IndexComponent implements OnInit {

  firstLoad: boolean = true;
  stream: any;
  matchStream: Object;
  queryParams: string;

  constructor(
       private _apiService: ApiServices,
       private _pluginService: PluginService,
       private _sanitizer: DomSanitizer,
       private _sharedService: SharedService,
       private _activatedRoute: ActivatedRoute
  ) {}

  ngOnInit() {
    this._pluginService.slickSliderPlayer('.game-slide');
    // taking default lang params
    this._activatedRoute.queryParams.subscribe((params) => {
      if (params['lang'] !== undefined) {
        this.queryParams = '?lang=' + params['lang'];
      }
    });

    // listening for change of links
    this._sharedService.getLanguageQuery().subscribe((query) => {
        this.queryParams = query;
    });
    
    this._apiService.matchWithStream.subscribe(
      data => {
        this.matchStream = data;
        // check if stream is available at dummy match or at events
        if(data.dummy_match){
          if(data.dummy_match.streams != ''){
              for(var i = 0; i < data.dummy_match.streams.length; i++){
                if(data.dummy_match.streams[i].embed != '') {
                  this.stream = this._sanitizer.bypassSecurityTrustHtml(data.dummy_match.streams[i].embed);

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
            }
        }
        else if (data.events){
          if(data.events[0].streams != ''){
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
