import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'orderByDate'
})
export class OrderByDatePipe implements PipeTransform {

  transform(array: Array<Object>, args: string): Array<Object> {
    if (array !== null) {
      array.sort((a: any, b: any) => {
        if (a.start_date < b.start_date) {
          return -1;
        } else if (a.start_date > b.start_date) {
          return 1;
        } else {
          return 0;
        }
      });
    }
      return array;
  }

}
