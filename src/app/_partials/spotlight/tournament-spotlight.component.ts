import {Component, OnInit, Input} from '@angular/core';
import {ApiServices} from "../../services/api-services.service";

@Component({
  selector: 'app-tournament-spotlight',
  templateUrl: 'tournament-spotlight.component.html'
})
export class TournamentSpotlightComponent implements OnInit {
  @Input() tournamentId: number;
  @Input() gameId: string;
  tournament = {};

  constructor(private _apiServices: ApiServices) { }

  ngOnInit() {

    this._apiServices.getTournament(this.gameId, this.tournamentId).subscribe((res)=>{
        this.tournament = res;
    });

  }

}
