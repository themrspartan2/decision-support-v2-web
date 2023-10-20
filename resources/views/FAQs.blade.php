@extends('layouts.master')

@section("title")
FAQ
@endsection

@section("pageJS")
<script src="{{ URL::asset('FAQs.js') }}"></script>
@endsection

@section("content")
    <h1 style="text-align: center; color: white">Frequently Asked Questions</h1>
    <br>
    <div class="faqBox">
        <button type="button" class="collapseButton">How do I login?</button>
        <div class="collapsibleContent">
            You must use your Canvas API key for this app, and be listed as an instructor in at least one course.
            To get this, in Canvas go to Account->Settings->Approved Integrations->New Access Token.
            This dialog box will allow you to generate an API key which you can use to login to the app.
            <br><br>
            I'm not sure how you got to this page if you didn't log in though...
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">Why don't the cluster gender and project choices functions work?</button>
        <div class="collapsibleContent">
            For these to work, you must create a course survey, which you can do on the Course page.
            After students complete the survey, these functions will work correctly.
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">Why does it take so long to push groups to Canvas?</button>
        <div class="collapsibleContent">
            Each student must be pushed to Canvas separately, which can take a long time depending on your internet connection.
            Enjoy the loading animation while you wait.
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">How can I make groups if they've already been created?</button>
        <div class="collapsibleContent">
            You can remove the group set for the project on canvas. After doing this, you will be able to use the algorithms to
            assign students to groups normally.
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">I want to use the loading animation in my site. Where can I get it?</button>
        <div class="collapsibleContent">
            You can get the code for it <a href="https://codepen.io/GeoxCodes/pen/PBoQZa" target="_blank">here</a>, unless the site doesn't exist anymore.
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">How can I learn to make such effective and efficient algorithms?</button>
        <div class="collapsibleContent">
            You'll have to ask Aidan Becker, or your local code wizard.
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">I've encountered a bug. Where can I report this?</button>
        <div class="collapsibleContent">
            Impossible. We would never write software with bugs.
            <br><br>
            But if you <span style="font-style: italic">somehow</span> encounter one...<br>
            The dev team has probably graduated by now, but you're welcome to fix it yourself.
        </div>
    </div>
    
@endsection
