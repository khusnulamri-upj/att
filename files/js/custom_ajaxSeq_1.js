function fireRequest(url,interface,ajaximg) {
    return $.ajax({
            type: 'POST',
            url: url,
            beforeSend: function () {
                if ((typeof interface.before !== "undefined") && (typeof interface.divid !== "undefined")) {
                    $('#'.concat(interface.divid)).html(interface.before.concat(ajaximg));
                } else if (typeof interface.divid !== "undefined") {
                    $('#'.concat(interface.divid)).html(ajaximg);
                }
            },
            error: function () {
                if ((typeof interface.before !== "undefined") && (typeof interface.divid !== "undefined")) {
                    $('#'.concat(interface.divid)).html('Error '.concat(interface.before));
                } else if (typeof interface.divid !== "undefined") {
                    $('#'.concat(interface.divid)).html('Error '.concat(url));
                }
            },
            success: function () {
                if ((typeof interface.after !== "undefined") && (typeof interface.divid !== "undefined")) {
                    $('#'.concat(interface.divid)).html(interface.after);
                } else if (typeof interface.divid !== "undefined") {
                    $('#'.concat(interface.divid)).html('Success '.concat(url));
                }
            }
        });
}

function sequenceRequest(urls, scrn, ajaximg) {
    initurl = urls[0];
    initscrn = scrn[0];
    urls.splice(0,1);
    scrn.splice(0,1);
    startingpoint = fireRequest(initurl,initscrn,ajaximg);
    $.each(urls, function(ix, urlx) {
        startingpoint = startingpoint.pipe(function(response, status, jqXhr) {
            console.log('Sequence ' + ix + ' is ' + status);
            return fireRequest(urlx, scrn[ix], ajaximg);
        },
                function(jqXhr, status, httpResponse) {
                    // This will get called once
                    console.log('Sequence ' + ix + ' is ' + status + ' with response ' + httpResponse);
                });
    });
}