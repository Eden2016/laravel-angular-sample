import { Component, OnInit, Input } from '@angular/core';
import { PluginService } from '../services/plugin.service';
import 'rxjs/add/operator/map';

@Component({
    selector: 'app-bracket-match',
    templateUrl: './match-bracket.component.html',
    styles: [ `` ]
})

export class MatchBracketComponent implements OnInit {

    @Input() bracketData: Object[];
    @Input() containerName: string;
    @Input() singleElThirdPlace: boolean;
    @Input() upperBracketOnly: boolean;
    constructor( private _pluginService: PluginService) {}

    ngOnInit() {
            this.containerName = this.containerName.replace(/#/g, '');
            this._pluginService.bracketInit(this.bracketData, '.bracket-cont-' +  this.containerName + ' .bracket-plugin', this.singleElThirdPlace, this.upperBracketOnly, this.containerName);
    }
}
