import {Injectable} from '@angular/core';
import { TranslateService } from 'ng2-translate';
import * as jQuery from 'jquery';
import 'slick-carousel';

 declare var bracket:any;

@Injectable()
export class PluginService {

  constructor(private _translateService: TranslateService) { }

  slickSliderPlayer(elem:string) {
    var svgArrowNext = '<svg class="next" version="1.1" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
    var svgArrowPrev = '<svg class="prev" version="1.1" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
    var parameter =  {
      infinite: true,
      slidesToShow: 4,
      slidesToScroll: 1,
      marginLeft: 20,
      nextArrow: '<div class="next-slide">' + svgArrowNext + '</div>',
      prevArrow: '<div class="prev-slide">' + svgArrowPrev + '</div>',
      responsive: [
        {
          breakpoint: 1601,
          settings: {
            slidesToShow: 3,
          }
        },
      ]
    };

    setTimeout(function () {
      jQuery(elem).slick(parameter);
    })
  }

  slickSliderOutrightMarkets(elem: string) {
    setTimeout(function () {

      var parameter = {
        dots: false,
        infinite: false,
        slidesToShow: 2,
        slidesToScroll: 1,
        prevArrow: '',
        nextArrow: '',
        speed: 300,
        responsive: [
          {
            breakpoint: 1367,
            settings: {
              slidesToShow: 1,
            }
          },
        ]
      };

      jQuery(elem).slick(parameter);

      // Draggable false
      jQuery(elem).slick("slickSetOption", "draggable", false, false);
      // Outright slider Next slide
      jQuery('.left-arr').on('click', function () {
        jQuery(elem).slick('slickPrev');
      });
      // Outright slider Next slide
      jQuery('.right-arr').on('click', function () {
        jQuery(elem).slick('slickNext');
      });
    })
  }

  slickSliderParticipants(elem:string) {


    setTimeout(function () {
      var parameter = {
        dots: false,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        slidesPerRow: 4,
        rows: 3,
        vertical: true,
        prevArrow: '',
        nextArrow: '',
        speed: 300
      };

      jQuery(elem).slick(parameter);

      //    Outright slider Next slide
      jQuery('.top-arr').on('click', function () {
        jQuery(elem).slick('slickPrev');
      });

      //   Outright slider Next slide

      jQuery('.bottom-arr').on('click', function () {
        jQuery(elem).slick('slickNext');
      });
    });
  }

  slickSliderParticipantsGame(elem:string) {
    let that = this;
    var svgArrowNext = '<svg class="next" version="1.1" width="16px" height="13px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
    var svgArrowPrev = '<svg class="prev" version="1.1" width="16px" height="13px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
    var parameter = {
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      marginLeft: 20,
      dots: true,
      nextArrow: '<div class="next-slide">' + svgArrowNext + '</div>',
      prevArrow: '<div class="prev-slide">' + svgArrowPrev + '</div>',
      customPaging : function(slider, i) {
        i++;
        return '<button>' + that.getTranslation('Game') + ' ' + i +'</button>';
      }
    };
    setTimeout(function () {
      jQuery(elem).slick(parameter);
    })
  }

  slickSliderParticipantsGameCsgo(elem:string) {
    let that = this;
    var svgArrowNext = '<svg class="next" version="1.1" width="16px" height="13px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
    var svgArrowPrev = '<svg class="prev" version="1.1" width="16px" height="13px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
    var parameter = {
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      marginLeft: 20,
      dots: true,
      nextArrow: '<div class="next-slide">' + svgArrowNext + '</div>',
      prevArrow: '<div class="prev-slide">' + svgArrowPrev + '</div>',
      customPaging : function(slider, i) {
        var html = '',
          scoreTeam1 = jQuery(slider.$slides[i]).data('score-team-1'),
          scoreTeam2 = jQuery(slider.$slides[i]).data('score-team-2');
        i++;
        
        var  score = '<button>'+ that.getTranslation('Game') + ' ' + i +' (<span class="score-dots active">' + scoreTeam1 + '</span>:<span class="score-dots">' + scoreTeam2 + '</span>)</button>';
        return score;
      }
    };
    setTimeout(function () {
      jQuery(elem).slick(parameter);
    })
  }

  slickSliderGroup(){
    var svgArrowNext = '<svg class="next" version="1.1" width="16px" height="13px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
    var svgArrowPrev = '<svg class="prev" version="1.1" width="16px" height="13px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g><g id="chevron-right"><polygon points="94.35,0 58.65,35.7 175.95,153 58.65,270.3 94.35,306 247.35,153"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

    setTimeout(function () {
      $('.nav.stage-slider a:first').tab('show');
      $('.nav.group-slider a:first').tab('show');

        jQuery('.group-slider').each(function(){
          if (jQuery(this).children('li').length > 5) {
            jQuery(this).slick({
              slidesToScroll: 1,
              slidesToShow: 5,
              infinite: false,
              nextArrow: svgArrowNext,
              prevArrow: svgArrowPrev,
              appendArrows: jQuery(this).parents('.tab-pane')
            });
          }
        });


      // this is for stages slider      
      jQuery('.stage-slider').each(function(){
        if (jQuery(this).children('li').length > 4) {
            jQuery(this).slick({
              slidesToScroll: 1,
              slidesToShow: 4,
              infinite: false,
              variableWidth: true,
              nextArrow: svgArrowNext,
              prevArrow: svgArrowPrev,
              appendArrows: jQuery(this).parents('.stages-cont')
            });
        }
      });
     });
  }

  bracketInit(data: any[], elem: string, singleElThirdPlace: boolean, upperBracketOnly: boolean, uniqueStageName: string) {
    // timeout needed to wait dom to load   
    setTimeout(function(){
      if (data) {
      bracket(data, {
        renderTo: jQuery(elem),
        upperBracketOnly: upperBracketOnly,
        singleEliminationThirdPlaceMatch: singleElThirdPlace,
        uniqueStageName: uniqueStageName
      });
    }

    });
  }

  // translating dynamic content
  getTranslation(word: string): string {
    let result;
    this._translateService.get(word).subscribe((res: string) => {
      result = res;
    });
    return result;
  }

}
