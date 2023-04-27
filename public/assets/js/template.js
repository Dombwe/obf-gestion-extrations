
$(document).ready(function() {

    const baseUrl = "https://127.0.0.1:8000";

    // on utilisateurs button click
    // $('.utilisateursBtn').on('click', async function (event) {
    //     event.preventDefault();
    //     openLayout(this, baseUrl);
    // });

    // $('.directionsBtn').on('click', async function (event) {
    //     event.preventDefault();
    //     openLayout(this,baseUrl);
    // });

});


function openLayout(event, baseUrl) {
    const url = baseUrl+($(event).attr('href'));
    console.log(url);
    window.open(url).focus() ;
}
