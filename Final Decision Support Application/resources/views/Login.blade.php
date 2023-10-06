@extends('layouts.login')

@section("title")
Login
@endsection

@section("pageJS")
<script src="{{ URL::asset('Login.js') }}"></script>
@endsection

@section("content")

<div class="outer">
    <div class="middle">
        <div id="login">

            <div class="outerInLogin">
            <div class="middle padded">
            <h1>Welcome</h1><br>
            <form method="post" action="{{route('getCourses') }}" accept-charset="UTF-8">
                {{ csrf_field() }}
                <div class="form-group justify-content-center">
                    <label for="api_Key">Instructor API Key</label>
                    <input type="text" name="api_key" class="form-control" id="api_Key" aria-describedby="Instructor API Key" placeholder="Enter API Key">
                </div><br>
                <button type="submit" class="btn btn-dark" onsubmit="showLoader()">Login</button>
            </form>
            </div>
            </div>

        </div>
    </div>
</div>

@if(session('badKey'))
    <script type="text/javascript" >
        alert('{{ session('badKey') }}');
    </script>
@endif
@if(session('isStudent'))
    <script type="text/javascript" >
        alert('{{ session('isStudent') }}');
    </script>
@endif

@endsection
