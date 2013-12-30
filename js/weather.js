$(document).ready(function() {
    getWeatherData(null);
    
    $('#searchButton').on('click', function() {getWeatherData($('#zipInput').val())});
    $('#zipInput').on('keydown', function(e) {if (e.keyCode == 13) {getWeatherData($('#zipInput').val())}});
});

/* if the zip code supplied finds a DB match, display the next five days of weather */
function drawForecast(json) {
    $('#message').removeClass('loading').addClass('response')
                 .text('Forecast for ' + json.location + ', ' + json.zipCode);
    
    $.each(json.forecast, function() {
        $('<div>').addClass('forecast')
                  .append([$('<div>').addClass('day').text(this.day),
                           $('<div>').addClass('high').text(this.high),
                           $('<div>').addClass('low').text(this.low),
                           $('<div>').addClass('conditions').text(this.conditions),
                           $('<div>').addClass('precipitation').text(this.precip + '% chance of precipitation'),
                           $('<div>').addClass('expand').text('More')
                                     .on('click', function() {
                                         var text = $(this).text();
                                         var details = $(this).parent().children('.conditions, .precipitation');
                                         if (text == 'More') {
                                            details.show();
                                            $(this).text('Less');
                                         } else {
                                            details.hide();
                                            $(this).text('More');
                                         }
                                     })])
                  .appendTo('#weatherResults');
    });
}

/* if the page is loading or the entered zip code cannot be found,
 * show a list of available locations to choose from */
function drawLocations(json, zipCode) {
    var messageNoData = 'Sorry, no weather data was found. Please select one of the following locations:';
    var messageNoZip = 'Current weather data available for these locations:';
    $('#message').removeClass('loading').addClass('response')
                 .text((zipCode) ? messageNoData : messageNoZip);

    $.each(json, function() {
        $('<div>').addClass('location')
                  .append([$('<div>').text(this.location), $('<div>').text(this.zipCode)])
                  .on('click', {'zipCode': this.zipCode}, function(e) {getWeatherData(e.data.zipCode);})
                  .appendTo('#weatherResults');
    });
}

function getWeatherData(zipCode) {
    $('#message').removeClass('response').addClass('loading');
    $('#message').text('Loading...');
    $('#weatherResults').empty();
    
    $.ajax({url: 'php/weather.php',
            type: "POST",
            dataType: "JSON",
            async: false,
            data: {zipCode: zipCode}
    }).done(function(responseJSON) {
        $('#loading').hide();
        (responseJSON.length) ? drawLocations(responseJSON, zipCode) : drawForecast(responseJSON);
    });
}
