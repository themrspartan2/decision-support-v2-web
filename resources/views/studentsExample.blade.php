<!DOCTYPE html>
<html>
    <head>
        <title>Example API Call</title>
    </head>
    <body>
        <h1>{{$courseName}}</h1>
        @foreach ($students as $student)
        <div class="flex" style="align-items: center;">
            <p>{{ $student->name }}</p>
        </div>
        @endforeach
        <form method="get" action="{{route('index') }}" accept-charset="UTF-8">
            {{ csrf_field() }}
            <button type="submit" style="max-height: 25px; margin-left: 20px;">Return</button>
        </form>
    </body>
</html>
