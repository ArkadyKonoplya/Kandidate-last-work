@extends('layouts.candidates')
@section('template')
    <style>
        .m-content.admin-content h4{
            font-family: "Avenir-Heavy";
            font-size: 32px;
            color: #434656;
        }        
        .m-content.admin-content{
            font-family: "Avenir-Roman";
        }
        .candidate-profile {
            margin-bottom: 30px;
        }
        .candidate-profile .profile-details {
            padding: 10px;
            border: 1px solid #ddd;
            border-top: 0;
            height: 250px;
        }
        .box-container {
            padding: 15px;
            border-radius: 5px;
            background: #fff;
            margin-top: 20px;
            margin-bottom: 15px;
        }
        .box-container > span{
            font-size: 22px;
            color: #3D4564;
        }
        .admin-content {
            padding: 0px 0px !important;
        }
        .top-section {
            background: linear-gradient(to top,#0074E4,#5867DD);
            padding-top: 80px;
        }
        .top-section .row{
            background: #fff;
        }
        .top-section .col-md-3{
            padding: 0;
        }
        .m-subheader {
            position: absolute;
            left: 0;
            right: 0;
            width: 100%;
            max-width: 1140px;
            display: block;
            padding-top: 0px;
            margin: 0 auto;
            padding-left: 0px !important;
        }
        .top-section .col-md-4{
            padding-left: 0;
        }
        .mr-auto {
            margin-top: 15px;
            margin-left: 10px;
        }
        #candidates .col-md-4{
            padding-left: 0;
        }
        .box-skills{
            font-size: 14px;
        }
        .sector{
            font-size: 12px;
            color: #7A7B81;
            text-transform: uppercase; 
        }
        .period{
            font-size: 12px;
            text-align: right;
            color: #7A7B81;
            margin-right: 20px;
            float: right;
        }
        .hf div{
            display: inline-block;
        }
        .com-name{
            font-size: 22px;
            color: #FF7B7D;
        }
        .exp-position{
            display: inline-block;
            padding: 5px 15px;
            box-shadow: 0 2px 2px rgba(0,0,0,.1);
            border-radius: 20px;
        }
        .box-container > span.lang{
            color: #FF7B7D;
            font-size: 20px;
        }
        .box-container > span.lang-class{
            font-size: 16px;
        }
        @media (min-width: 768px){
            .col-md-8{
                padding-right: 0 !important 
            }
        }
    </style>
    <div class="m-content admin-content">
        <div class="m-portlet m-portlet--mobile bg-admin">
            <div class="m-portlet__body">
                @if (Session::has('user_message'))
                    <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show"
                         role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                        {{Session::get('user_message')}}
                    </div>
                @endif
                <div class="top-section">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-4">
                            <img src="{{!empty($candidate->picture) ? '/public/uploads/' . $candidate->picture : '/no-photo.jpg'}}" alt="" style="width: 100%">
                            </div>
                            <div class="col-md-8">
                            <div class="box-container">
                                    <h4>{{$candidate->first_name}} - {{$candidate->optional_interview}}</h4>
                                    <div style="font-size: 18px !important;;">
                                        {{$candidate->roles->implode(', ')}}
                                    </div>
                                    <div style="font-size: 18px !important;;">
                                        {{$candidate->salaryExpectations->implode(', ')}}; {{$candidate->linguisticProficiency->implode(', ')}}.
                                    </div>
                                    <div class="gehal">{!! nl2br($candidate->general_notes) !!}</div>
                                    <script>
                                        var inf = $('.gehal').text();
                                        var substr = inf.substring(inf.indexOf('Gehaltsvorstellung'));
                                        inf.text(substr);
                                    </script>
                                </div>   
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row" id="candidates">
                        <div class="col-md-4">
                            <div class="box-container box-skills">
                                <span style="font-weight: bold">
                                    Technische Fähigkeiten
                                </span>
                                <p></p><br>{!! $candidate->skills->implode('<hr>') !!}</br>
                                
                            </div>
                            <div class="box-container box-skills">
                                <span style="font-weight: bold">
                                    Core Competences
                                </span>
                                <p></p><br>{!! $candidate->competences->implode('<hr>') !!}</br>
                            </div>
                            <div class="box-container box-skills">
                                <span style="font-weight: bold">
                                    Other interviews and offers:
                                </span>
                                <p></p><br>{!! $candidate->other_interview ? $candidate->comment_area_text : 'No' !!}</br>
                            </div>
                            <div class="box-container box-skills">
                                <span style="font-weight: bold">
                                    References Details:
                                </span>
                                <p></p><br>{{$candidate->client_name}}</br>
                            </div>
                            @if(Auth::user()->isAdmin)
                            <div class="box-container">
                                <span style="font-weight: bold">
                                    Is confirmed?
                                </span>
                                {!! $candidate->is_confirmed ? 'Yes' : 'No' !!}
                            </div>
                            <div class="box-container">
                                <span style="font-weight: bold">
                                    Is active?
                                </span>
                                {!! $candidate->is_active ? 'Yes' : 'No' !!}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="box-container">
                                <span style="font-weight: bold">in aller Kürze über mich</span>
                                <p></p>
                                <div>{!! nl2br($candidate->general_notes) !!}</div>
                            </div>
                            <div class="box-container">
                                <span style="font-weight: bold">Möglicher Arbeitsort:</span>
                                <p></p>{!! $candidate->possibleLocation->implode(',<br> ') !!}
                            </div>
                            <div class="box-container">
                                <span style="font-weight: bold">Berufserfahrung</span>
                                <br>
                                @if(isset($data))
                                    @foreach ($data as $subdata)

                                    <div class="" style="    border-bottom: 1px solid #d7d9de;" >
                                        <div class="hf" style="    margin-top: 10px;">
                                        <div class="sector">{{\App\Kandidate::WORK_EXPERIENCE_SECTORS[$subdata->sector]}}</div>
                                        <div class="period">{{substr($subdata->start_month,0,3)}} {{$subdata->start_year}} -
                                        
                                            @if($subdata->end_month == "present")
                                            present
                                            @else
                                            {{substr($subdata->end_month,0,3)}} {{$subdata->end_year}}
                                            @endif
                                        </div>   
                                        </div>
                                        <br>
                                        <div class="com-name">{{$subdata->country_name}}</div>
                                        <br>
                                        <div class="exp-position">@php $roles = \App\Kandidate::ROLES; @endphp
                                        {{isset($roles[$subdata->position]) ? $roles[$subdata->position] : ''}}</div>
                                        <br>
                                        <br>
                                        <div class="prof-experience" style="word-break: break-all;    margin-bottom: 20px;">{!! $subdata->position_desc !!}</div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="box-container">
                               
                                <span style="font-weight: bold">Ausbildung, Universität und Abschlüsse</span>
                                <br>
                                @if(isset($data_education))
                                @foreach ($data_education as $subdata_education)

                                <div class="" style="    border-bottom: 1px solid #d7d9de;" >
                                    <div class="hf" style="    margin-top: 10px;">
                                    <div class="com-name">{{$subdata_education->education_topic}}</div>
                                    <div class="period">{{substr($subdata_education->start_month,0,3)}} {{$subdata_education->start_year}} - 
                                    @if($subdata_education->end_month == "present")
                                    present
                                    @else
                                    {{substr($subdata_education->end_month,0,3)}} {{$subdata_education->end_year}}
                                    @endif</div>   
                                    </div>
                                    <br>
                                    <div class="sector">{{$subdata_education->education_uni}}</div>
                                    <br>
                                    <div class=""> @foreach(\App\Kandidate::EDUCATION_ITEMS as $i => $value)
                                               
                                                @if((strpos($subdata_education->education_list, (string)$i) !== false))
                                                 <label class="">
                                                <font style="vertical-align: inherit;">
                                                <font style="vertical-align: inherit;"> {{$value}}
                                                        </font></font>
                                                </label>
                                                @endif
                                                @endforeach</div>
                                    <br>
                                </div>
                                @endforeach
                                @endif
                            </div>
                            <div class="box-container">
                                <span style="font-weight: bold">Languages:</span>
                                <p></p>
                                <span class="lang">Deutsch:</span><br><span class="lang-class">{{$candidate->linguisticProficiency->implode(', ')}}</span>
                                <hr>
                                <span class="lang">English</span><br><span class="lang-class">{{$candidate->linguisticProficiencyEn->implode(', ')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
