import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'timeToSeconds'
})
export class TimeToSecondsPipe implements PipeTransform {

  transform(value: any): any {
    return new Date(value);
  }

}
