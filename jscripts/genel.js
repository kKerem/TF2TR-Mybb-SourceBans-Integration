$(document).ready(function() {
    $(".gelismisArama form").hide();
    $(".gelismisArama h1").click(function() {
        $(".gelismisArama").toggleClass('acilsusamacil');
        $(".gelismisArama form").toggle(200);
    });

    $(".tborder tr:not(.acilirpanel)").click(function() {
        $(this).toggleClass("detaylibilgi");
        $(this).next(".acilirpanel").find("div").slideToggle("fast");
    });
    
    $(".aramaform-label").click(function() {
        $(this).find('input[type=radio]').prop("checked", "true");
    })
});
