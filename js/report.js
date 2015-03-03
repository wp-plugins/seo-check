function reportWidth(report_id, parent_class) {
    var parent_width = jQuery('.' + parent_class).width(),
            font_size = parent_width / 8,
            percent = jQuery('.col.can-float.factors-percent'),
            score = jQuery('.col.can-float.factors-score'),
            printscreen = jQuery('.col.can-float.factors-site'),
            factorname = jQuery('.col.can-float.factor-name'),
            factordata = jQuery('.col.can-float.factor-data'),
            backlinks = jQuery('#givemepie .col.can-float'),
            speedlabel = jQuery('.col.can-float.speed-label'),
            speedprogress = jQuery('.col.can-float.speed-progress'),
            speedgrade = jQuery('.col.can-float.speed-grade');
    if (font_size > 100) {
        font_size = 100;
    }
    if (font_size < 73) {
        font_size = 73;
    }
    if (parent_width < 479) {
        percent.removeClass('col-sm-6 col-md-3').addClass('col-xs-12');
        score.removeClass('col-sm-6 col-md-4').addClass('col-xs-12');
        printscreen.removeClass('col-md-5').addClass('can-hide');
        factorname.removeClass('col-sm-12 col-md-4').addClass('col-xs-12');
        factordata.removeClass('col-xs-12 col-md-8').addClass('col-sm-12');
        backlinks.removeClass('col-md-4 col-sm-6').addClass('col-xs-12');
        speedlabel.removeClass('col-md-4 col-sm-12').addClass('col-xs-12');
        speedprogress.removeClass('col-md-7 col-sm-12').addClass('col-xs-12');
        speedgrade.removeClass('col-md-1 col-sm-12').addClass('col-xs-12');
    }
    if (parent_width > 480 && parent_width < 767) {
        percent.removeClass('col-xs-12 col-md-3').addClass('col-sm-6');
        score.removeClass('col-xs-12 col-md-4').addClass('col-sm-6');
        printscreen.removeClass('col-md-5').addClass('can-hide');
        factorname.removeClass('col-xs-12 col-md-4').addClass('col-sm-12');
        factordata.removeClass('col-xs-12 col-md-8').addClass('col-sm-12');
        backlinks.removeClass('col-md-4 col-xs-12').addClass('col-sm-6');
        speedlabel.removeClass('col-md-4 col-xs-12').addClass('col-sm-12');
        speedprogress.removeClass('col-md-7 col-xs-12').addClass('col-sm-12');
        speedgrade.removeClass('col-md-1 col-xs-12').addClass('col-sm-12');
    }
    if (parent_width > 768) {
        percent.removeClass('col-xs-12 col-sm-6').addClass('col-md-3');
        score.removeClass('col-xs-12 col-sm-6').addClass('col-md-4');
        printscreen.removeClass('can-hide').addClass('col-md-5');
        factorname.removeClass('col-xs-12 col-sm-12').addClass('col-md-4');
        factordata.removeClass('col-xs-12 col-sm-12').addClass('col-md-8');
        backlinks.removeClass('col-sm-6 col-xs-12').addClass('col-md-4');
        speedlabel.removeClass('col-xs-12 col-sm-12').addClass('col-md-4');
        speedprogress.removeClass('col-xs-12 col-sm-12').addClass('col-md-7');
        speedgrade.removeClass('col-xs-12 col-sm-12').addClass('col-md-1');
    }
    jQuery(report_id).width(parent_width); //removed the "- 20"
    jQuery(report_id).children('.row').css('font-size', font_size + '%');
    jQuery(report_id).show();
    if (jQuery(report_id).html() == '') {
        jQuery(report_id).attr('data-display', 'pending');
    } else {
        jQuery(report_id).attr('data-display', 'loaded');
    }
}

function reportResize() {
    var report_id = '#erreport', parent_class = 'superreport-seo';
    jQuery(report_id).parent().addClass(parent_class);
    reportWidth(report_id, parent_class);
    jQuery(window).resize(function () {
        reportWidth(report_id, parent_class);
    });
}

function niceToggle(id) {
    jQuery('#' + id + ' i.expandtoggle').toggleClass('show-details');
    if (jQuery('#' + id + ' i.expandtoggle').hasClass('show-details')) {
        jQuery('#' + id + ' i.expandtoggle').removeClass('fa-plus').addClass('fa-minus');
    } else {
        jQuery('#' + id + ' i.expandtoggle').removeClass('fa-minus').addClass('fa-plus');
    }
    jQuery('#' + id + ' .factor-info').toggle();
}


var reportScoreCircle;

function reportinit() {
    if (jQuery('#circles') && jQuery('#circles').attr('data-circle-started') != "1") {
        reportScoreCircle = Circles.create({
            id: 'circles',
            radius: 70,
            value: jQuery('#circles').attr('data-percent'),
            maxValue: 100,
            width: 10,
            text: "",
            colors: ['#E5E5E5', '#0281C4'],
            duration: 400,
            wrpClass: 'circles-wrp',
            textClass: 'circles-text'
        });
        jQuery('#circles').attr('data-circle-started', 1);
    }

    try {
        jQuery(".erankertooltip[title]").tooltip({
            show: {
                effect: "slideDown",
                delay: 250
            },
            position: {
                my: "left top",
                at: "left bottom"
            },
            placement: "bottom"
        });
    } catch (e) {

    }
}

jQuery(document).ready(function () {
    reportResize();
    reportinit();
    downloadFactorsHTML();
});



function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}

var reportDownloadRetries = 0;


function updateReportScore(value) {
    if (console) {
        console.log("Updating scores...");
    }
    reportScoreCircle.update(Math.round(value.percentage), 300);

    jQuery('.superreport-seo .overall-score h1 ').html(Math.round(value.percentage));
    jQuery('#rating-stars .rating-stars').css('width', (Math.round(value.percentage) / 10 * 10.6) + 'px');
    //Multi colors not implemented yet....
    //reportScoreCircle.updateColors(['#E5E5E5', '#0281C4']);

    var total = value.factors.green + value.factors.orange + value.factors.red + value.factors.missing;

    jQuery('.score-table .factors-score .green .factor-score span').html(value.factors.green);
    jQuery('.score-table .factors-score .green .factorbar').css('width', Math.round((value.factors.green / total) * 100) + '%');
    jQuery('.score-table .factors-score .orange .factor-score span').html(value.factors.orange);
    jQuery('.score-table .factors-score .orange .factorbar').css('width', Math.round((value.factors.orange / total) * 100) + '%');
    jQuery('.score-table .factors-score .red .factor-score span').html(value.factors.red);
    jQuery('.score-table .factors-score .red .factorbar').css('width', Math.round((value.factors.red / total) * 100) + '%');
}

function downloadFactorsHTML() {

    reportDownloadRetries++;
    if (reportDownloadRetries > 120 || jQuery(".erfactor[data-factorready='0']").size <= 0) { // retry until finished or 10 minutes
        return;
    }
    if (console) {
        console.log("Downloading missing factors...");
    }
    var factorList = "";
    jQuery(".erfactor[data-factorready='0']").each(function (idx, el) {
        factorList += jQuery(el).attr('data-id') + ",";
    });
    factorList = factorList.substring(0, factorList.length - 1);


    if (factorList === "") {
        if (console) {
            console.log("Finished download the factors data.");
        }
        return;
    }
    var jsonURL = updateQueryStringParameter(updateQueryStringParameter(window.location.href, "factors", factorList), "ajax", "1");


    jQuery.getJSON(jsonURL, function (data) {
        updateReportScore(data.score);

        if (data.status !== 'DONE') {
            //Try download again in 5 seconds
            setTimeout(function dfact() {
                downloadFactorsHTML();
            }, 3000);
        } else {
            if (console) {
                console.log("Finished download the factors data.");
            }
        }
        jQuery.each(data, function (index, value) {
            if (index === "score" || index === "status") {
                return;
            }
            var section = jQuery('.erfactor[data-id="' + index + '"]');
            section.find(".factor-data").html(value.html);

            var statusclass = "info";
            switch (value.status) {
                case "RED":
                case "MISSING":
                    statusclass = 'times';
                    break;
                case "ORANGE":
                    statusclass = 'minus';
                    break;
                case "GREEN":
                    statusclass = 'check';
                    break;
                case "NEUTRAL":
                    statusclass = 'info';
                    break;
                default:
                    statusclass = "question-circle";
                    break;
            }
            var statuscolor = value.status.toLowerCase();

            section.find(".factor-name").html('<i class="fa fa-' + statusclass + ' ' + statuscolor + '"></i> ' + value.friendly_name);

        });
        reportinit();

    }).fail(function () {
        //If an error happens, try download again in 10 seconds
        setTimeout(function dfact() {
            downloadFactorsHTML();
        }, 10000);
    });


}

function printSeoReport() {
    jQuery("#erreport").print();
}