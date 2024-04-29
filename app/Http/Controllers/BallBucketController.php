<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bucket;
use App\Models\Ball;
use App\Models\BucketBall;
class BallBucketController extends Controller
{
    //Get all data ball and bucket
    public function index()
    {
        $buckets = Bucket::orderBy('capacity', 'desc')->get();
        
        $ballName = Ball::get();

        //get all bucket ball dat
        $bucketball = BucketBall::select('bucket_id','ball_id','quantity')->groupBy('bucket_id','ball_id','quantity')->get();
       
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
    public function distributeBalls(Request $request)
    {
      
        $buckets = Bucket::orderBy('capacity', 'desc')->get();
        $balls = Ball::all();
        $totalBallVolume = 0;
        $totalSize = 0;
        
        //get ball size and total volume
        foreach($balls as $ball) {
            $totalBallVolume += $ball->size * $request->input('quantity_'. $ball->id);
            $totalSize += $ball->size;
           
        }

        $bucketSpace = $buckets->sum('capacity');

        //get minimum number of bucket
        $minBucketsRequired = ceil($totalBallVolume / max($buckets->pluck('capacity')->all()));

        if ($bucketSpace < $totalBallVolume) {
         
            return redirect()->back()->with('error','Not enough bucket space to fit all balls required minimum number of bucket '.$minBucketsRequired);
           
        }

        //get number of ball count according ball color
        $results = [];
        foreach ($balls as $ball) {
            foreach ($buckets as $bucket) {
                $fitCount = min($request->input('quantity_'. $ball->id), intdiv($bucket->capacity, $ball->size));
                if ($fitCount > 0) {
                    $results[$bucket->name][$ball->color] = $fitCount;
                    
                    // Save each distribution BucketBall
                    $alreadyAddedBall = BucketBall::where('bucket_id',$bucket->id)->where('ball_id',$ball->id)->first();
                    if($alreadyAddedBall){
                       
                        $alreadyAddedBall->bucket_id =$bucket->id;
                        $alreadyAddedBall->ball_id   =$ball->id;
                        $alreadyAddedBall->quantity  =$fitCount;
                        $alreadyAddedBall->save();
                    }else{
                        $bucketBall = new BucketBall();
                        $bucketBall->bucket_id =$bucket->id;
                        $bucketBall->ball_id   =$ball->id;
                        $bucketBall->quantity  =$fitCount;
                        $bucketBall->save();
                    }
                }
            }
        }

      return redirect()->back()->with('message','Ball added in bucket successfully!');
    }  
     
}
