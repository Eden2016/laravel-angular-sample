import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'publicationTime'
})
export class PublicationTimePipe implements PipeTransform {
    transform(value: string) {

        let result: string;

        // current time
        let now = new Date().getTime();
        // time since message was sent in seconds
        let delta = (now - new Date(value).getTime()) / 1000;

        // format string
        if (delta < 60) {
            result = '1m ago';
        } else if (delta < 3600) {
            result = Math.floor(delta / 60) + 'm ago';
        } else if (delta < 86400) {
            result = Math.floor(delta / 3600) + 'h ago';
        } else {
            result = Math.floor(delta / 86400) + 'd ago';
        }

        return result;

    }
}