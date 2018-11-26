import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'secondsToTime'
})
export class SecondsToTimePipe implements PipeTransform {
  transform(value: any): any {
    var seconds = Math.floor(value / 1000);
    var minutes = Math.floor(seconds/60);
    var hours = Math.floor(minutes/60);
    var days = Math.floor(hours/24);

    hours = hours-(days*24);
    minutes = minutes-(days*24*60)-(hours*60);


    if(days > 0){
      return days + 'd ' + hours + 'h ' + minutes + 'm ';
    }else if(days == 0 && hours > 0 && minutes > 0){
       return hours + 'h ' + minutes + 'm ';
     }else{
       return minutes + 'm ';
     }
  }

}
