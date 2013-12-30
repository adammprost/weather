<?php
/* Simulates a weather database and outputs weather data
   in JSON format.
   
   Author: Adam Prost
*/

class Forecast {
    function __construct($zipCode, $location, $weatherData) {
        $this->zipCode = $zipCode;
        $this->location = $location;
        $this->forecast = array();
   
        $weekDay = date('w');
        
        //if we reach the end of the week's data, start over at the beginning
        $loopRun = ($weekDay > 2) ? $weekDay - 2 : 0;
        $loopArray = array_merge(array_slice($weatherData, $weekDay, 5),
                                 array_slice($weatherData, 0, $loopRun));
        foreach ($loopArray as $pipeString) {
            $this->addDay(explode('|', $pipeString));
        }
    }
    function addDay($explodeArray) {
        $this->forecast[] = array('day' => $explodeArray[0],
                                  'high' => $explodeArray[1],
                                  'low' => $explodeArray[2],
                                  'precip' => $explodeArray[3],
                                  'conditions' => $explodeArray[4]);
    }
}

class WeatherDB {
    //Weather data for the week - for demo purposes this only covers two locations
    function __construct() {
        $this->data = array("64501" => array("location" => "Saint Joseph",
                                             "weather" => array("Sunday|50|23|80|Snow",
                                                                "Monday|55|28|0|Sunny",
                                                                "Tuesday|58|28|10|Partly Cloudy",
                                                                "Wednesday|62|32|60|Rain",
                                                                "Thursday|65|40|5|Sunny",
                                                                "Friday|64|34|5|Sunny",
                                                                "Saturday|62|30|20|Partly Cloudy")),
                            "65807" => array("location" => "Springfield",
                                             "weather" => array("Sunday|55|25|90|Snow",
                                                                "Monday|62|32|10|Mostly Sunny",
                                                                "Tuesday|60|30|0|Partly Cloudy",
                                                                "Wednesday|66|34|50|Rain",
                                                                "Thursday|70|40|0|Sunny",
                                                                "Friday|68|38|0|Mostly Sunny",
                                                                "Saturday|66|32|0|Mostly Sunny")));
    }
    function retrieveLocations() {
        $locations = array();
        foreach ($this->data as $zip => $data) {
            $locations[] = array('zipCode' => $zip,
                                 'location' => $data['location']);
        }
        return $locations;
    }
    function retrieveWeather($zipCode) {
        $this->match = False;
        if (key_exists($zipCode, $this->data)) {
            $this->location = $this->data[$zipCode]['location'];
            $this->weather = $this->data[$zipCode]['weather'];
            $this->match = True;
        }
    }
}

$zipCode = isset($_POST['zipCode']) ? mysql_real_escape_string($_POST['zipCode']) : '';

$weatherDb = new WeatherDB();
$weatherDb->retrieveWeather($zipCode);

if ($weatherDb->match) {
    echo json_encode(new Forecast($zipCode, $weatherDb->location, $weatherDb->weather));
} else {
    echo json_encode($weatherDb->retrieveLocations());
}
?>
