import { ApiServices } from '../services/api-services.service'
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import * as jQuery from 'jquery';

@Component({
    selector: 'matchticker',
    templateUrl: './matchticker.component.html'
})
export class MatchtickerComponent implements OnInit {
    furtherMatches: Object[];
    inPlayMatches: Object[];
    dateNow = Math.floor(Date.now());

    constructor (
        public _router: Router,
        private _apiServices: ApiServices
    ) {
    }

    ngOnInit() {
        this._apiServices.getMatches(1).subscribe( (result) => {
          this.furtherMatches = result.further;
          this.inPlayMatches = result.inPlay;
        });
    }

    showMore() {
        // show more matches on click
        jQuery('#more-matches').parent('.match-ticker').toggleClass('expanded');
    }
}