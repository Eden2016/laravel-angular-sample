import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { AppComponent } from './app.component';
import { HeaderComponent } from './_partials/layout/header.component';
import { FooterComponent } from './_partials/layout/footer.component';
import { RightSidebarComponent } from './_partials/layout/right-sidebar.component';
import { LeftSidebarComponent } from './_partials/layout/left-sidebar.component';
import { IndexComponent } from './views/index/index.component';
import {Routes, RouterModule} from '@angular/router';
import { TournamentComponent } from './views/tournament/tournament.component';
import {ApiServices} from './services/api-services.service';
import { SecondsToTimePipe } from './pipes/seconds-to-time.pipe';
import { ReplaceSpacePipe } from './pipes/replace-space.pipe';
import { MatchTableComponent } from './_partials/match-table.component';
import { TeamSpotlightComponent } from './_partials/spotlight/team-spotlight.component';
import {ChartModule} from 'angular2-highcharts';
import { PlayerSpotlightComponent } from './_partials/spotlight/player-spotlight.component';
import { TournamentSpotlightComponent } from './_partials/spotlight/tournament-spotlight.component';
import { MatchComponent } from './views/match/match.component';
import { MatchCsgoComponent } from './views/match/match-csgo.component';
import {CalculateRoundsPipe} from './pipes/calculate-rounds.pipe';
import { BlogComponent } from './views/blog/blog.component';
import { MatchDota2Component } from './views/match/match-dota2.component';
import { MatchLolComponent } from './views/match/match-lol.component';
import { PlayerCsgoSpotlightComponent } from './_partials/spotlight/player-csgo-spotlight.component';
import { PlayerLolSpotlightComponent } from './_partials/spotlight/player-lol-spotlight.component';
import {PluginService} from './services/plugin.service';
import { MatchBracketComponent } from './_partials/match-bracket.component';
import { CapitalizePipe } from './pipes/capitalize.pipe';
import { ReplaceWithPipe } from './pipes/replace-with.pipe';
import { OrderByNumberPipe } from './pipes/order-by-number.pipe';
import { SharedService } from './services/shared.service';
import { TranslateModule } from 'ng2-translate';
import { PublicationTimePipe } from './pipes/publication-time.pipe';
import { TimeToSecondsPipe } from './pipes/time-to-seconds.pipe';
import { OrderByDatePipe } from './pipes/order-by-date.pipe';
import {SelectModule} from 'ng-select';

import { MatchtickerComponent } from './_partials/matchticker.component';
import { SbkService } from './services/sbk.service';

import { LocalStorageModule } from 'angular-2-local-storage';
import { PostsComponent } from "./_partials/posts.component";
import { ReplaceSortCodePipe } from "./pipes/replace-shortcode.pipe";

const appRoutes: Routes = [
  {path: '', component: IndexComponent},
  {path: 'tournament/:game/:id', component: TournamentComponent},
  {path: 'blog/:game/:client/:id', component: BlogComponent},
  {path: 'match/csgo/:id', component: MatchCsgoComponent},
  {path: 'match/dota2/:id', component: MatchDota2Component},
  {path: 'match/lol/:id', component: MatchLolComponent},
  {path: 'match/all/:id', component: MatchComponent},
];

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    FooterComponent,
    RightSidebarComponent,
    LeftSidebarComponent,
    IndexComponent,
    TournamentComponent,
    SecondsToTimePipe,
    ReplaceSpacePipe,
    CalculateRoundsPipe,
    MatchTableComponent,
    TeamSpotlightComponent,
    PlayerSpotlightComponent,
    TournamentSpotlightComponent,
    MatchComponent,
    MatchCsgoComponent,
    BlogComponent,
    MatchDota2Component,
    MatchLolComponent,
    PlayerCsgoSpotlightComponent,
    PlayerLolSpotlightComponent,
    MatchBracketComponent,
    MatchtickerComponent,
    CapitalizePipe,
    ReplaceWithPipe,
    OrderByNumberPipe,
    PublicationTimePipe,
    TimeToSecondsPipe,
    OrderByDatePipe,
    PostsComponent,
    ReplaceSortCodePipe
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule,
    ChartModule,
    SelectModule,
    TranslateModule.forRoot(),
    RouterModule.forRoot(appRoutes),
    LocalStorageModule.withConfig({
            prefix: '188bet',
            storageType: 'localStorage'
        })
  ],
  providers: [
    ApiServices,
    PluginService,
    SharedService,
    SbkService,
    MatchtickerComponent,
    LocalStorageModule
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
