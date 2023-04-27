$(document).ready(function() {

    // // on Update button click
    // $('.addCommentBtn').on('click', async function (event) {
    //     event.preventDefault();
    //     console.log('addReviserCommentBtn');
    //     // openAddCommentBox(this);
    // });

    // on Delete button click
    $('.deleteBtn').on('click', async function (event) {
        event.preventDefault();
        openDeleteBox(this);
    });
        
});


function openAddReviserCommentBox(event) {
    const url = ($(event).attr('href'));
    Swal.fire({
        title: 'Mise a jour',
        text: "Voulez-vous modifier cet enregistrement ?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Confirmer'
        }).then((result) => {
        if (result.isConfirmed) {
            axios.get(url).then(function (response) {
                Swal.fire({
                    title: 'Modifié!',
                    text: 'L\'enregistrement a été modifié',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                })
            });
        }
    })
}


function openDeleteBox(event) {
    const url = ($(event).attr('href'));
    Swal.fire({
        title: 'Suppression',
        text: "Voulez-vous supprimer cet enregistrement ?",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Confirmer'
        }).then((result) => {
        if (result.isConfirmed) {
            axios.get(url).then(function (response) {
                location.reload(true);
                Swal.fire({
                    title: 'Supprimé!',
                    text: 'L\'enregistrement a été supprimé',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                })
            });
        }
    })
}