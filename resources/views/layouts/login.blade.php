<!doctype html>
<html lang="en">
  <head>
    @include('partials.commonHeaderIncludes')

	<!-- CSS -->
   <link rel="stylesheet" href="{{ URL::asset('decisions.css') }}">
	
	<!-- JavaScript -->
	@yield('pageJS')
    <title>@yield('title')</title>
  </head>
  <body onbeforeunload="showLoader()">
    @include('partials.loader')
    <div id="loginBackground">
        <div class="blur"></div>
    </div>
	
	@yield('content')

  </body>
</html>
