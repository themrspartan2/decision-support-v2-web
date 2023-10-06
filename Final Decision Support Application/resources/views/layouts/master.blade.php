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
    @include('partials.navbar')
	
	@yield('content')

  </body>
</html>
