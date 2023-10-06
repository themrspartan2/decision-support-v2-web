@extends('layouts.master')

@section("title")
Course
@endsection

@section("pageJS")
<script src="decisions.js?rev=1"></script>
@endsection

@section("content")
<div class="cards">
  <div class="card">
    <div class="card-body">
      <h2 class="card-title">Acorn Collecting</h2>
      <a class="boxLink" href="/groups"></a>
    </div>
  </div>
  
  <div class="card">
  
    <div class="card-body">
      <h2 class="card-title">Project 4</h2>
      <a class="boxLink" href="/groups"></a>
    </div>
  </div>
  
  <div class="card">
    <div class="card-body">
      <h2 class="card-title">Resurrect Lincoln</h2>
      <a class="boxLink" href="/groups"></a>
    </div>
  </div>
  
  <div class="card">
  
    <div class="card-body">
      <h2 class="card-title">Draw a Circle</h2>
      <a class="boxLink" href="/groups"></a>
    </div>
  </div>
</div>
@endsection
