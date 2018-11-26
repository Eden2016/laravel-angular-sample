import { SharedService } from './services/shared.service';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { TranslateService } from 'ng2-translate';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html'
})
export class AppComponent implements OnInit, OnDestroy {

  private subscription: Subscription;
  queryParams: string;

  constructor(
    private _translateService: TranslateService,
    private _activatedRouted: ActivatedRoute,
    private _sharedService: SharedService
  ) {
    _translateService.addLangs(['en-gb', 'zh-cn', 'vi-vn', 'km-kh', 'id-id', 'pt-br', 'ko-kr', 'th-th']);
    _translateService.setDefaultLang('en-gb');
  }

  ngOnInit(): any {
    this.subscription = this._activatedRouted.queryParams.subscribe((params) => {
      if (params['lang'] !== undefined) {
        // use langaugae from query
        this._translateService.use(params['lang']);
        // watch for query changes so it can update links
        this._sharedService.getLanguageQuery().subscribe((query) => {
            this.queryParams = query;
        });
        // set default back language
        this._sharedService.setLanguageQuery('?lang=' + params['lang']);
      }

    });
  }
  // unsubscribe on destroy to save memory recources 
  ngOnDestroy(): any {
     this.subscription.unsubscribe();
  }
}
