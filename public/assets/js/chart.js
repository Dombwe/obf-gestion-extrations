const ctx1 = document.getElementById('myChart1');
const ctx2 = document.getElementById('myChart2');


initStagiaireChart();
initDemandeChart();

function initStagiaireChart() {
    const url_labels = '/chartjs/stagiaires/labels';
    const url_ldata = '/chartjs/stagiaires/data';
    const labels = axios.get(url_labels).then(function (labelsResponse) {
        const data = axios.get(url_ldata).then(function (dataResponse) {
            return new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labelsResponse.data,
                    datasets: [{
                        label: '# Total Stagiaires',
                        data: dataResponse.data,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }).catch(function (error) {
            //console.log(error);
        });
    }).catch(function (error) {
        //console.log(error);
    });
}


function initDemandeChart() {
    const url_labels = '/chartjs/demandes/labels';
    const url_ldata = '/chartjs/demandes/data';
    const labels = axios.get(url_labels).then(function (labelsResponse) {
        const data = axios.get(url_ldata).then(function (dataResponse) {
            return new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: labelsResponse.data,
                    datasets: [{
                        label: '# Total Demandes',
                        data: dataResponse.data,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        }).catch(function (error) {
            //console.log(error);
        });
    }).catch(function (error) {
        //console.log(error);
    });
}