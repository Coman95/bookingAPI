<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Trip as TripResource;
use App\Models\Trip;
use App\Models\User;


class TripController extends BaseController
{

    /**
     * Get all Trips
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $trips = Trip::all();

        return $this->handleResponse(TripResource::collection($trips), 'Trips have been retrieved!');
    }

    /**
     * Get Trip by title
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        if (is_null($slug)){
            return $this->handleError('Slug not found!');
        }
        // Property [id] does not exist on this collection instance
        $trip = Trip::where('slug', 'like', $slug)->get()[0];
        
        if (is_null($trip)) {
            return $this->handleError('Trip not found!');
        }
        return $this->handleResponse(new TripResource($trip), 'Trip retrieved!');
    }

     /**
     * Create new Trip
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'slug' => 'required',
            'title' => 'required'
        ]);
        
        if($validator->fails()){
            return $this->handleError($validator->errors());       
        }
        $trip = Trip::create($input);
        return $this->handleResponse(new TripResource($trip), 'Trip created!');
    }
    
    /**
     * Update Trip
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $trip = Trip::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'slug' => 'required',
            'title' => 'required'
        ]);

        if($validator->fails() || $trip == null){
            return $this->handleError($validator->errors(), 'No trip with id: ' . $id);       
        }

        $trip->slug = $input['slug'];
        $trip->title = $input['title'];
        $trip->description = $input['description'];
        $trip->start_date = $input['start_date'];
        $trip->end_date = $input['end_date'];
        $trip->price = $input['price'];
        $trip->location = $input['location'];
        $trip->save();
        
        return $this->handleResponse(new TripResource($trip), 'Trip successfully updated!');
    }
   
    /**
     * Delete Trip 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Trip $trip)
    {
        $trip->delete();
        return $this->handleResponse([], 'Trip deleted!');
    }

    /**
     * Search Trip by title 
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($title)
    {
        $trips = Trip::where('title', 'like',  '%' . $title . '%')->get();

        return $this->handleResponse($trips, 'Searched for: ' . $title);
    }

    /**
     * Order Trips by param, Add '-' before the param to reverse ordering.
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderby($param)
    { 
        if( $param[0] == '-'){
            $order = 'desc';
            $param = str_replace('-', '', $param);
        } else {
            $order = 'asc';
        }

        try {
            $trips = Trip::orderby($param, $order)->get();
        } catch (\Throwable $th) {
            return $this->handleError('Column does not exist!');
        }

        if (is_null($trips)) {
            return $this->handleError('No Trips found!');
        }

        return $this->handleResponse($trips, 'Ordered by: ' . $param);
    }

    /**
     * Reserve booking for the authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function reserve($trip)
    {
        $user = Auth::user();
        $trip = Trip::find($trip);

        if (is_null($trip)) {
            return $this->handleError('Trip not found!');
        }

        if ($this->tripBooked($trip, $user)){
            return $this->handleError('Booking already exists!');
        }

        // Update Pivot Table for many to many relationship
        $user->trips()->attach($trip);

        return $this->handleResponse($trip, 'Booking was successfull!');
    }

    /**
     * Check if User has already booked a certain trip
     * @return Bool
     */
    public function tripBooked($trip, $user)
    {
        return $trip->users()->where('user_id', $user->id)->exists();
    }

}