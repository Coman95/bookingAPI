<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Trip as TripResource;
use App\Models\Trip;
use App\Models\User;
use DateTime;

class TripController extends BaseController
{

    /**
     * Get all Trips
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $trips = Trip::all();

        return $this->successResponse(TripResource::collection($trips), 'Trips have been retrieved!');
    }

    /**
     * Get Trip by title
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        if (is_null($slug)){
            return $this->errorResponse('Slug not found!');
        }

        $trip = Trip::where('slug', 'LIKE', "%{$slug}%" )->get()->first();
        
        if (is_null($trip)) {
            return $this->errorResponse('Trip not found!');
        }
        return $this->successResponse(new TripResource($trip), 'Trip retrieved!');
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
            return $this->errorResponse($validator->errors());       
        }
        $trip = Trip::create($input);
        return $this->successResponse(new TripResource($trip), 'Trip created!');
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
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'price' => 'required',
            'location' => 'required',
        ]);

        if($validator->fails() || $trip == null){
            return $this->errorResponse($validator->errors(), 'No trip with id: ' . $id);       
        }

        $trip->slug = $input['slug'];
        $trip->title = $input['title'];
        $trip->description = $input['description'];
        $trip->start_date = $input['start_date'];
        $trip->end_date = $input['end_date'];
        $trip->price = $input['price'];
        $trip->location = $input['location'];
        $trip->save();
        
        return $this->successResponse(new TripResource($trip), 'Trip successfully updated!');
    }
   
    /**
     * Delete Trip 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Trip $trip)
    {
        $trip->delete();
        return $this->successResponse([], 'Trip deleted!');
    }

    /**
     * Get trips according to search and order_by terms
     * @return \Illuminate\Http\JsonResponse
     */
    public function terms(Request $request)
    {
        $search = $request->input('q');
        $order_by = $request->input('order_by');

        if ( is_null($search) )
        {
            return $this->errorResponse([], 'No search term found'); 
        }

        if( is_null($order_by))
        {
            // TODO:: query will break if column does not exist
            $order_by = 'id';
        }

        if( $order_by[0] == '-'){
            $order = 'desc';
            $order_by = str_replace('-', '', $order_by);
        } else {
            $order = 'asc';
        }
        
        $trips = Trip::query()
                    ->where('title', 'LIKE', "%{$search}%")
                    ->orderby($order_by, $order)
                    ->get();

        return $this->successResponse($trips, 'Search for: ' . $search . ' and ordered by ' . $order_by);
      
    }

    /**
     * Reserve booking for the authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function reserve($trip)
    {
        $user = Auth::user();
        $trip = Trip::find($trip);
        $currentDate = new DateTime();

        if (is_null($trip)) {
            return $this->errorResponse('Trip not found!', $code = 400);
        }

        $start = new DateTime($trip->start_date);
        if ( $start < $currentDate ){
            return $this->errorResponse('Trip has already started!', $code = 400);
        }     

        if ($this->tripBooked($trip, $user)){
            return $this->errorResponse('Booking already exists!', $code = 400);
        }

        // Update Pivot Table for many to many relationship
        $user->trips()->attach($trip);

        return $this->successResponse( $trip, 'Booking was successfull!');
    }

    /**
     * Get trips between 2 prices
     * @return \Illuminate\Http\JsonResponse
     */
    public function priceRange($start_price, $end_price = PHP_INT_MAX)
    {

        $trips = Trip::query()
                    ->where('price', '>', $start_price)
                    ->where('price', '<', $end_price)
                    ->get();

        if (is_null($trips->first())) {
            return $this->errorResponse('No Trips found!', $code = 400);
        }
      
        return $this->successResponse( $trips, 'Trips starting from: ' . $start_price);
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