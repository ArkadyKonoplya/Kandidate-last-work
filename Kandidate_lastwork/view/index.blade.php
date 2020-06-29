@extends('layouts.candidates')
@section('template')
    <style>
        .candidate-profile {
            margin-bottom: 30px;
        }
        .candidate-profile .profile-details {
            padding: 10px;
            border-top: 0;
            height: 300px;
        }
        .candidate-number {
            float: right;
            margin: 0 0 -30px;
            position: relative;
            z-index: 2;
            top: 5px;
            right: 5px;
            padding: 3px 11px;
            background: #fff;
            border-radius: 19px;
        }
        .m-content.admin-content strong{
            font-family: "Avenir-Heavy";
        }        
        .m-content.admin-content{
            font-family: "Avenir-Roman";
        }
        .profile-details{
            background: #fff;
        }
        .picture{
            border-radius: 10px 10px 0 0;
            background: #fff;
            position: relative;
        }        
        .picture img{
            border-radius: 10px;
            background: #fff;
            height: 223px
        }
        .profile-details{
            position: relative;
            border-radius: 0 0 7px 7px;
        }        
        .profile-details strong{
            display: block;
        }
        .candidate-profile:hover > div{
            box-shadow: 0 8px 10px rgba(0,0,0,.5);
        }        
        .candidate-profile:hover .profile-button a{
            background: #007BE5;
            color: #fff;
        }
        .img-info td{
            width: 50%;
        }
        .img-info{
            width: 100%;
            position: absolute;
            bottom: -12px;
            left: 0;
            font-size: 14px;
            z-index: 1;
        }        
        .img-info div{
            background: #fff;
            border-radius: 10px;
            padding: 0px 15px;
            width: fit-content;
            margin: 0 auto;
            text-align: center;
        }
        .firstName{
            font-size: 18px;
            margin-top: 10px;
        }
        .Einsatzort{
            font-size: 14px;
        }
        .Einsatzort strong{
            font-size: 16px;
        }
        .Fähigkeiten{
            font-size: 12px;
        }
        .Fähigkeiten strong{
            font-size: 16px;
        }
        .profile-button{
            text-align: center;
            position: absolute;
            bottom: 10px;
            width: 94%;
            left: 3%;
            right: 3%;
        }        
        .profile-button a{
            display: block;
            color: #434656;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 0;
        }
        .candidates-filter label {
            font-size: 17px !important;
        }
        .skill-items label {
            margin-left: 30px;
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
                <div class="row" id="candidates">
                    <div class="col-md-3">
                        @include('kandidate._filter')
                    </div>
                    <div class="col-md-9 col-lg-9">
                        <div class="loader_msg" style='display: block;'>
                            <img src="../assets/app/media/img/logos/loader.gif" width='132px' height='132px'
                                 style="height: 70px;width: 67px;margin-left: 40%;">
                        </div>
                        <div class="candidates-container">
                            <div class="row">
                                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 candidate-profile" v-for="candidate in candidates" style="display: none">
                                    <div class="picture">
                                        <span class="candidate-number">@{{ candidate.raw.number }}</span>
                                        <img :src="candidate.image" alt="" style="width: 100%;">
                                        <table class="img-info" style="width: 100%;">
                                            <tr>
                                                <td><div>@{{ candidate.salaryExpectations }}</div></td>
                                                <td class="text-right"><div>@{{ candidate.linguisticProficiency }}</div></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="profile-details">
                                        <div class="firstName"><strong style="color: #000;">@{{ candidate.firstName }}</strong></div>
                                        <div class="Einsatzort"><strong style="color: #000;">Möglicher Einsatzort:</strong> @{{ candidate.possibleLocation }}</div>
                                        <div class="Fähigkeiten"><strong style="color: #000;">Technische Fähigkeiten:</strong> <div v-html="candidate.techSkills"></div></div>
                                        <div class="profile-button">
                                            <a :href="candidate.viewUrl">Profile ansehen</a>
                                        </div>
                                        @if(Auth::user()->isAdmin)
                                        <table style="width: 100%">
                                            <tr>
                                                <td>
                                                    <a :href="candidate.cvUrl" v-if="candidate.cvUrl">CV</a>
                                                </td>
                                                <td class="text-right">
                                                    <span v-if="candidate.hasVideo">VIDEO</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span v-if="candidate.isActive">Active</span>
                                                </td>
                                                <td class="text-right">
                                                    <span v-if="candidate.isConfirmed">Confirmed</span>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">var allCandidates = {!! json_encode($candidates) !!};</script>
@endsection