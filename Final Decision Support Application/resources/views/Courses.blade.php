@extends('layouts.master')

@section("title")
Courses
@endsection

@section("pageJS")
<!-- <script src="{{ URL::asset('decisions.js') }}"></script> -->
@endsection

@section("content")

<div class="cards">

@foreach ($courses as $course)

    <div class="card">
        <div class="card-body">
            <h2 class="card-title">{{ $course->course_code }}</h2>
            <p>
                {{ $course->name }}
            </p>
            <a class="boxLink" href="{{route('getProjects', [$course->id]) }}">{{ csrf_field() }}</a>
            <!--
            <a class="boxLink" href="/course"></a>
            -->
        </div>
    </div>


    <!--
        <div class="flex" style="align-items: center;">
            <p>{{ $course->name }}</p>
            <form method="post" action="{{route('getProjects', [$course->id]) }}" accept-charset="UTF-8">
                {{ csrf_field() }}
                <button type="submit" style="max-height: 25px; margin-left: 20px;">See Students</button>
            </form>
            <form method="post" action="{{route('makeSurvey', [$course->id]) }}" accept-charset="UTF-8">
                {{ csrf_field() }}
                <button type="submit" style="max-height: 25px; margin-left: 20px;">Make Survey</button>
            </form>
        </div>
        -->
@endforeach

</div>

@endsection
