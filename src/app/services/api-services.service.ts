import {Injectable, Optional, EventEmitter} from '@angular/core';
import {Http} from '@angular/http';
import 'rxjs/add/operator/map';

import { environment } from '../../environments/environment';

@Injectable()
export class ApiServices {

  matchWithStream = new EventEmitter<Object>();
  constructor(private _http: Http) { }

  getMatches(type: number) {
    return this._http.get(environment.apiUrl + 'call.php?path=all/toutou_matches?type=' + type)
      .map((res) => res.json());
  }

  getMatch(id: number , @Optional() game: string = 'all') {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/match/' + id + '/client-188bet')
      .map((res) => res.json());
  }

  getGeneralMatch(id: number , @Optional() game: string = 'all') {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/raw/' + id + '/toutou')
      .map((res) => res.json());
  }

  getTournaments(game: string) {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/tournament')
      .map((res) => res.json());
  }

  getTournament(game: string, id: number) {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/tournament/' + id)
      .map((res) => res.json());
  }

  getTeam(id: number) {
    return this._http.get(environment.apiUrl + 'call.php?path=all/team/' + id + '?performance_time_frame=5')
      .map((res) => res.json());
  }

  getPlayer(game: string, id: number) {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/player/' + id + '?performance_time_frame=5' )
      .map((res) => res.json());
  }

  getPlayerStats (game: string, id: number) {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/player/' + id + '/stats' )
        .map((res) => res.json());
  }

  getHeros() {
    return this._http.get(environment.apiUrl + 'data/heroes.json')
      .map((res) => res.json());
  }

  getMatchOdds(game: string, matchId: number) {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/match/' + matchId + '/toutou')
      .map((res) => res.json());
  }

  getPosts(game: string, clientID: number) {
    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/blogs/' + clientID)
        .map((res) => res.json());
  }

  getSinglePost(game: string, clientID: number, postID: number) {

    return this._http.get(environment.apiUrl + 'call.php?path=' + game + '/blogs/' + clientID + '/' + postID)
        .map((res) => res.json());
  }

  setLiveStream(stream: Object) {
    this.matchWithStream.emit(stream);
  }

}
