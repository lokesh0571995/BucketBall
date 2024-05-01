<!DOCTYPE html>
<html>
<head>
    <title>Ball and Bucket Management</title>
</head>

<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/c8ee3dd930.js"></script>

<div class="container">
      <div class="card">
        <div class="title">
     
      @if(session()->has('message'))
        <div class="alert alert-success alert-block " style="color:green">
            {{ session()->get('message') }}
        </div>
    @endif
    
    <h1>Add Bucket</h1>
    <br>
    <form method="POST" action="/add-bucket">
        @csrf
        <input type="text" name="name" placeholder="Bucket Name" required>
        <input type="number" name="capacity" placeholder="Bucket volume in inches" required>
        <button type="submit">Add Bucket</button>
    </form>

    <h1>Add Ball</h1>
    <br>
    <form method="POST" action="/add-ball">
        @csrf
        <input type="text" name="color" placeholder="Ball Color" required>
        <input type="number" name="size" placeholder="Ball Size volume in inches" required>
        <button type="submit">Add Ball</button>
    </form>

    <br>
    <br>
    <br>
    <h1>Bucket Suggestion</h1>
    <br>
    <form method="POST" action="{{url('/distribute-balls')}}">
        @csrf
        @if($ballName)
            @foreach($ballName as $ball)
            
            {{ucfirst($ball->color)}} : <input type="text" id="quantity_{{ $ball->id }}" name="quantity_{{ $ball->id }}" placeholder="Ball quantity" required>
            @endforeach
        @endif
        <br>
        <br>
        <button type="submit">Place Fills In Bucket</button>
        <br>
        <br>
        @if(session()->has('error'))
        <div class="alert alert-danger alert-block" style="color:red">
            {{ session()->get('error') }}
        </div>
    @endif
    </form>
    <br>
    <br>
    <br>
    <h1>Results </h1>
    <br>
    @if($bucketball)
        @foreach($bucketball as $bucket) 
            <?php 
               $bucketname =DB::table('buckets')->where('id',$bucket->bucket_id)->value('name');
               $ballname   =DB::table('balls')->where('id',$bucket->ball_id)->value('color');
               
            ?>
            <h2>{{ ucfirst($bucketname)  }} |  {{ ucfirst($ballname)  }}  {{$bucket->ball_count}}</h2>
           
        @endforeach
    @endif
    </div>
    <div class="content">

  </div>
</div>




<style>
body {
  font-family: 'Roboto', sans-serif;
  background: #f0f5f9;
}
.card {
  position: relative;
  margin: 150px auto;
  width: 700px;
  padding: 20px;
  box-shadow: 3px 10px 20px rgba(0, 0, 0, 0.2);
  border-radius: 3px;
  border: 0;
  .circle {
    border-radius: 3px;
    width: 150px;
    height: 150px;
    background: black;
    position: absolute;
    right: 0px;
    top: 0;
    background-image: linear-gradient(to top, #fbc2eb 0%, #a6c1ee 100%);
    border-bottom-left-radius: 170px;
  }
  .content {
    margin-top: 25px;
    display: flex;
    flex-direction: column;
  }
  h1 {
    font-size: 34px;
    font-weight: bold;
    margin-bottom: 0;
  }
  h2 {
    font-size: 18px;
    letter-spacing: 0.5px;
    font-weight: 300;
  }
  .social {
    margin-bottom: 5px;
      a {
   text-decoration: none !important;
   color: black;
    margin-left: 8px;
    font-weight: 300;
    i {
      font-weight: 400;
    }
  }
  }
  .location {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    i {
      color: red;
    }
    p {
      font-weight: 300;
    }
  }
  }

</style>    