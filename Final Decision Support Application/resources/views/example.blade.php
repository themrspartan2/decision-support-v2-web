<!DOCTYPE html>
<html>
    <head>
        <title>Example API CallS</title>
    </head>
    <body>
        <h1>Courses: </h1>
        @foreach ($courses as $course)
        <div class="flex" style="align-items: center;">
            <p>{{ $course->name }}</p>
            <form method="post" action="{{route('getStudents', [$course->id, $course->name]) }}" accept-charset="UTF-8">
                {{ csrf_field() }}
                <button type="submit" style="max-height: 25px; margin-left: 20px;">See Students</button>
            </form>
            <form method="post" action="{{route('makeSurvey', [$course->id]) }}" accept-charset="UTF-8">
                {{ csrf_field() }}
                <button type="submit" style="max-height: 25px; margin-left: 20px;">Make Survey</button>
            </form>
        </div>
        @endforeach
        <form method="get" action="{{route('index') }}" accept-charset="UTF-8">
            {{ csrf_field() }}
            <button type="submit" style="max-height: 25px; margin-left: 20px;">Return</button>
        </form>
    </body>
</html>
