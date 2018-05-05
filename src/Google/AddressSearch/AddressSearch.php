<?php
namespace Schrammel\Google\AddressSearch;

use Dotenv\Dotenv;
use Exception;
use GuzzleHttp\Client;

class AddressSearch
{
    private $results;
    
    
    public function __construct()
    {
        if (is_file(__DIR__ . '/.env')) {
            $dotenv = new Dotenv(__DIR__);
            $dotenv->load();
        }
    }
    
    /**
     * Fetch location data for given address
     *
     * @param string $address
     */
    public function searchAddress(string $address): void
    {
        $informationJson = $this->getAddress($address);
        $informationArray = json_decode($informationJson, true);
        
        $this->results = $informationArray['results'][0];
    }
    
    public function getFormattedAddress()
    {
        return $this->results['formatted_address'];
    }
    
    public function getLocationCoordinates()
    {
        return $this->results['geometry']['location'];
    }
    
    public function getLocationLng()
    {
        $location = $this->getLocationCoordinates();
        
        return $location['lng'];
    }
    
    public function getLocationLat()
    {
        $location = $this->getLocationCoordinates();
        
        return $location['lat'];
    }
    
    public function getViewport()
    {
        return $this->results['geometry']['viewport'];
    }
    
    public function getViewportNorthEast()
    {
        $viewport = $this->getViewport();
        
        return $viewport['northeast'];
    }
    
    public function getNorthEastLng()
    {
        $northEastCorner = $this->getViewportNorthEast();
        
        return $northEastCorner['lng'];
    }
    
    public function getNorthEastLat()
    {
        $northEastCorner = $this->getViewportNorthEast();
        
        return $northEastCorner['lat'];
    }
    
    public function getViewportSouthWest()
    {
        $viewport = $this->getViewport();
        
        return $viewport['southwest'];
    }
    
    public function getSouthWestLng()
    {
        $southWestCorner = $this->getViewportSouthWest();
        
        return $southWestCorner['lng'];
    }
    
    public function getSouthWestLat()
    {
        $southWestCorner = $this->getViewportSouthWest();
        
        return $southWestCorner['lat'];
    }
    
    /**
     * Replace blanks with "+" and URL-encode the address to meet Google Maps requirement
     * 
     * @param string $address
     * @return string
     */
    private function prepareAddress(string $address)
    {
        $address = str_replace(' ', '+', $address);
        $address = urlencode($address);
        
        return $address;
    }
    
    /**
     * Fetch the address information from Google
     * 
     * @param string $address
     * @return string
     */
    private function getAddress(string $address)
    {
        $address = $this->prepareAddress($address);
    
        try {
            $apiKey = getenv('GM_API_KEY');
            $uri = 'https://maps.google.com/maps/api/geocode/json?sensor=false&address=' . $address . '&key=' . $apiKey;
            $client = new Client();
            $response = $client->get($uri);
            
            if ($response->getStatusCode() != 200) {
                throw new Exception(
                    'An error occured while fetching the address information. 
                    Google Maps API responded with status code ' . $response->getStatusCode()
                );
            }
    
            return (string)$response->getBody();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}