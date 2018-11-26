jQuery(document).ready(function ($) {

    'use strict';

//    Tablet / Mobile sidebar menu
    var open = true;

    $('.sidebar-bet .uc-list').hide();
    $('.sidebar-bet').on('click', '.uc-select', function(e) {
        $(this).addClass('active');
        $('.sidebar-bet .s-select').removeClass('active');

        $('.sidebar-bet .s-list').hide();
        $('.sidebar-bet .uc-list').show();
    });
    $('.sidebar-bet').on('click', '.s-select', function(e) {
        $(this).addClass('active');
        $('.sidebar-bet .uc-select').removeClass('active');

        $('.sidebar-bet .uc-list').hide();
        $('.sidebar-bet .s-list').show();
    });

    $('.burger').click(function () {

        var height = jQuery('#accordion').height();

        var heightSlide = (open) ? height: 0;

        jQuery('.burger').toggleClass('active');

        open = !open;

        $('.sidebar-menu').animate({height: heightSlide}, 300, function () {
            if (!open) $(this).css('height', 'auto');
        });

    });
    //    Body click
    $('.mobile-button-container').on('click', function () {
        $('body').toggleClass('no-scroll');
        $(this).toggleClass('active');
        $('.main-content .content-container .sidebar-bet').toggleClass('active');
    });

    //    Main sidebar tabs
    /*$('.curent-bet, .history-bet').on('click', function () {
        $('.curent-bet, .history-bet, #curent-bet, #history-bet').removeClass('active');
        var getClass = $(this).attr('class');
        $('#' + getClass).addClass('active');
        $(this).addClass('active');
    });*/

    //  Datepicker
    $('#date-start, #date-final').datepicker({
        monthNames: [ "Januar", "Februar", "Marts", "April", "Maj", "Juni", "Juli", "August", "September", "Oktober", "November", "December" ],
        showOtherMonths: true,
        showButtonPanel: true
    });

    //  Tooltip
    $(document).on({
        mouseenter: function () {

            var tooltip = $(this).find('span'),
                heightTootip = tooltip.height();

            tooltip.css({top: -(heightTootip + 14)});

            $(this).addClass('group-hover');
        },

        mouseleave: function () {
            $(this).removeClass('group-hover');
        }
    }, '.group-tooltip');

    //    Bet Sidebar tabs
    $('.bet-change-tabs .tab').on('click', function () {
        var tab = $(this).attr('data-tab'),
            tab_1 = $('.bet-change-tabs .tab:eq(0)'),
            tab_3 = $('.bet-change-tabs .tab:eq(2)');
        $('.bet-change-tabs .tab').removeClass('active light');
        $(this).addClass('active');
        $('.toggle-tab').css('display', 'none');
        $('.toggle-tab[data-tab='+ tab +']').css('display', 'block');
        if (tab == 1 || tab == 2) tab_3.addClass('light');
        if (tab == 3) tab_1.addClass('light');
    });

    $('.match-page-container #more-matches').click(function() {
      $(this).parent('.match-ticker').toggleClass('expanded');
    });
});

