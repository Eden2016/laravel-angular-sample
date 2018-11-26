import { Component, OnInit } from '@angular/core';
import { ApiServices } from '../../services/api-services.service';
import { SharedService } from '../../services/shared.service';
import { forEach } from '@angular/router/src/utils/collection';

@Component({
  selector: 'app-left-sidebar',
  templateUrl: './left-sidebar.component.html',
  styles: [`
   .sidebar-menu .menu .blog-panel .sub-menu {
    max-height: 550px;
    overflow: auto; }
  .sidebar-menu .menu .blog-panel .sub-menu li a {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    min-height: 75px;
    -webkit-flex-direction: column;
    flex-direction: column;
    -webkit-justify-content: space-between;
    justify-content: space-between; }
  .sidebar-menu .menu .blog-panel .sub-menu li a h3 {
    font-size: 1em;
    color: #999999;
    font-weight: 700;
    margin-top: 0;
    margin-bottom: 5px; }
  .sidebar-menu .menu .blog-panel .sub-menu li a p {
    color: #ffffff;
    font-size: 0.833em;
    margin-bottom: 0; }
  .sidebar-menu .menu .blog-panel .sub-menu li a p img {
    margin-left: 0;
    margin-right: 5px;
    width: 15px;
    height: 15px; }
  .sidebar-menu .menu .blog-panel .sub-menu li a:hover h3 {
    color: #ffffff; }
  `]
})
export class LeftSidebarComponent implements OnInit {
  dateNow = Math.floor(Date.now());
  queryParams: string;
  posts: Object[];

  gameTournaments = {
      dota2:{},
      csgo:{},
      lol:{}
  }

  constructor(
    private _apiServices: ApiServices,
    private _sharedService: SharedService
  ) { }

  ngOnInit() {
    this.getTournamentsPerGame('dota2');
    this.getTournamentsPerGame('csgo');
    this.getTournamentsPerGame('lol');

    // listening for change of links
    this._sharedService.getLanguageQuery().subscribe((query) => {
        this.queryParams = query;
    });

    // Get all posts
    this._apiServices.getPosts('all', 1).subscribe(posts => {
      this.posts = posts.data;
    })
  }

  getTournamentsPerGame(game: string) {
    this._apiServices.getTournaments(game).subscribe((res)=>{
      this.gameTournaments[game].tournaments = res;
      let dateNowRef = this.dateNow;
      let tournamentsOngoingRef = [];
      let tournamentsUpcomingRef = [];
      // looping true tournaments to separate upcoming and current
      this.gameTournaments[game].tournaments.forEach(function (tournament) {
        let startDate = new Date(tournament.start);
        let endDate = new Date(tournament.end);
        if(dateNowRef > +startDate && dateNowRef < +endDate) {
          tournamentsOngoingRef.push(tournament);
        } else if (dateNowRef < +startDate) {
          tournamentsUpcomingRef.push(tournament);
        }
      });
      // assigning back to global variables
      this.gameTournaments[game].tournamentsOngoing = tournamentsOngoingRef;
      this.gameTournaments[game].tournamentsUpcoming = tournamentsUpcomingRef;

    });
  }
}
