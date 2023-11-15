@extends('layouts.master')

@section("title")
About Us
@endsection

@section("pageJS")
<script src="{{ URL::asset('FAQs.js') }}"></script>
@endsection

@section("content")
    <h1 style="text-align: center; color: white">Our Team</h1>
    <br>
    <div class="faqBox">
        <button type="button" class="collapseButton">Hunter Hicks</button>
        <div class="collapsibleContent">
            <div>
                <div style="width: 210px; float: left">
                    <img src="{{ asset('images/Hunter.jpg') }}" alt="Hunter Image">
                    <br>
                    <br>
                </div>
                <div style="width:1000px; float: left">
                    <p>Hunter Hicks is the Lead Canvas Integegration Specialist and Lead Backend Developer.
                        Hunter was responsible for researching and implementing all calls made to the Canvas
                        LMS API. Hunter was also responsible for developing most of the backend of our application
                        as well as delegating and supervising the backend work done by the Assistant Backend Developer.
                    </p>

                </div>
            </div>
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">Aidan Becker</button>
        <div class="collapsibleContent">
            <div>
                <div style="width: 210px; float: left">
                    <img src="{{ asset('images/AidanB.jpg') }}" alt="Becker Image">
                    <br>
                    <br>
                </div>
                <div style="width:1000px; float: left">
                    <p>Aidan Becker is the Lead Algorithm Developer. Aidan was responsible for the development of the
                        algorithms used by our project. Aidan was also responsible for making any changes to the algorithms
                        that were necessary for the smooth integration of the algorithms into our application.
                         Aidan was also responsible for delegating and supervising work done by the Assistant Algorithm Developer.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">Aidan Li</button>
        <div class="collapsibleContent">
            <div>
                <div style="width: 210px; float: left">
                    <img src="{{ asset('images/AidanL.jpg') }}" alt="Li Image">
                    <br>
                    <br>
                </div>
                <div style="width: 1000px; float: left">
                    <p>Aidan Li is the Lead Frontend Developer and Assistant Backend Developer. 
                        Aidan was responsible for the design and development of our application's frontend
                         as well as making changes to the backend that were requested by the Lead Backend Developer.
                         Aidan oversaw the entire design process for the frontend of our application and was also
                         responsible for delegating changes to the Assistant Frontend Developer.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">Kyle Reed</button>
        <div class="collapsibleContent">
            <div>
                <div style="width: 210px; float: left">
                    <img src="{{ asset('images/Kyle.png') }}" alt="Kyle Image">
                    <br>
                    <br>
                </div>
                <div style="width: 1000px; float: left">
                    <p>Kyle Reed is the Assistant Algorithm Developer and Lead Administration Worker. 
                        Kyle was responisble for making changes to the algorithms that were requested by the 
                        Lead Algorithm Developer. In addition to this, Kyle was also in charge of taking care 
                        of administrative tasks that would otherwise distract the main developers from their 
                        specific tasks. This includes things like: writing papers, preparing presentations, 
                        creating diagrams, etc.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="faqBox">
        <button type="button" class="collapseButton">Elton Zeng</button>
        <div class="collapsibleContent">
            <div>
                <div style="width: 210px; float: left">
                    <img src="{{ asset('images/elton.jpg') }}" alt="Elton Image">
                    <br>
                    <br>
                </div>
                <div style="width: 1000px; float: left">
                    <p>Elton Zeng was the Assistant Frontend Developer and Lead Page Creator.
                         Elton was responsible for creating new pages that were requested of him
                         by the Lead Frontend Developer. Elton was also responsible for making any
                         changes to the frontend that were requested by the Lead Frontend Developer.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
@endsection