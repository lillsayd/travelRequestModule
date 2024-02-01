if ($('#Travel_request_chart').length > 0) {
    let options = {
        scales: {
            y: {
                beginAtZero: true,
                min: 100,
            }
        }
    };

    generateChart('bar', 'Travel_request_chart', 'stats');
}

