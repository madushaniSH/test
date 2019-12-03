$("#slideshow > div:gt(0)").hide();

setInterval(function() {
    $('#slideshow > div:first')
        .fadeOut(500)
        .next()
        .fadeIn(500)
        .end()
        .appendTo('#slideshow');
},  10000);