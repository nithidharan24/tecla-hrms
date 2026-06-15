<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestCheckin;
use App\Models\EmployeeLocation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class TestCheckinController extends Controller
{
    public function index()
    {
        return view('test-checkin');
    }

    public function history()
    {
        // Check if employee is logged in
        if (!session('employee_id') || !session('first_name')) {
            return redirect()->route('login')->with('error', 'Please login to view your check-in history.');
        }

        $employeeName = session('first_name') . ' ' . session('last_name');
        
        // Get check-ins for the logged-in employee
        $checkins = TestCheckin::with('location')
            ->where('employee_name', $employeeName)
            ->orderBy('checkin_time', 'desc')
            ->paginate(10);
        
        // Update location names if needed
        foreach ($checkins as $checkin) {
            if ($checkin->location) {
                $checkin->location_name = $checkin->location->address ?: 'Location not available';
            } else {
                $checkin->location_name = 'Location not available';
            }
        }
        
        return view('test-checkin-history', compact('checkins', 'employeeName'));
    }

    public function showCheckins()
    {
        $checkins = TestCheckin::with('location')
            ->orderBy('checkin_time', 'desc')
            ->paginate(20); // Load only 20 records per page instead of all
        
        foreach ($checkins as $checkin) {
            if ($checkin->location && $checkin->location->address) {
                $checkin->location_name = $checkin->location->address;
            } else {
                $checkin->location_name = 'Location not available';
            }
        }
        
        return view('checkin-index', compact('checkins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'employee_image' => 'required|string', // base64 image
            'employee_name' => 'nullable|string|max:255'
        ]);

        $accuracy = $request->accuracy;
        if ($accuracy && $accuracy > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Location accuracy is too low (' . round($accuracy) . 'm). Please try again in an area with better GPS signal.',
                'accuracy' => $accuracy
            ], 400);
        }

        try {
            $checkin = new TestCheckin();
            if (session('first_name') && session('last_name')) {
                $checkin->employee_name = session('first_name') . ' ' . session('last_name');
            } else {
                $checkin->employee_name = $request->employee_name ?? 'Unknown Employee';
            }
            $checkin->device = $request->header('User-Agent');
            $checkin->checkin_time = now();

            // Handle image upload
            if ($request->employee_image) {
                $imageData = $request->employee_image;
                
                // Remove data:image/jpeg;base64, part if present
                if (strpos($imageData, 'data:image') === 0) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                }
                
                $imageData = base64_decode($imageData);
                $fileName = 'employee_checkin_' . time() . '_' . uniqid() . '.jpg';
                
                $uploadPath = public_path('uploads/checkins');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }
                
                // Store image directly in public directory
                file_put_contents($uploadPath . '/' . $fileName, $imageData);
                $checkin->employee_image = 'uploads/checkins/' . $fileName;
            }

            $checkin->save();

            $address = $this->getAddressFromCoordinates($request->latitude, $request->longitude);

            $location = new EmployeeLocation();
            $location->employee_id = $checkin->id;
            $location->latitude = $request->latitude;
            $location->longitude = $request->longitude;
            $location->accuracy = $request->accuracy ?? null;
            $location->address = $address;
            $location->save();

            return response()->json([
                'success' => true, 
                'message' => 'Check-in saved successfully with image and location',
                'checkin_id' => $checkin->id,
                'address' => $address,
                'accuracy' => $accuracy ? round($accuracy, 1) . 'm' : 'Unknown'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving check-in: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAddressFromCoordinates($latitude, $longitude)
    {
        // Try multiple geocoding services for maximum accuracy
        $services = [
            'nominatim_precise' => function($lat, $lon) { return $this->getNominatimPreciseAddress($lat, $lon); },
            'bigdatacloud' => function($lat, $lon) { return $this->getBigDataCloudAddress($lat, $lon); },
            'locationiq' => function($lat, $lon) { return $this->getLocationIQAddress($lat, $lon); },
            'nominatim_fallback' => function($lat, $lon) { return $this->getNominatimFallbackAddress($lat, $lon); }
        ];

        foreach ($services as $serviceName => $serviceFunction) {
            try {
                $address = $serviceFunction($latitude, $longitude);
                if ($address && $address !== "Coordinates: " . round($latitude, 6) . ", " . round($longitude, 6)) {
                    // Validate that the returned address is actually close to our coordinates
                    if ($this->validateAddressProximity($address, $latitude, $longitude)) {
                        return $address;
                    }
                }
            } catch (\Exception $e) {
                continue; // Try next service
            }
        }

        // If all services fail or return inaccurate results, return coordinates
        return "Coordinates: " . round($latitude, 6) . ", " . round($longitude, 6);
    }

    private function getNominatimPreciseAddress($latitude, $longitude)
    {
        $response = Http::timeout(30)->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat' => $latitude,
            'lon' => $longitude,
            'zoom' => 20, // Maximum zoom for building-level precision
            'addressdetails' => 1,
            'accept-language' => 'en',
            'extratags' => 1,
            'namedetails' => 1,
            'polygon_threshold' => 0.0001, // Very precise polygon matching
            'email' => 'your-app@domain.com' // Add your email for better service
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['address'])) {
                $address = $data['address'];
                $addressParts = [];
                
                // Prioritize most specific location data first
                if (isset($address['house_number']) && isset($address['road'])) {
                    $addressParts[] = $address['house_number'] . ' ' . $address['road'];
                } elseif (isset($address['road'])) {
                    $addressParts[] = $address['road'];
                } elseif (isset($address['pedestrian'])) {
                    $addressParts[] = $address['pedestrian'];
                } elseif (isset($address['footway'])) {
                    $addressParts[] = $address['footway'];
                }
                
                // Get the most precise locality available
                $locality = $address['building'] ?? 
                           $address['house_name'] ?? 
                           $address['neighbourhood'] ?? 
                           $address['suburb'] ?? 
                           $address['quarter'] ?? 
                           $address['residential'] ?? 
                           $address['hamlet'] ?? 
                           $address['village'] ?? 
                           null;
                
                if ($locality) {
                    $addressParts[] = $locality;
                }
                
                // Only add broader areas if we don't have specific locality
                if (empty($locality)) {
                    $broader = $address['town'] ?? 
                              $address['city'] ?? 
                              $address['municipality'] ?? 
                              null;
                    if ($broader) {
                        $addressParts[] = $broader;
                    }
                }
                
                if (isset($address['state'])) {
                    $addressParts[] = $address['state'];
                }
                
                if (isset($address['country'])) {
                    $addressParts[] = $address['country'];
                }
                
                if (!empty($addressParts)) {
                    return implode(', ', $addressParts);
                }
            }
        }
        
        return null;
    }

    private function getBigDataCloudAddress($latitude, $longitude)
    {
        $response = Http::timeout(25)->get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'localityLanguage' => 'en',
            'key' => 'free' // Use free tier with better accuracy
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $addressParts = [];
            
            // Prioritize most specific location
            if (!empty($data['localityInfo']['administrative'][0]['name'])) {
                $addressParts[] = $data['localityInfo']['administrative'][0]['name'];
            } elseif (!empty($data['locality'])) {
                $addressParts[] = $data['locality'];
            }
            
            if (!empty($data['city']) && !in_array($data['city'], $addressParts)) {
                $addressParts[] = $data['city'];
            }
            
            if (!empty($data['principalSubdivision'])) {
                $addressParts[] = $data['principalSubdivision'];
            }
            
            if (!empty($data['countryName'])) {
                $addressParts[] = $data['countryName'];
            }
            
            if (!empty($addressParts)) {
                return implode(', ', $addressParts);
            }
        }
        
        return null;
    }

    private function getLocationIQAddress($latitude, $longitude)
    {
        // LocationIQ often has better data for Indian locations
        $response = Http::timeout(25)->get('https://us1.locationiq.com/v1/reverse.php', [
            'key' => 'pk.0123456789abcdef', // Replace with your free LocationIQ key
            'lat' => $latitude,
            'lon' => $longitude,
            'format' => 'json',
            'zoom' => 18,
            'addressdetails' => 1,
            'accept-language' => 'en'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['address'])) {
                $address = $data['address'];
                $addressParts = [];
                
                if (isset($address['house_number']) && isset($address['road'])) {
                    $addressParts[] = $address['house_number'] . ' ' . $address['road'];
                } elseif (isset($address['road'])) {
                    $addressParts[] = $address['road'];
                }
                
                $locality = $address['neighbourhood'] ?? 
                           $address['suburb'] ?? 
                           $address['village'] ?? 
                           $address['hamlet'] ?? 
                           null;
                
                if ($locality) {
                    $addressParts[] = $locality;
                }
                
                if (isset($address['state'])) {
                    $addressParts[] = $address['state'];
                }
                
                if (isset($address['country'])) {
                    $addressParts[] = $address['country'];
                }
                
                if (!empty($addressParts)) {
                    return implode(', ', $addressParts);
                }
            }
        }
        
        return null;
    }

    private function getNominatimFallbackAddress($latitude, $longitude)
    {
        $response = Http::timeout(20)->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat' => $latitude,
            'lon' => $longitude,
            'zoom' => 16,
            'addressdetails' => 1,
            'accept-language' => 'en'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['address'])) {
                $address = $data['address'];
                $addressParts = [];
                
                $locality = $address['neighbourhood'] ?? 
                           $address['suburb'] ?? 
                           $address['village'] ?? 
                           $address['hamlet'] ?? 
                           $address['town'] ?? 
                           $address['city'] ?? 
                           null;
                
                if ($locality) {
                    $addressParts[] = $locality;
                }
                
                if (isset($address['state'])) {
                    $addressParts[] = $address['state'];
                }
                
                if (isset($address['country'])) {
                    $addressParts[] = $address['country'];
                }
                
                if (!empty($addressParts)) {
                    return implode(', ', $addressParts);
                }
            }
        }
        
        return null;
    }

    private function validateAddressProximity($address, $latitude, $longitude)
    {
        // Skip validation for coordinate-only addresses
        if (strpos($address, 'Coordinates:') === 0) {
            return true;
        }

        try {
            // Forward geocode the address to get its coordinates
            $response = Http::timeout(15)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    $addressLat = floatval($data[0]['lat']);
                    $addressLon = floatval($data[0]['lon']);
                    
                    // Calculate distance between original coordinates and address coordinates
                    $distance = $this->calculateDistance($latitude, $longitude, $addressLat, $addressLon);
                    
                    // If the address is more than 5km away from the coordinates, it's likely wrong
                    return $distance <= 5.0; // 5km tolerance
                }
            }
        } catch (\Exception $e) {
            // If validation fails, assume address is valid to avoid blocking legitimate results
            return true;
        }
        
        return true;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

}
