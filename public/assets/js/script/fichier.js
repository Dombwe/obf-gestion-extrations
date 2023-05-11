const fileForm = document.querySelector('#fileForm');
const historiquesListTable = document.querySelector('#historiquesListTable');
const errorMessage = document.querySelector('#errorMessage');
const successMessage = document.querySelector('#successMessage');



fileForm.addEventListener('submit', function (e) {
    e.preventDefault();
    let xhr = $.ajax({
        xhr: function () {
            let xhr = new XMLHttpRequest();
            let startTime = new Date().getTime();
            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    let percentComplete = ((e.loaded / e.total) * 100);

                    let mbTotal = Math.floor(e.total / (1024 * 1024));
                    let mbLoaded = Math.floor(e.loaded / (1024 * 1024));

                    let time = (new Date().getTime() - startTime) / 1000;
                    let bps = e.loaded / time;
                    let Mbps = Math.floor(bps / (1024 * 1024));

                    let remTime = (e.total - e.loaded);
                    let second = Math.floor(remTime % 60);
                    let minutes = Math.floor(remTime / 60);

                    $('#dataTransferred').html(`${mbLoaded} / ${mbTotal} MB`);
                    $('#Mbps').html(`${Mbps} Mbps`);
                    $('#timeLeft').html(`${minutes}:${second}s`);
                    $('#percent').html(Math.floor(percentComplete) + '%');
                    $('.progress-bar').width(percentComplete + '%');
                }
            }, false);
            return xhr;
        },
        type: 'POST',
        url: this.action,
        data: new FormData(e.target),
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $(".component").show();
            $('#percent').html('0%');
            $('.progress-bar').width('0%');
        },
        error: function (e) {
            $(".component").hide();
        },
        success: function (response) {
            $(".component").hide();
            handleResponse(response);
        }
    });

})



const handleResponse = function (response) {
    removeErrors();
    successMessage.innerHTML = '';
    errorMessage.innerHTML = '';
    switch (response.code) {
        case 200:
            fileForm.reset();
            historiquesListTable.innerHTML += response.response;
            successMessage.innerHTML = 'Extraction reussie!';
            $('#percent').html('Extraction reussie!');
        break;
        case 400:
            errorMessage.innerHTML = response.response;
            $('#percent').html('Erreur!');
        break;

        default: break;
    }
}


const handleErrors = function (errors) {
    if (errors.length === 0) return;

    for (const key in errors) {
        let element = document.querySelector(`#fichier_${key}`);
        element.classList.add('is-invalid');

        let div = document.createElement('div');
        div.classList.add('invalid-feedback', 'd-block');
        div.innerText = errors[key];

        element.after(div);
    }
}


const removeErrors = function () {
    const invalidFeedbackElements = document.querySelectorAll('.invalid-feedback');
    const isInvalidElements = document.querySelectorAll('.is-invalid');

    invalidFeedbackElements.forEach(invalidFeedback => invalidFeedback.remove());
    isInvalidElements.forEach(isInvalid => isInvalid.remove());
}
























































































// fileForm.addEventListener('submit', function (e) {
//     e.preventDefault();

//     fetch(this.action, {
//         body: new FormData(e.target),
//         method: 'POST'
//     })
//     .then(response => response.json())
//     .then(json => {
//         // console.log(json);
//         handleResponse(json);
//     });

// })

// const handleResponse = function (response) {
//     removeErrors();
//     successMessage.innerHTML = '';
//     errorMessage.innerHTML = '';
//     switch (response.code) {
//         case 200:
//             historiquesListe.innerHTML += response.response;
//             successMessage.innerHTML = 'Chargement reussi!';
//             break;
//         case 400:
//             errorMessage.innerHTML = response.response;
//             // handleErrors(response.response);
//             break;
    
//         default:
//             break;
//     }
// }


// const handleErrors = function (errors) {
//     if (errors.length === 0) return;

//     for (const key in errors) {
//         let element = document.querySelector(`#fichier_${key}`);
//         element.classList.add('is-invalid');

//         let div = document.createElement('div');
//         div.classList.add('invalid-feedback', 'd-block');
//         div.innerText = errors[key];

//         element.after(div);
//     }
// }


// const removeErrors = function () {
//     const invalidFeedbackElements = document.querySelectorAll('.invalid-feedback');
//     const isInvalidElements = document.querySelectorAll('.is-invalid');

//     invalidFeedbackElements.forEach(invalidFeedback => invalidFeedback.remove());
//     isInvalidElements.forEach(isInvalid => isInvalid.remove());
// }