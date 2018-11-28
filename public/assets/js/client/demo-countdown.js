/*!
 CountDown Demo Script
 Copyright © 2018 5Studios.net
 http://5studios.net
 */

'use strict';
$(function($) {
    Number.prototype.pad = function(p) {
        return ('000' + this).slice(-p);
    };

    Number.prototype.toPluralizedString = function(m) {
        return m + (this > 1 ? 's' : '');
    };

    $(".count-down").each(function() {
        var $countdown = $(this);

        //if ($countdown.length) {
            var targetDate = $countdown.data("target");
            var part = targetDate.slice(-1);
            var launchDate = ['d'].indexOf(part) > -1 ? new Date((new Date()).getTime() + (targetDate.slice(0, -1)) * 86400000) : new Date(targetDate);

            var countDown = setInterval(function() {
                var today = Date.now();
                var days, hours, minutes, seconds, remainingSeconds;

                // seconds from today to launch date
                seconds = remainingSeconds = Math.floor((launchDate - today) / 1000);

                if (seconds > 0) {
                    // 1 day = 86400 seconds
                    days = (Math.floor(seconds / 86400));
                    remainingSeconds -= days * 86400; // take the days

                    // 1 hour = 3600 seconds
                    hours = (Math.floor(remainingSeconds / 3600));
                    remainingSeconds -= hours * 3600; //take the hours

                    // 1 minute = 60 seconds
                    minutes = (Math.floor(remainingSeconds / 60));
                    remainingSeconds -= minutes * 60; //take the minutes

                    seconds = (Math.floor(remainingSeconds)); // set the remaining seconds
                } else {
                    days = hours = minutes = seconds = 0;
                    clearInterval(countDown);
                }

                $countdown.html(
                    '<li><span>' + days.pad(2) + '</span><p> ' + days.toPluralizedString('day') + '</p></li>' +
                    '<li><span>' + hours.pad(2) + '</span><p> ' + hours.toPluralizedString('hour') + '</p></li>' +
                    '<li><span>' + minutes.pad(2) + '</span><p> ' + minutes.toPluralizedString('minute') + '</p></li>' +
                    '<li><span>' + seconds.pad(2) + '</span><p> ' + seconds.toPluralizedString('second') + '</p></li>');
            }, 1000);
        //}
    });
});
