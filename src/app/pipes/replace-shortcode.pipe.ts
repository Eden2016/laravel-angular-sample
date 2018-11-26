import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'replaceShortCode'
})
export class ReplaceSortCodePipe implements PipeTransform {

    constructor () {

    }

    transform(text: string): string {

        // console.log(text);

        return '<p>0</p>';

    }

}
