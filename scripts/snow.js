$(document).ready(function(){
    const d = new Date();
    const n = d.getMonth();
    if (n === 11) {
        //Start the snow default options you can also make it snow in certain elements, etc.
        $.get('verify.php', function(data) {
            if (data === "false") {
                $(document).snowfall({image: "scripts/flake.png", minSize: 10, maxSize: 15, maxSpeed: 2, flakeCount: 10});
            } else {
                $(document).snowfall({image: "scripts/heshan.png", minSize: 45, maxSize: 60, maxSpeed: 2, flakeCount: 20});
            }
            $(document).snowfall();
        });
        //$(document).snowfall('clear');
    }
});
