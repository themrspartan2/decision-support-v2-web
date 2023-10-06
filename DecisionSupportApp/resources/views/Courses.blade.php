@extends('layouts.master')

@section("title")
Courses
@endsection

@section("pageJS")
<script src="decisions.js?rev=1"></script>
@endsection

@section("content")
<div class="cards">
  <div class="card">
    <div class="card-body">
      <h2 class="card-title">CSE 382</h2>
      <p>
        Color by Number
      </p>
      <a class="boxLink" href="/course"></a>
    </div>
  </div>
  
  <div class="card">
  
    <div class="card-body">
      <h2 class="card-title">CSE 386</h2>
      <p>
        Connect the Dots
      </p>
      <a class="boxLink" href="/course"></a>
    </div>
  </div>
  
  <div class="card">
    <div class="card-body">
      <h2 class="card-title">ECO 101</h2>
      <p>
        Aerospace Engineering
      </p>
      <a class="boxLink" href="/course"></a>
    </div>
  </div>
  
  <div class="card">
  
    <div class="card-body">
      <h2 class="card-title">CSE 483</h2>
      <p>
        Play-doh molding
      </p>
      <a class="boxLink" href="/course"></a>
    </div>
  </div>
</div>
@endsection
