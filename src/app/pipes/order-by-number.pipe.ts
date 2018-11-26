import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'orderByNumber'
})
export class OrderByNumberPipe implements PipeTransform {

  transform(array: Array<Object>, args: string): Array<Object> {
    if (array !== null) {
      array.sort((a: any, b: any) => {
        if (a.points > b.points) {
          return -1;
        } else if (a.points < b.points) {
          return 1;
        } else {
          return 0;
        }
      });
    }
      return array;
  }
}
