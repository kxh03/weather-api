$(document).ready(function () {
    $('#weatherForm').on('submit', function (e) {
        e.preventDefault();

        const form = this;

        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            return;
        }

        const formData = $(form).serialize();

        $('#weatherResult').html(`<div class="text-center text-muted">Po kërkoj motin...</div>`);

        $.ajax({
            url: 'api/get_weather.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#weatherResult').html(`
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Weather in (${data.latitude}, ${data.longitude})</h5>
                                <p class="card-text">Current Temp: ${data.current_temp}°C</p>
                                <p class="card-text">Feels like: ${data.feels_like}°C</p>
                                <p class="card-text">Description: ${data.weather_description}</p>
                            </div>
                        </div>
                    `);
                } else {
                    $('#weatherResult').html(`<div class="alert alert-warning">${data.message}</div>`);
                }
            },
            error: function (error) {
                $('#weatherResult').html(`<div class="alert alert-danger">Gabim në server: ${error}</div>`);
            }
        });
    });
});
