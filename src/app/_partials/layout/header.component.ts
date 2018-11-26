import { SharedService } from '../../services/shared.service';
import { Component, OnDestroy, OnInit} from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { TranslateService } from 'ng2-translate';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styles: []
})
export class HeaderComponent implements OnInit, OnDestroy {
      currentLang: string;
      private subscription: Subscription;
      queryParams: string;

  constructor(
    private _translateService: TranslateService,
    private _activatedRoute: ActivatedRoute,
    private _sharedService: SharedService,
    private _router: Router
  ) {
      this.currentLang = _translateService.currentLang;
  }

  ngOnInit(): any {
    // currentLang variable is for lang menu changing
    this.subscription = this._activatedRoute.queryParams.subscribe((params) => {
        this.currentLang = params['lang'];
    });
    // listening for change of links
    this._sharedService.getLanguageQuery().subscribe((query) => {
        this.queryParams = query;
    });
  }

  // unsubscribe on destroy to save memory recources
  ngOnDestroy(): any {
    this.subscription.unsubscribe();
  }

  setLang(language: string) {
    // translate language
    this._translateService.use(language);
    // set current language
    this.currentLang = language;
    // set lang to global variable so it can adjust links
    this._sharedService.setLanguageQuery('?lang=' + language);
    // add query params to url
    this._router.navigateByUrl(window.location.pathname + '?lang=' + language);
    location.reload();
  }

}
