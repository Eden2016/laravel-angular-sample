import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'replaceWith'
})
export class ReplaceWithPipe implements PipeTransform {

  transform(value: any, searchedChar:any, replacedChar:string): any {
    var searchChar = new RegExp(searchedChar, 'g');
    return value.replace(searchChar, replacedChar);
  }

}
