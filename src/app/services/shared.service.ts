import { Injectable, EventEmitter} from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Injectable()
export class SharedService {

  languageQuery = new EventEmitter<string>();

  constructor(private _activatedRoute: ActivatedRoute) {}

  getLanguageQuery() {
    return this.languageQuery;
  }

  setLanguageQuery(lang: string) {
    this.languageQuery.emit(lang);
  }

}
