import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { MatchtickerComponent } from '../matchticker.component'
import { SbkService } from '../../services/sbk.service'
import { Router } from '@angular/router';
import {Observable} from 'rxjs/Rx';
import * as jQuery from 'jquery';

import {Subscription} from 'rxjs/Subscription';

import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-right-sidebar',
  templateUrl: './right-sidebar.component.html',
  styles: []
})
export class RightSidebarComponent implements OnInit {
  debug: boolean = true;
  selections: any = [];
  betslipResponse: any = {};

  stakesTotal: number = 0;
  toWinTotal: number = 0;
  betsTotal: number = 0;
  singlesToWinTotal: number = 0;

  currency: string = '';

  shouldConfirmBet: boolean = false;
  betReceipt: boolean = false;

  receiptItems: Object;

  myBets: Object;

  betslipShown: boolean = true;
  betHistoryShown: boolean = false;

  doublesField: Object = {name: "Doubles", selections: 2, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 2};
  treblesField: Object = {name: "Trebles", selections: 3,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 3};
  fourFoldField: Object = {name: "Four Folds", selections: 4,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 4};
  fiveFoldField: Object = {name: "Five Folds", selections: 5,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 5};
  sixFoldField: Object = {name: "Six Folds", selections: 6,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 6};
  sevenFoldField: Object = {name: "Seven Folds", selections: 7,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 7};
  eightFoldField: Object = {name: "Eight Folds", selections: 8,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 8};
  nineFoldField: Object = {name: "Nine Folds", selections: 9,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 9};
  tenFoldField: Object = {name: "Ten Folds", selections: 10,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 10};

  trixieField: Object = {name: "Trixie", selections: 3, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 11};
  yankeeField: Object = {name: "Yankee", selections: 4, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 12};
  superYankeeField: Object = {name: "Super Yankee", selections: 5, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 13};
  heinzField: Object = {name: "Heinz", selections: 6, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 14};
  superHeinzField: Object = {name: "Super heinz", selections: 7, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 15};
  goliathField: Object = {name: "Goliath", selections: 8, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 16};
  blocksNineField: Object = {name: "Blocks(9)", selections: 9, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 17};
  blocksTenField: Object = {name: "Blocks(10)", selections: 10, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 99999999999, typeID: 18};

  folds: Object = {
    doubles: this.doublesField,
    trebles: this.treblesField,
    fourFold: this.fourFoldField,
    fiveFold: this.fiveFoldField,
    sixFold: this.sixFoldField,
    sevenFold: this.sevenFoldField,
    eightFold: this.eightFoldField,
    nineFold: this.nineFoldField,
    tenFold: this.tenFoldField
  };

  systems: Object = {
    trixie: this.trixieField,
    yankee: this.yankeeField,
    superYankee: this.superYankeeField,
    heinz: this.heinzField,
    superHeinz: this.superHeinzField,
    goliath: this.goliathField,
    blocksNine: this.blocksNineField,
    blocksTen: this.blocksTenField
  };

  subscription:Subscription;

  constructor (
    public _router: Router,
    private _sbkService: SbkService,
    private _matchtickerComponent: MatchtickerComponent,
    private _cdr: ChangeDetectorRef
  ) {
   }

  ngOnInit() {
    //Set preffered language
    this._sbkService.setUserLanguage();

    //Get user preferences in terms currency
    if (this._sbkService.token) {
        this.setCurrency();
    }
    else {
      this._sbkService.setUserToken();
      this.setCurrency();
    }

    this.subscription = this._sbkService.betItems$
       .subscribe(selections => {
         this.refreshData(selections);
        })

    this._sbkService.getLocalStorage();

    setTimeout(() => {
      this.reloadBetslip();
    }, 30000);
  }

  ngOnDestroy() {
    this.receiptItems = {};
    this.selections = [];

    // prevent memory leak when component is destroyed
    this.subscription.unsubscribe();
  }

  setCurrency() {
    let $t = this;

    if (this._sbkService.token) {
        $.get(environment.apiUrl + 'sbk.php?action=login&token=' + this._sbkService.token, {
          async: false
        }, function (data) {
            if (data.status == "success") {
                $t.currency = data.data.preferredCurrency;
            } else {
                console.log(data);
            }
        });
    }
    else {
      this.currency = 'RMB';
    }
  }

  reloadBetslip() {
    let betslip:Observable<any>;

    betslip = this._sbkService.placeSelection();
    betslip.subscribe(res => {
      this.betslipResponse = res;

      this.detectBetslipChanges();

      this.recalculateFields();
      this.recalculateTotals();

      setTimeout(() => {
        this.reloadBetslip();
      }, 30000);
    },
    error => {
      if (this.debug)
        console.log(error);
    });
  }

  callBetslipService() {
    let betslip:Observable<any>;

    if (this.selections.length) {
      betslip = this._sbkService.placeSelection();
      betslip.subscribe(res => {
        this.betslipResponse = res;

        this.recalculateFields();
        this.recalculateTotals();

        this._cdr.detectChanges();
        console.log('CALLING CDR CHANGES');
      },
      error => {
        if (this.debug)
          console.log(error);
      });
    }
  }

  confirmBet() {
    this.shouldConfirmBet = true;

    //Set receipt items
    this.receiptItems = {
      selections: this.selections,
      folds: this.folds,
      systems: this.systems,
      totalBets: this.betsTotal,
      totalStake: this.stakesTotal,
      totalToWin: this.toWinTotal
    };
  }

  cancelBet() {
    this.shouldConfirmBet = false;
    this.receiptItems = {};
  }

  confirmReceipt() {
    //Update better odds
    let accept:Observable<any>;

    accept = this._sbkService.placeBet(this.folds, this.systems);
    accept.subscribe(res => {
        //Success
    },
    error => {
      if (this.debug)
        console.log(error);
    });

    //Clean up receipt object
    this.receiptItems = {};

    //Hide receipt tab
    this.betReceipt = false;
  }

  placeBet() {
    let bet:Observable<any>;

    if (this.selections.length > 0) {
      bet = this._sbkService.placeBet(this.folds, this.systems);
      bet.subscribe(res => {
          //If no errors and bet was placed successfully, clear stuff up
          if (res.status == "success") {
            //Clear selections from betslip
            this.selections = [];
            this._sbkService.betItems = [];

            //Reset all combo fields to their original state
            this.cleanUpFields();

            //Hide confirm bet tab
            this.shouldConfirmBet = false;

            //Show bet receipt
            this.betReceipt = true;
          }
          else {
            //TODO
            //Display error
          }
      },
      error => {
        if (this.debug)
          console.log(error);
      });
    }
    else {
      //TODO
      //Display error (No selections in betslip)
    }
  }

  betsHistory() {
    let myBets:Observable<any>;

    myBets = this._sbkService.getBets();
    myBets.subscribe(res => {
      //TODO
      //Render bets response
      this.myBets = res;
    },
    error => {
      if (this.debug)
        console.log(error);
    });
  }

  refreshData(data) {
    this.selections = data;
    this.callBetslipService();
  }

  removeFromBetSlip(selectionId) {
    this._sbkService.removeSelection(selectionId);
    this.callBetslipService();
  }

  singlesStake(event) {
    var stake = parseFloat(event.target.value);

    for (var i = 0; i < this.selections.length; i++) {
      var odds = parseFloat(this.selections[i].odd);
      var payout = this.calculatePayout(stake, odds, this.selections[i].maxBet);

      if (stake > this.selections[i].maxBet)
        this._sbkService.updateSelectionItem(this.selections[i].strippedSelectionId, "stake", this.selections[i].maxBet);
      else
        this._sbkService.updateSelectionItem(this.selections[i].strippedSelectionId, "stake", event.target.value);

      this._sbkService.updateSelectionItem(this.selections[i].strippedSelectionId, "toWin", payout);
    }
  }

  stake(event, selectionId) {
    var stake = parseFloat(event.target.value);
    var odds = parseFloat(this._sbkService.getSelectionItem(selectionId, "odd"));
    var maxBet = this.getSelectionsMaxBet(selectionId);
    var payout = this.calculatePayout(stake, odds, maxBet);

    if (stake > maxBet)
      stake = maxBet;

    this._sbkService.updateSelectionItem(selectionId, "stake", stake);
    this._sbkService.updateSelectionItem(selectionId, "toWin", payout);
  }

  toWin(event, selectionId) {
    var toWin = parseFloat(event.target.value);
    var maxBet = this.getSelectionsMaxBet(selectionId);
    var odds = parseFloat(this._sbkService.getSelectionItem(selectionId, "odd"));
    var stake = this.calculateStake(toWin, odds, maxBet);

    this._sbkService.updateSelectionItem(selectionId, "toWin", stake[1]);
    this._sbkService.updateSelectionItem(selectionId, "stake", stake[0]);

    this.recalculateTotals();
  }

  addStake(selectionId, value) {
    var maxBet = this.getSelectionsMaxBet(selectionId);
    var stake = parseFloat(this._sbkService.getSelectionItem(selectionId, "stake"));
    stake = !isNaN(stake) ? stake : 0;
    stake += parseFloat(value);

    var odds = parseFloat(this._sbkService.getSelectionItem(selectionId, "odd"));
    var payout = this.calculatePayout(stake, odds, maxBet);

    if (stake > maxBet)
      stake = maxBet;

    this._sbkService.updateSelectionItem(selectionId, "stake", stake);
    this._sbkService.updateSelectionItem(selectionId, "toWin", payout);

    this.recalculateTotals();
  }

  clear(selectionId) {
    this._sbkService.updateSelectionItem(selectionId, "stake", "");
    this._sbkService.updateSelectionItem(selectionId, "toWin", "");

    this.recalculateTotals();
  }

  maxBet(selectionId) {
    var maxBet = this.getSelectionsMaxBet(selectionId);
    var odds = parseFloat(this._sbkService.getSelectionItem(selectionId, "odd"));
    var payout = this.calculatePayout(maxBet, odds, maxBet);

    this._sbkService.updateSelectionItem(selectionId, "stake", maxBet);
    this._sbkService.updateSelectionItem(selectionId, "toWin", payout);

    this.recalculateTotals();
  }

  stakeFold(event, key) {
    this.folds[key].stake = parseFloat(event.target.value);

    this.folds[key].toWin = this.calculatePayout(this.folds[key].stake, this.folds[key].odds, this.folds[key].maxBet);
    this.recalculateTotals();
  }

  toWinFold(event, key) {
    this.folds[key].toWin = parseFloat(event.target.value);

    this.folds[key].stake = this.calculateStake(this.folds[key].toWin, this.folds[key].odds, this.folds[key].maxBets);
    this.recalculateTotals();
  }

  stakeSystems(event, key) {
    this.systems[key].stake = parseFloat(event.target.value);

    this.systems[key].toWin = this.calculatePayout(this.systems[key].stake, this.systems[key].odds, this.systems[key].maxBet);
    this.recalculateTotals();
  }

  toWinSystems(event, key) {
    this.systems[key].toWin = parseFloat(event.target.value);

    this.systems[key].stake = this.calculateStake(this.systems[key].toWin, this.systems[key].odds, this.systems[key].maxBets);
    this.recalculateTotals();
  }

  removeAll() {
    this._sbkService.removeAll();
    this.recalculateFields();
    this.recalculateTotals();
  }

  recalculateTotals() {
    var totalStakes = 0,
        totalToWin = 0,
        totalBets = 0,
        singlesTotalWin = 0;

    //Sum total single stakes, returns and bets
    for (var i = 0; i < this.selections.length; i++) {
      this.selections[i].maxBet = this.getSelectionsMaxBet(this.selections[i].strippedSelectionId);

      var stake = parseFloat(this.selections[i].stake);
      if (stake > 0) {
        totalStakes += stake;
        totalToWin += parseFloat(this.selections[i].toWin);
        singlesTotalWin += parseFloat(this.selections[i].toWin);
        totalBets++;
      }
    }

    if (this.debug)
      console.log(this.selections);

    //Sum total flods stakes, returns and bets
    for (var key in this.folds) {
      if (this.folds.hasOwnProperty(key)) {
        if (this.folds[key].active === 1 && this.folds[key].stake > 0) {
          totalBets += this.folds[key].bets;
          totalStakes += this.folds[key].bets * this.folds[key].stake;
          totalToWin += this.folds[key].toWin;
        }
      }
    }

    //Sum total system stakes, returns and bets
    for (var key in this.systems) {
      if (this.systems.hasOwnProperty(key)) {
        if (this.systems[key].active === 1 && this.systems[key].stake > 0) {
          totalBets += this.systems[key].bets;
          totalStakes += this.systems[key].bets * this.systems[key].stake;
          totalToWin += this.systems[key].toWin;
        }
      }
    }

    this.stakesTotal = this.roundNum(totalStakes);
    this.toWinTotal = this.roundNum(totalToWin);
    this.betsTotal = this.roundNum(totalBets);
    this.singlesToWinTotal = singlesTotalWin;
  }

  recalculateFields() {
    var inPlay = this.checkInPlay();
    var sameMatch = this.checkMatchDuplicate();

    //Calculate folds odds, number of bets
    for (var key in this.folds) {
      if (this.folds.hasOwnProperty(key)) {
        if ((this.folds[key].selections <= this.selections.length) && !inPlay && !sameMatch) {
          if (this.debug)
            console.log('Setting ' + this.folds[key].name + ' to active');

          this.folds[key].active = 1;

          this.folds[key].bets = this.calculateNumOfBets(this.folds[key].selections);
          this.folds[key].odds = this.calculateFoldsOdds(this.folds[key].selections);

          this.folds[key].maxBet = this.getMultiMaxBet(this.folds[key].typeID);
        }
        else {
          this.folds[key].active = 0;
          this.folds[key].odds = 0;
          this.folds[key].bets = 0;
        }
      }
    }

    //Calculate system odds, number of bets
    for (var key in this.systems) {
      if (this.systems.hasOwnProperty(key)) {
        if ((this.systems[key].selections == this.selections.length) && !inPlay && !sameMatch) {
          if (this.debug)
            console.log('Setting ' + this.systems[key].name + ' to active');

          this.systems[key].active = 1;

          this.systems[key].bets = this.calculateSystemBets();
          this.systems[key].odds = this.calculateSystemOdds();

          this.systems[key].maxBet = this.getMultiMaxBet(this.systems[key].typeID);
        }
        else {
          this.systems[key].active = 0;
          this.systems[key].bets = 0;
          this.systems[key].odds = 0;
        }
      }
    }
  }

  detectBetslipChanges() {
    for (var i = 0; i < this.betslipResponse.s.length; i++) {
      if (this.debug)
              console.log("Checking for changes on " + this.betslipResponse.s[i].sid);

      for (var l = 0; l < this.selections.length; l++) {
        if (this.selections[l].strippedSelectionId == this.betslipResponse.s[i].sid) {
          if (parseFloat(this.selections[l].odd) != this.betslipResponse.s[i].o) {
            this.selections[l].changed = true;
            this.selections[l].odd = this.betslipResponse.s[i].o;

            this.selections[l].toWin = this.changeToWin(this.selections[l]);

            this._sbkService.changeSelection(this.selections[l]);

            if (this.debug)
              console.log("Should change odds on selection " + this.selections[l].strippedSelectionId);
          }
          else {
            if (this.selections[l].changed) {
              this.selections[l].changed = false;
              this._sbkService.changeSelection(this.selections[l]);
            }
            else {
              this.selections[l].changed = false;
            }
          }
        }
      }
    }
  }

  changeToWin(item) {
    var stake = parseFloat(item.stake);
    var odds = parseFloat(item.odd);

    return this.calculatePayout(stake, odds, item.maxBet);
  }

  calculatePayout(stake, odds, maxbet) {
      var profit = 0;

      if (stake > maxbet) {
          stake = maxbet;
      }

      switch (this._sbkService.oddType) {
          //For decimal odds
          case 1:
              //Returning the payout (profit - stake)
              profit = stake * (odds - 1);
              break;
          //For Hong Kong odds
          case 2:
              //Returning only the profit
              profit = stake * odds;
              break;
          //For Malay odds
          case 3:
              //Returning the payout (profit + stake)
              if (odds > 0) {
                  profit = stake * (1 + odds);
              } else {
                  profit = stake * (1 - 1 / odds);
              }
              break;
          //For Indo odds
          case 4:
              //Returns only profit
              if (odds > 0) {
                  profit = stake * odds;
              } else {
                  profit = stake / (-1 * odds);
              }
              break;
      }

      profit = profit || 0;
      profit = this.roundNum(profit);
      return profit;
  }

  calculateStake(toWin, odds, maxbet) {
      var stake = 0;

      switch (this._sbkService.oddType) {
          //For decimal odds
          case 1:
              //Returning the payout (profit - stake)
              stake = toWin / (odds - 1);
              break;
          //For Hong Kong odds
          case 2:
              //Returning only the profit
              stake = toWin / odds;
              break;
          //For Malay odds
          case 3:
              //Returning the payout (profit + stake)
              if (odds > 0) {
                  stake = toWin / (1 + odds);
              } else {
                  stake = toWin / (1 - 1 / odds);
              }
              break;
          //For Indo odds
          case 4:
              //Returns only profit
              if (odds > 0) {
                  stake = toWin / odds;
              } else {
                  stake = toWin / (-1 * odds);
              }
              break;
      }

      if (stake > maxbet) {
          stake = maxbet;
          toWin = this.calculatePayout(stake, odds, maxbet);
      }

      stake = stake || 0;
      stake = this.roundNum(stake);
      return [stake,toWin];
  }

  // Calculate combinations number (number of outcomes)
  calculateNumOfBets(outcomes) {
    if (outcomes > this.selections.length)
      return 0;

    return this.factorial(this.selections.length) / (this.factorial(outcomes) * this.factorial(this.selections.length - outcomes));
  }

  //Calculate odds for each folds fields
  calculateFoldsOdds(outcomes) {
    var combos = this.kCombinations(this.selections, outcomes);

    var odds = [];
    for (var i = 0; i < combos.length; i++) {
      var odd = 1;
      for (var l = 0; l < outcomes; l++) {
        odd *= parseFloat(combos[i][l].odd);
      }
      odds.push(odd);
    }

    return odds.reduce(function(acc, val) {
              return acc + val;
            }, 0);
  }

  calculateSystemBets() {
    var betNum = 0;

    for (var key in this.folds) {
      if (this.folds.hasOwnProperty(key)) {
        if (this.folds[key].active === 1) {
          betNum += this.folds[key].bets;
        }
      }
    }

    return betNum;
  }

  calculateSystemOdds() {
    var oddsNum = 0;

    for (var key in this.folds) {
      if (this.folds.hasOwnProperty(key)) {
        if (this.folds[key].active === 1) {
          oddsNum += this.folds[key].odds;
        }
      }
    }

    return oddsNum;
  }

  checkInPlay() {
    for (var i = 0; i < this.selections.length; i++) {
      if (this.selections[i].in_play)
        return true;
    }

    return false;
  }

  checkMatchDuplicate() {
    var hasDuplicate = false;
    var buffer = [];

    for (var i = 0; i < this.selections.length; i++) {
      var value = this.selections[i].event_id;
      if (buffer.indexOf(value) !== -1) {
          this.selections[i].hasDuplicate = true;
          hasDuplicate = true;
      }
      else {
        this.selections[i].hasDuplicate = false;
      }
      buffer.push(value);
    }

    return hasDuplicate;
  }

  getMultiMaxBet(typeId) {
    for (var i = 0; i < this.betslipResponse.cinfo.length; i++) {
      if (this.betslipResponse.cinfo[i].wid === typeId)
        return this.betslipResponse.cinfo[i].cbs.bmax;
    }

    return 0;
  }

  getSelectionsMaxBet(selectionId) {
    for (var i = 0; i < this.betslipResponse.s.length; i++) {
      if (this.betslipResponse.s[i].sid === selectionId)
        return this.betslipResponse.s[i].bs.bmax;
    }

    return 0;
  }

  cleanUpFields() {
    let doublesField: Object = {name: "Doubles", selections: 2, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 2};
    let treblesField: Object = {name: "Trebles", selections: 3,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 3};
    let fourFoldField: Object = {name: "Four Folds", selections: 4,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 4};
    let fiveFoldField: Object = {name: "Five Folds", selections: 5,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 5};
    let sixFoldField: Object = {name: "Six Folds", selections: 6,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 6};
    let sevenFoldField: Object = {name: "Seven Folds", selections: 7,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 7};
    let eightFoldField: Object = {name: "Eight Folds", selections: 8,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 8};
    let nineFoldField: Object = {name: "Nine Folds", selections: 9,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 9};
    let tenFoldField: Object = {name: "Ten Folds", selections: 10,stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 10};

    let trixieField: Object = {name: "Trixie", selections: 3, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 11};
    let yankeeField: Object = {name: "Yankee", selections: 4, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 12};
    let superYankeeField: Object = {name: "Super Yankee", selections: 5, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 13};
    let heinzField: Object = {name: "Heinz", selections: 6, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 14};
    let superHeinzField: Object = {name: "Super Heinz", selections: 7, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 15};
    let goliathField: Object = {name: "Goliath", selections: 8, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 16};
    let blocksNineField: Object = {name: "Blocks(9)", selections: 9, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 17};
    let blocksTenField: Object = {name: "Blocks(10)", selections: 10, stake: 0, toWin: 0, active: 0, bets: 0, odds: 0, minBet: 0, maxBet: 0, typeID: 18};

    let folds: Object = {
      doubles: this.doublesField,
      trebles: this.treblesField,
      fourFold: this.fourFoldField,
      fiveFold: this.fiveFoldField,
      sixFold: this.sixFoldField,
      sevenFold: this.sevenFoldField,
      eightFold: this.eightFoldField,
      nineFold: this.nineFoldField,
      tenFold: this.tenFoldField
    };

    let systems: Object = {
      trixie: this.trixieField,
      yankee: this.yankeeField,
      superYankee: this.superYankeeField,
      heinz: this.heinzField,
      superHeinz: this.superHeinzField,
      goliath: this.goliathField,
      blocksNine: this.blocksNineField,
      blocksTen: this.blocksTenField
    };

    this.folds = folds;
    this.systems = systems;

    return true;
  }

  showBetslip() {
    this.betHistoryShown = false;
    this.betslipShown = true;
  }

  loadMyBets() {
    let myBets:Observable<any>;

    this.betslipShown = false;
    this.betHistoryShown = true;

    myBets = this._sbkService.getBets();
    myBets.subscribe(res => {
        this.myBets = res;
    },
    error => {
      if (this.debug)
        console.log(error);
    });
  }

  //Always have 2 digits after the decimal point
  roundNum(num) {
      num = num || 0;

      return Math.round(num * 100) / 100;
  }

  //Simple factorial function
  factorial(num) {
      if (num === 0)
        return 1;
      else
        return num * this.factorial(num - 1);
  }

  //Get combinations of k pairs from a set
  kCombinations(set, k) {
    var i, j, combs, head, tailcombs;

    // There is no way to take e.g. sets of 5 elements from
    // a set of 4.
    if (k > set.length || k <= 0) {
      return [];
    }

    // K-sized set has only one K-sized subset.
    if (k == set.length) {
      return [set];
    }

    // There is N 1-sized subsets in a N-sized set.
    if (k == 1) {
      combs = [];
      for (i = 0; i < set.length; i++) {
        combs.push([set[i]]);
      }
      return combs;
    }

    // Assert {1 < k < set.length}
    combs = [];
    for (i = 0; i < set.length - k + 1; i++) {
      // head is a list that includes only our current element.
      head = set.slice(i, i + 1);
      // We take smaller combinations from the subsequent elements
      tailcombs = this.kCombinations(set.slice(i + 1), k - 1);
      // For each (k-1)-combination we join it with the current
      // and store it to the set of k-combinations.
      for (j = 0; j < tailcombs.length; j++) {
        combs.push(head.concat(tailcombs[j]));
      }
    }

    return combs;
  }
}
