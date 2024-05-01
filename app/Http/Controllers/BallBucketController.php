<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bucket;
use App\Models\Ball;
use App\Models\BucketBall;
use DB;
class BallBucketController extends Controller
{
    //Get all data ball and bucket
    public function index()
    {
        $buckets = Bucket::orderBy('capacity', 'desc')->get();
        
        $ballName = Ball::get();

        //get all bucket ball dat
        $bucketball = BucketBall::select('bucket_id','ball_id','quantity',DB::raw('sum(quantity) as ball_count'))->groupBy('bucket_id','ball_id','quantity')->get();
       
        return view('ballbucket.index', compact('buckets', 'ballName','bucketball'));
    }

    //store bucket name
    public function addBucket(Request $request)
    {
        $request->validate(['name' => 'required', 'capacity' => 'required|numeric']);
        Bucket::create($request->all());
        return redirect()->back()->with('message','Bucket added successfully!.');
    }

     //store ball name
    public function addBall(Request $request)
    {
        $request->validate(['color' => 'required', 'size' => 'required|numeric']);
        Ball::create($request->all());
        return redirect()->back()->with('message','Ball added successfully!');
    }


    //store number of ball in bucket
    public function distributeBalls(Request $request) {

        // Retrieve all buckets and balls
        $buckets = Bucket::orderBy('capacity', 'desc')->get();
        $balls = Ball::all();
        $totalBallVolume = 0;
    
        // Calculate the total volume of balls requested and update each ball's requested quantity
        foreach ($balls as $ball) {
            $ball->requestedQuantity = $request->input('quantity_' . $ball->id, 0);
            $totalBallVolume += $ball->size * $ball->requestedQuantity;
        }
    
        $bucketSpace = $buckets->sum('capacity');
    
        $results = [];
        $undistributed = [];
    
        // Distribute balls into buckets
        foreach ($balls as $ball) {
            foreach ($buckets as $bucket) {
                if ($ball->requestedQuantity > 0 && $bucket->capacity >= $ball->size) {
                    $fitCount = min($ball->requestedQuantity, intdiv($bucket->capacity, $ball->size));
                    $bucket->capacity -= $fitCount * $ball->size;
                    $bucket->save();
                    $ball->requestedQuantity -= $fitCount;
    
                    // distribution data
                    $results[$bucket->id][$ball->id] = ($results[$bucket->id][$ball->id] ?? 0) + $fitCount;
    
                    // Save data
                    $bucketBall = new BucketBall();
                    $bucketBall->bucket_id = $bucket->id;
                    $bucketBall->ball_id   = $ball->id;
                    $bucketBall->quantity  = $fitCount;
                    $bucketBall->save();
    
                    // If the bucket is now full, move to the next bucket
                    if ($bucket->capacity < $ball->size) {
                        break;
                    }
                }
            }
    
            // Check if there are not distributed balls
            if ($ball->requestedQuantity > 0) {
                $notdistributed[$ball->id] = ($notdistributed[$ball->id] ?? 0) + $ball->requestedQuantity;
            }
        }
    
        // Check if there were not distributed balls and handle the response
        if (!empty($notdistributed)) {
            return redirect()->back()->with('error', "Not all balls could be distributed due to insufficient bucket capacity. ".$bucket->capacity);
        }
    
        return redirect()->back()->with('message', 'All balls saved successfully!');
    }
    
}
