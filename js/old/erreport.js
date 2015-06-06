/*
 * eRanker Report JS
 */
function processErReport() {
    reportResize();
    is_processed = true;
    var progress_cats = [];
    $('[data-category].row').each(function() { 
        if( $.inArray( $(this).attr('data-category'), progress_cats) == -1 ) 
            progress_cats.push( $(this).attr('data-category') ); 
    });
    for( var i = 0; i < progress_cats.length; i++ ) {
        var progress_factors = 0, progress_green = 0;
        $('[data-category="' + progress_cats[i] + '"]').each(function(){
            progress_factors++; 
            if( $(this).children('section').attr('data-status') == 'GREEN' ) { 
                progress_green++;
            } 
        });
        var progress_id = $('#progress-' + progress_cats[i].toLowerCase().replace(/\s/g, '') );
        progress_id.html('<style>#' + progress_id.attr('id') + ' p::after{width:' + ( 100 * progress_green / progress_factors ) + '%;}</style><p>' + progress_green + '/' + progress_factors + '</p>');
    }
    
    if( !$('#givemepie').html() ) {
        var pies = 0;
        $('.hidden.piechart').each(function(){
            var labels = [], values = [], pieclass = 'col can-float pdf-pie';
            $(this).children('.data-chart').each(function(){
                labels.push( $(this).attr('data-label') );
                values.push( $(this).attr('data-value') );

            });
            var total = values.length, sum = 0, percentvalues = [];
            while(total--) sum += parseFloat(values[total]) || 0;
            for( var i = 0; i < values.length; i++ ) percentvalues.push( parseFloat(values[i]*100/sum).toFixed(1) );
            for( var i = 0; i < percentvalues.length; i++ ) if( isNaN(percentvalues[i]) ) pieclass = 'hidden';
            $('#givemepie').append('<div id="mypie' + pies + '" class="' + pieclass + '"></div>');
            if( values.length === 3 && ( values[0] !== '0' || values[1] !== '0' || values[2] !== '0' ) ) {
                createD3PieChart( $(this), 'div#givemepie #mypie' + pies, 160, 200, 70, labels, percentvalues, 1, 1.4,values );
            } 
            if( values.length === 2 && ( values[0] !== '0' || values[1] !== '0' ) ) {
                 createD3PieChart( $(this), 'div#givemepie #mypie' + pies, 160, 200, 70, labels, percentvalues, 1, 1.4, values );
            }
            labels = [];
            values = [];
            percentvalues = [];
            pies++;
        });
    } 
    if( $('#basic-google-map').length ) {
        createMap(
                '#basic-google-map', 
                $('#basic-google-map').attr('data-maplat'), 
                $('#basic-google-map').attr('data-maplng'), 
                $('#basic-google-map').hasClass('map-on-pdf') ? '500px' : $('#basic-google-map').parent().width() + 'px', 
                '200px',//right value for a good look in pdf
                $('#basic-google-map').attr('data-title')
            );
    }
    if($('#circles')) {
        Circles.create({
            id:         'circles', 
            radius:     70,
            value:      jQuery('#circles').attr('data-percent'),
            maxValue:   100,
            width:      10,
            text:       "",			
            colors:     ['#E5E5E5', '#0281C4'],
            duration:   400,
            wrpClass:   'circles-wrp',
            textClass:  'circles-text'

        })
    };
}

function niceToggle(id) {
    $('#' + id + ' i.expandtoggle').toggleClass('show-details');
    if( $('#' + id + ' i.expandtoggle').hasClass('show-details') ) {
        $('#' + id + ' i.expandtoggle').removeClass('fa-plus').addClass('fa-minus');
    } else {
        $('#' + id + ' i.expandtoggle').removeClass('fa-minus').addClass('fa-plus');
    }
    $('#' + id + ' .factor-info').toggle();
}

function createD3PieChart(chartHtml, place, width, height, radius, labels, values, pos1, pos2,initialvalues){
    var showLabels = chartHtml.attr("data-labels");
    var donut = chartHtml.attr("data-donut");
    var posValChart = chartHtml.attr("data-pos-values");
    if (donut === 'true' ) donut = true;//donut chart
    else if (donut === 'false' ) donut = false ; //pie chart
    else donut = false;
    if (posValChart === 'true' ) posValChart = true;//inside values
    else if (posValChart === 'false' ) posValChart = false ; //outside values
    else posValChart = false;
    color = d3.scale.category20();
    var dataSet = new Array();
    for (i = 0; i < labels.length; i++) {
      dataSet[i] = {
        'legendLabel': labels[i],
        'magnitude': values[i]
      }
    }
    var vis = d3.select(place) .append('svg:svg') .data([dataSet]) .attr('width', width) .attr('height', height) .append('svg:g') .attr('transform', 'translate(' + pos1 * radius + ',' + pos2 * radius + ')')
    if (donut === false) {
      var arc = d3.svg.arc() .outerRadius(radius) .innerRadius(0);
    } 
    else {
      var arc = d3.svg.arc() .outerRadius(radius) .innerRadius(radius / 1.5);
    }
    var pie = d3.layout.pie() .value(function (d) {
      return d.magnitude;
    }) .sort(function (d) {
      return null;
    });
    var arcs = vis.selectAll('g.slice') .data(pie) .enter() .append('svg:g') .attr('class', 'slice');
    arcs.append('svg:path') .attr('fill', function (d, i) {
      return color(i);
    }).attr('d', arc);
     if(posValChart === true){
    if(showLabels === "true") {
        arcs.append('svg:text') .attr('transform', function (d) {
          var c = arc.centroid(d),
          x = c[0],
          y = c[1],
          h = Math.sqrt(x * x + y * y);
          return 'translate(' + (x / h * (radius + 20)) + ',' + ((y / h * (radius + 20)) + 10) + ')';
        }) .attr('text-anchor', 'middle') .style('fill', 'Purple') .style('font', 'bold 10px Arial') .text(function (d, i) {
          //return dataSet[i].legendLabel;
        });
         arcs.filter(function (d) {
      return d.endAngle - d.startAngle > 0.2;
    }) .append('svg:text') .attr('dy', '.35em') .attr('text-anchor', 'middle') .attr('transform', function (d) {
      return 'translate(' + arc.centroid(d) + ')rotate(' + angle(d) + ')';
    }) .attr('transform', function (d) {
      d.outerRadius = radius;
      d.innerRadius = radius / 2;
      return 'translate(' + arc.centroid(d) + ')rotate(' + angle(d) + ')';
    }) .style('fill', 'White') .style('font', 'bold 10px Arial') .text(function (d) {
      return d.data.magnitude+'%';
    });
    }     
     }
     else{
         arcs.append('svg:text') .attr('transform', function (d) {
          var c = arc.centroid(d),
          x = c[0],
          y = c[1],
          h = Math.sqrt(x * x + y * y);
          return 'translate(' + (x / h * (radius + 20)) + ',' + ((y / h * (radius + 20)) + 10) + ')';
        }) .attr('text-anchor', 'middle') .style('fill', 'Purple') .style('font', 'bold 10px Arial') .text(function (d, i) {
          return dataSet[i].legendLabel+"\n\r "+dataSet[i].magnitude;
        });
    }
    var legend = d3.select(place) .append('svg') .attr('class', 'legend') .attr('width', ((radius * 2)+20)) .attr('height', radius * 2) .selectAll('g') .data(color.domain() .slice()) .enter() .append('g') .attr('transform', function (d, i) {
      return 'translate(0,' + i * 20 + ')';
    });
    legend.append('rect') .attr('width', 16) .attr('height', 16) .style('fill', color);
    legend.append('text') .attr('x', 24) .attr('y', 9) .attr('dy', '.30em') .style('font', 'bold 10px Arial') .text(function (d, i) {
      return dataSet[i].legendLabel;
    }).append('tspan').style('fill', '#375A59').text(function (d, i) {  return ' (' + initialvalues[i] + ')'; }); //+ dataSet[i].magnitude + '%)'
}

function angle(d) {
    var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
    return a > 90 ? a - 180 : a;
}

function createMap(containerName, lat, lng, width, height, description) {
    var map = new GMaps({
        div: containerName,
        lat: lat,
        lng: lng,
        width: width,
        height: height,
        zoom: 12,
        zoomControl: true,
        zoomControlOpt: {
            style: 'SMALL',
            position: 'TOP_LEFT'
        },
        panControl : false,
      });
      map.addMarker({
      lat: lat,
      lng: lng,
      title: description,
      infoWindow: {
          content: '<h6>' + description + '</h6>'
      }
    });
}

function reportReady(report_token, report_domain) {
    var result = false;
    $.ajax({ 
        url: templateurl + '/ajax/reportpending.php?t=' + report_token, 
        timeout: 10000,
        type: "GET", 
        dataType: "text",
        success: function(response) {
            if(response === "0") {
                window.location = "/seo/?domain=" + report_domain + "&token=" + report_token;
                report_pending = false;
                result = true;
            }
        },
        error: function(err) {
             console.log("Error");
        },
        async: true
        });
    return result;
}

function hasSupport(support_type) {
    var addSupport;
    switch(support_type) {
        case 'pdf': {
            addSupport = $('#download-pdf').attr('href', websiteurl + '/pdf/?token=' + report_token + '&domain=' + report_domain);
            break;
        } 
        case 'update': { 
            addSupport = $('#update-now').attr('href', websiteurl + '/seo/?domain=' + report_domain + '&update=1');
            break;
        }
        default: alert('This feature is disabled.'); 
    }
    return addSupport;
}

function reportWidth(report_id, parent_class) {
    var parent_width 	= $('.' + parent_class).width(),
        font_size 	= parent_width / 8,
        percent 	= $('.col.can-float.factors-percent'),
        score 		= $('.col.can-float.factors-score'),
        printscreen 	= $('.col.can-float.factors-site'),
        factorname 	= $('.col.can-float.factor-name'),
        factordata 	= $('.col.can-float.factor-data'),
        backlinks 	= $('#givemepie .col.can-float'),
        speedlabel	= $('.col.can-float.speed-label'),
        speedprogress 	= $('.col.can-float.speed-progress'), 
        speedgrade	= $('.col.can-float.speed-grade'); 
    if( font_size > 100 ) {
        font_size = 100;
    }
    if( font_size < 73 ) {
        font_size = 73;
    }
    if( parent_width < 479 ) {
        percent		.removeClass('col-sm-6 col-md-3')	.addClass('col-xs-12');
        score		.removeClass('col-sm-6 col-md-4')	.addClass('col-xs-12');
        printscreen	.removeClass('col-md-5')		.addClass('can-hide');
        factorname	.removeClass('col-sm-12 col-md-4')	.addClass('col-xs-12');
        factordata	.removeClass('col-xs-12 col-md-8')	.addClass('col-sm-12');
        backlinks	.removeClass('col-md-4 col-sm-6')	.addClass('col-xs-12');
        speedlabel	.removeClass('col-md-4 col-sm-12')	.addClass('col-xs-12');
        speedprogress	.removeClass('col-md-7 col-sm-12')	.addClass('col-xs-12');
        speedgrade	.removeClass('col-md-1 col-sm-12')	.addClass('col-xs-12');
    }
    if( parent_width > 480 && parent_width < 767 ) {
        percent		.removeClass('col-xs-12 col-md-3')	.addClass('col-sm-6');
        score		.removeClass('col-xs-12 col-md-4')	.addClass('col-sm-6');
        printscreen	.removeClass('col-md-5')		.addClass('can-hide');
        factorname	.removeClass('col-xs-12 col-md-4')	.addClass('col-sm-12');
        factordata	.removeClass('col-xs-12 col-md-8')	.addClass('col-sm-12');
        backlinks	.removeClass('col-md-4 col-xs-12')	.addClass('col-sm-6');
        speedlabel	.removeClass('col-md-4 col-xs-12')	.addClass('col-sm-12');
        speedprogress	.removeClass('col-md-7 col-xs-12')	.addClass('col-sm-12');
        speedgrade	.removeClass('col-md-1 col-xs-12')	.addClass('col-sm-12');
    }
    if( parent_width > 768 ) {
        percent		.removeClass('col-xs-12 col-sm-6')	.addClass('col-md-3');
        score		.removeClass('col-xs-12 col-sm-6')	.addClass('col-md-4');
        printscreen	.removeClass('can-hide')		.addClass('col-md-5');
        factorname	.removeClass('col-xs-12 col-sm-12')	.addClass('col-md-4');
        factordata	.removeClass('col-xs-12 col-sm-12')	.addClass('col-md-8');
        backlinks	.removeClass('col-sm-6 col-xs-12')	.addClass('col-md-4');
        speedlabel	.removeClass('col-xs-12 col-sm-12')	.addClass('col-md-4');
        speedprogress	.removeClass('col-xs-12 col-sm-12')	.addClass('col-md-7');
        speedgrade	.removeClass('col-xs-12 col-sm-12')	.addClass('col-md-1');
    }
    $(report_id).width(parent_width - 20);
    $(report_id).children('.row').css('font-size', font_size + '%');
    $(report_id).show();
    if( $(report_id).html() == '' ) {
        $(report_id).attr('data-display', 'pending');
    } else {
        $(report_id).attr('data-display', 'loaded');
    }
}

function reportResize() {
    var report_id = '#erreport-seo', parent_class = 'superreport-seo';
    $(report_id).parent().addClass(parent_class);
    reportWidth(report_id, parent_class);
    $(window).resize(function() {
        reportWidth(report_id, parent_class);
    });
}