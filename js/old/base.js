jQuery(document).ready(function() {
    
    jQuery(".sat_page .sat_topreport li").tooltip({
        track: true,
        html: true,
        content: function() {
            return jQuery(this).prop('title');
        }
    });
    jQuery(".sat_page .sat_tabs").tabs();
    jQuery(".sat_page .sat_citieslist").sortable({placeholder: "ui-state-highlight"});
    jQuery(".sat_page .sat_citieslist").disableSelection();
    jQuery('.sat_page form.sat_newreport input[type=text]').keydown(function(event) {
        if ((event.keyCode == 13)) {
            event.preventDefault();
        }
    });
    jQuery('.sat_page .sat_actualcity').keydown(function(event) {
        if ((event.keyCode == 13)) {
            event.preventDefault();
            sat_addcustomcity(jQuery('.sat_citynotfoundmsg'), jQuery('.sat_actualcity'), jQuery('.sat_customcitieslist'), jQuery('.sat_countriesselectbox'), jQuery('.sat_maxcities'), jQuery('.sat_citieslist'));
        }
    });

    jQuery(function() {
        jQuery("#sat_error-modal").dialog({
            modal: true,
            width: 450,
            buttons: {
                Ok: function() {
                    jQuery(this).dialog("close");
                }
            }
        });
    });

});



function sat_arrayunique(a) {

    return a.filter(function(v, i, a) {
        return a.indexOf(v) == i
    });
}
function sat_updatecitylist(maxcitiesobj, sockscityobj, citieslistobj, countrycode) {    
    var totalc = maxcitiesobj.val();
    var arr1 = sockscityobj.val().split(";");
    var allc = sat_arrayunique(arr1);
    if (allc.length < 1 || sockscityobj.val() === "") {
        citieslistobj.html('');
        sockscityobj.val('');
        return;
    }
    var joined = allc.join(';');
    sockscityobj.val(joined);
    var stringout = "";
    for (var i in allc) {
        if (parseInt(i) < parseInt(totalc)) {
            stringout += "<li class=\"ui-state-default\"><div class=\"sat_flagcountry\" style=\"background-image: url('" + sat_flagfolder + countrycode + ".png')\"></div> <span class=\"ui-icon ui-icon-close\" onclick=\"sat_removecityfromlist('" + allc[i] + "', jQuery('.sat_maxcities'), jQuery('.sat_customcitieslist'), jQuery('.sat_citieslist'),jQuery('.sat_countriesselectbox').children('option:selected').val())\" ></span> " + allc[i] + "</li> ";
        }
    }
    stringout += "<br/><br/>";
    citieslistobj.html(stringout);
    citieslistobj.sortable({placeholder: "ui-state-highlight"});
    citieslistobj.disableSelection();
}

function sat_removecityfromlist(citytoremove, maxcitiesobj, sockscityobj, citieslistobj, countrycode) {
    var fixedA = new Array();
    var a = sat_arrayunique(sockscityobj.val().split(";"));
    for (var i = 0; i < a.length; i++) {
        if (a[i] != citytoremove) {
            fixedA.push(a[i]);
        }
    }
    var joined = fixedA.join(';');
    sockscityobj.val(joined);
    sat_updatecitylist(maxcitiesobj, sockscityobj, citieslistobj, countrycode);
}




function sat_addcustomcity(citynotfoundmsg, actualcity, sockscity, countryselect, maxcitiesobj, citieslistobj) {
    citynotfoundmsg.hide();
    if (actualcity.val().replace(/\s+/g, "").length < 1) {
        return false;
    }
    if (countryselect.val() === '') {
        window.alert("You must check one country.");
        return false;
    }
    var data = countryselect.children('option:selected').data();
    var geocoder2;
    geocoder2 = new google.maps.Geocoder();
    geocoder2.geocode({
        'address': actualcity.val() + ", " + data.name,
        'region': data.code
    }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            var oncountry = false;
            for (var i = 0; i < results.length; i++) {
                if (results[i].address_components.length != 1 && results[i].address_components[results[i].address_components.length - 1]['short_name'] == data.code) {
                    oncountry = true;
                }
                if (results[i].address_components.length > 1 && results[i].address_components[results[i].address_components.length - 2]['short_name'] == data.code) {
                    oncountry = true;
                }
                if (results[i].address_components.length > 2 && results[i].address_components[results[i].address_components.length - 3]['short_name'] == data.code) {
                    oncountry = true;
                }
            }
            if (!oncountry) {
                citynotfoundmsg.show();
                return;
            }

            if (sockscity.val() != '') {
                sockscity.val(";" + sockscity.val());
            }

            var finalname = "";
            for (var i = 0; i < results.length; i++) {
                if (results[i].address_components[results[i].address_components.length - 1]['short_name'] == data.code) {
                    for (var b = 0; b < results[i].address_components.length - 1; b++) {
                        if (results[i].address_components[b]['short_name'].length <= 2) {
                            finalname += results[i].address_components[b]['short_name'].toUpperCase();
                        } else {
                            finalname += results[i].address_components[b]['short_name'];
                        }
                        if ((b + 1) < (results[i].address_components.length - 1)) {
                            finalname += ", ";
                        }
                    }
                    break;
                }
            }
            if (finalname == "") {
                finalname = actualcity.val();
            }
            sockscity.val(finalname + sockscity.val());
            sat_updatecitylist(maxcitiesobj, sockscity, citieslistobj, data.code);
            actualcity.val("");
        } else {
            citynotfoundmsg.show();
        }
    });
}

