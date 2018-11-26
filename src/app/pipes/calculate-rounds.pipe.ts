import { Pipe, PipeTransform } from '@angular/core';
import {DomSanitizer} from "@angular/platform-browser";

@Pipe({
  name: 'calculateRounds'
})
export class CalculateRoundsPipe implements PipeTransform {

  constructor( private _sanitizer: DomSanitizer){}

  transform(value: any, args?: any): any {
    var team1_score = 0;
    var team2_score = 0;

    for(var i=0; value.length > i; i++) {
        team1_score += +value[i].team1_score;
        team2_score += +value[i].team2_score;
    }
    // if score is null just show 0
    team1_score = isNaN(team1_score) ? 0 : team1_score;
    team2_score = isNaN(team2_score) ? 0 : team2_score;

    return this._sanitizer.bypassSecurityTrustHtml("<span>"+ team1_score + "</span>:<span>"+ team2_score + "</span>");
  }

}
