<!doctype html>
<html lang="en">
  <head>
    @include('partials.commonHeaderIncludes')

	<!-- CSS -->
   <link rel="stylesheet" href="decisions.css">
	
	<!-- JavaScript -->
	@yield('pageJS')
    <title>@yield('title')</title>
  </head>
  <body>
	@include('partials.navbar')
	
	@yield('content')

  </body>
</html>
