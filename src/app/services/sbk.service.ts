import {Injectable, Optional, EventEmitter} from '@angular/core';
import {Http, Headers, Response, RequestOptions} from '@angular/http';
import {Observable} from 'rxjs/Rx';
import 'rxjs/add/operator/map';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';
import { LocalStorageService } from 'angular-2-local-storage';

import { forEach } from '@angular/router/src/utils/collection';
import { environment } from '../../environments/environment';

import * as jQuery from 'jquery';


@Injectable()
export class SbkService {
    betItems: any = [];
    betItemsEvent = new EventEmitter<any>();

    oddType: number = 1;

    qs: string = "";
    token: any = false;
    language: string = 'zh-cn';

    // Observable navItem source
    private _betItemsSource = new BehaviorSubject<any>(this.betItems);
    // Observable navItem stream
    betItems$ = this._betItemsSource.asObservable();

    debug: boolean = true;

    constructor (
            private _http: Http,
            private _localStorageService: LocalStorageService
        ) {}

    //Add a selection to the betslip
    storeSelection(item) {
        if (this.betItems.length < 10) {
            if (item.odd != "0.00") {
                var present = false;

                //Parsing selection ID as a integer
                var stripped = item.selectionId;
                item.strippedSelectionId = parseInt(stripped.substr(1));

                forEach(this.betItems, (betItem, key) => {
                    if (betItem.strippedSelectionId == item.strippedSelectionId)
                        present = true;
                });

                if (!present) {
                    if (this.debug)
                        console.log('We don\'t have the selection in the local storage');

                    this.betItems.push(item);
                    this.setLocalStorage();
                }
                else {
                    //TODO
                    //Indicate that the user already has that selection in the betslip

                    if (this.debug)
                        console.log('We already have the selection in the local sotrage');
                }
            }
            else {
                if (this.debug)
                    console.log('The selection has 0.00 odds');
                return false;
            }
        }
        else {
            if (this.debug)
                    console.log('You already have 10 selections in your betslip');
            return false;
        }
    }

    removeSelection(selectionId) {
        if (this.debug)
            console.log('Remove selection called for selection id ' + selectionId);

        for (var i = this.betItems.length-1; i >= 0; i--) {
            if (this.betItems[i].strippedSelectionId == selectionId)
                this.betItems.splice(i, 1);
        }

        if (this.debug)
            console.log('Removed the selection, betItems length is ' + this.betItems.length);

        this.setLocalStorage();
        return true;
    }

    changeSelection(item) {
        for (var i = 0; i < this.betItems.length; i++) {
            if (this.betItems[i].strippedSelectionId == item.strippedSelectionId) {
                this.betItems[i] = item;
            }
        }

        this._localStorageService.set('betItems', this.betItems);
    }

    removeAll() {
        this.betItems = [];
        this.setLocalStorage();
    }

    //Get all betslip selections from local storage and notify everyone subscribed to the event
    getLocalStorage() {
        this.betItems = this._localStorageService.get('betItems');
        if (!this.betItems)
            this.betItems = [];

        this._betItemsSource.next(this.betItems);

        return this.betItems;
    }

    //Update local storage to match our betslip and notify everyone subscribed to the event
    setLocalStorage() {
        this._localStorageService.set('betItems', this.betItems);
        this._betItemsSource.next(this.betItems);

        return true;
    }

    //Get one selection from betslip by its selection id
    getSelection(selectionId) {
        var items = this.getLocalStorage();
        var item: Object;

        for (var i = 0; i < items.length; i++) {
            if (items[i].selectionId == selectionId) {
                item = items[i];
                break;
            }
        }

        if (item) {
            return item;
        }
        else {
            return false;
        }
    }

    updateSelectionItem(selectionId, key, value) {
        var changed = false;
        this.getLocalStorage();

        if (this.debug)
            console.log("Updating " + key + " to " + value + " for selection id " + selectionId);

        for (var i = 0; i < this.betItems.length; i++) {
            if (this.betItems[i].strippedSelectionId == selectionId) {
                if (this.betItems[i][key] != value) {
                    this.betItems[i][key] = value;

                    changed = true;
                }
                break;
            }
        }

        if (changed)
            this.setLocalStorage();

        return true;
    }

    getSelectionItem(selectionId, key) {
        var value = null;

        if (this.debug)
            console.log("Fetching value for " + key + " of selection id " + selectionId);

        for (var i = 0; i < this.betItems.length; i++) {
            if (this.betItems[i].strippedSelectionId == selectionId) {
                value = this.betItems[i][key];
                break;
            }
        }

        return value;
    }

    //Place a selection in the betslip (In the SBK betslip service)
    placeSelection() {
        let token = this.token ? this.token : '';
        let headers      = new Headers({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        let options       = new RequestOptions({ headers: headers });
        let body = JSON.stringify(this.betItems);
        return this._http.post(environment.apiUrl + 'sbk.php?action=placebetslip&token=' + token, body)
                    .map((res:Response) => res.json()) // ...and calling .json() on the response to return data
                    .catch((error:any) => Observable.throw(error.json().error || 'Server error')); //...errors if
    }

    //Place a bet
    placeBet(folds, systems) {
        let token = this.token ? this.token : '';
        let headers      = new Headers({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        let options       = new RequestOptions({ headers: headers });
        let body = JSON.stringify({
            selections: this.betItems,
            folds: folds,
            systems: systems
        });

        return this._http.post(environment.apiUrl + 'sbk.php?action=placebet&token=' + token, body)
                    .map((res:Response) => res.json()) // ...and calling .json() on the response to return data
                    .catch((error:any) => Observable.throw(error.json().error || 'Server error')); //...errors if
    }

    //Get unsettled and settled bets
    getBets() {
        let token = this.token ? this.token : '';

        return this._http.get(environment.apiUrl + 'sbk.php?action=mybets&language=' + this.language + '&token=' + token)
                  .map((res) => res.json())
                  .catch((error:any) => Observable.throw(error.json().error || 'Server error'));
    }

    getLogin() {
        let token = this.token ? this.token : '';

        return this._http.get(environment.apiUrl + 'sbk.php?action=login&token=' + token)
                  .map((res) => res.json())
                  .catch((error:any) => Observable.throw(error.json().error || 'Server error'));
    }

    acceptBetterOdds() {
        let token = this.token ? this.token : '';
        let isAccept = $('#acceptBetterOdds').is(':checked') ? 1 : 0;

        return this._http.get(environment.apiUrl + 'sbk.php?action=acceptbetterodds&token=' + token + '&accept=' + isAccept)
                  .map((res) => res.json())
                  .catch((error:any) => Observable.throw(error.json().error || 'Server error'));
    }

    getQueryString() {
        var vars = [],
        hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }

    setUserToken() {
        //Get token from query string and store it into a cookie (if present)
        let qs = this.getQueryString();
        if (qs['token']) {
          this.token = qs['token'];
        }
        else {
          this.token = false;
        }
    }

    setUserLanguage() {
        let qs = this.getQueryString();

        if (qs['language']) {
          this.language = qs['language'];
        }
    }
}
