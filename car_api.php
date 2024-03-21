<?php
require 'vendor/autoload.php';


class CarApiFunctions {
  private  $baseUrl = "https://carapi.app/api/";

    function getYearsByMake($make) {
        $url = "https://carapi.app/api/years?make=" . urlencode($make);
        
        $context = stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => "Accept: application/json\r\n"
            ]
        ]);
        $response = file_get_contents($url, false, $context);
        if ($response !== false) {
            return json_decode($response, true);
        } else {
            echo "Error getting data";
        }
    }

    function getMakes($page = null, $limit = null, $sort = null, $direction = null, $make, $year) {
        $url = $this->baseUrl . "makes?";
        if ($make!=null || $year!=null){
            $params = array_filter([
                'page' => $page,
                'limit' => $limit,
                'sort' => $sort,
                'direction' => $direction,
                'make' => $make,
                'year' => $year,
            ], function($value) {
                return !is_null($value);
            });
            $queryString = http_build_query($params);
            $url .= $queryString;
    
            $context = stream_context_create([
                "http" => [
                    "method" => "GET",
                    "header" => "Accept: application/json\r\n"
                ]
            ]);
            $response = file_get_contents($url, false, $context);
            if ($response !== false) {
                return json_decode($response, true);
            } else {
                return ["error" => "Error getting data from the API"];
            }
        }

        else{
            return ["error" => "make or year must not be empty"];
        }
        
    }

   function getRecall($make, $model, $year) {
    $url = "https://api.nhtsa.gov/recalls/recallsByVehicle?make=" . urlencode($make) . "&model=" . urlencode($model) . "&modelYear=" . urlencode($year);
    
    $context = stream_context_create([
        "http" => [
            "method" => "GET",
            "header" => "Accept: application/json\r\n"
        ]
    ]);
    $response = file_get_contents($url, false, $context);
    if ($response !== false) {
        return json_decode($response, true);
    } else {
        return ["error" => "Error getting recall data from the API"];
    }
}

}

