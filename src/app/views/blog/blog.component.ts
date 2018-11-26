import { Component, OnInit } from '@angular/core';
import { ApiServices } from "../../services/api-services.service";
import { ActivatedRoute } from "@angular/router";
import * as jQuery from 'jquery';

@Component({
  selector: 'blog-page',
  templateUrl: './blog.component.html',
  styles: []
})
export class BlogComponent implements OnInit {

  private post: Object;
  private postID: number;
  private postEmbedText: string;

  constructor( private _apiServices: ApiServices, private _route: ActivatedRoute) { }

  ngOnInit() {

    this._route.params.subscribe(data => {
      this.postID = data['id'];
    });

    this._apiServices.getSinglePost('all', 1, this.postID).subscribe(post => {
      this.post = post;

      this.embedToHtml(post.post);

    })
  }

  embedToHtml(str: string) {
    let elem = jQuery(str),
        found: any = jQuery('*', elem);
        // embedCodes = [
        //     'dota2draft',
        //     'dota2matchgamedraft',
        //     'dota2scoreboard',
        //     'loldraft',
        //     'lolmatchgamedraft',
        //     'lolscoreboard',
        //     'csgodraft',
        //     'csgomatchgamedraft',
        //     'csgoscoreboard',
        //     'matchdetails'
        // ];

    console.log(found);

    for (let i = 0; i < found.length; i++) {

      let tag = jQuery(found[i])[0].localName;
      console.log(tag);

      switch (tag) {
        case 'dota2draft':
        case 'csgodraft':
        case 'loldraft':
          this.draft(tag);
          break;
        case 'dota2matchgamedraft':
        case 'csgomatchgamedraft':
        case 'lolmatchgamedraft':
          this.matchgamedraft(tag);
          break;
        case 'dota2scoreboard':
        case 'csgoscoreboard':
        case 'lolscoreboard':
          this.scoreboard(tag);
          break;
        default:
          console.log('*');
      }
    }
  }

  draft (embed: string) {
    console.log('draft');
  }

  matchgamedraft(embed: string) {
    console.log('matchgamedraft');
  }

  scoreboard(embed: string) {
    console.log('scoreboard');
  }

  matchdetails() {
    console.log('matchdetails');
  }

}
