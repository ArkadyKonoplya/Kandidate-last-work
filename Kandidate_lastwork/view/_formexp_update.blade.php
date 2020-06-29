{{ csrf_field() }}
<style>
    .has-error input {
        border-color: red;
    }
    .has-error .help-block {
        color: red;
    }
    #view{
        margin-right: 0;
        margin-left: 0;
    }
    #event{
        border: 1px solid #d0d1d4;
        margin-left:30px;
        margin-right:30px;
    }
    .event_row{
        margin-top: 10px;
    }
    .event_row_row{
        margin-right:0;
        float:right;
    }
    #del{
        margin-right: 10px;
    }
    #edit{
        border: 1px solid #dbdce0;
    }
    .form_event{
        float:right;
    }
    #add{
        padding-left: 45px;
        padding-top:0px;
        padding-bottom:0px;
    }
    #add_exp{
        margin-left: 30px;
    }
</style>
<div class="m-portlet__body">
    <div class="container">
                <div class="col-md-6"id="work_exp">
                    <div class="modal-header">
                    <a href="{{route('candidates.add_dataexp')}}" class="close" data-dismiss="modal">Add</a>
                    <h4 class="modal-title">Experience List</h4>
                    </div>
                    @foreach ($data as $subdata)
                    <a href="{{route('candidates.experience_sub_delete',$subdata->id)}}"  style="float: none;font-size: 2rem;"  class="close" data-dismiss="modal">&times;</a>
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <span id="form_output"></span>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sector</label>
                                        <select class="form-control"  name="work_experience_sector{{$subdata->id}}">
                                            @foreach(\App\Kandidate::WORK_EXPERIENCE_SECTORS as $id => $sector)
                                                <option {{$id == $subdata->sector ? 'selected' : ''}} value="{{$id}}">{{$sector}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Position</label>
                                        <div >
                                        <select class="form-control"  name="work_experience_position{{$subdata->id}}">
                                            @foreach(\App\Kandidate::ROLES as $id => $role)
                                                <option {{$id == $subdata->position ? 'selected' : ''}} value="{{$id}}">{{$role}}</option>
                                            @endforeach
                                        </select>
                                        </div>
                                    </div> 
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Start month</label>
                                            
                                            <!-- <input type="text" name="work_experience_period" id="work_period" class="form-control" placeholder="Enter Your Last name" value=""> -->
                                            <select class="form-control" id="position" name="start_month{{$subdata->id}}">
                                                    @if($subdata->start_month == 'January')
                                                            
                                                            <option selected>January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option>
                                                    @elseif($subdata->start_month == 'February')
                                                            <option >January</option>
                                                            <option selected>February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option>
                                                    @elseif($subdata->start_month == 'March')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option selected>March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'April')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option selected>April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'May')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option selected>May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'June')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option selected>June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'July')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option selected>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'August')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option selected>August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'September')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option selected>September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'October')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option selected>October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'November')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option selected>November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->start_month == 'December')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option selected>December</option>   
                                                    @endif
                                                    
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Start year</label>
                                            <select class="form-control" id="position" name="start_year{{$subdata->id}}">
                                                @for($i=2019;$i>=1990; $i--)
                                                    @if($subdata->start_year == $i)
                                                    <option selected> {{$i}} </option>
                                                    @else
                                                        <option > {{$i}} </option> 
                                                    @endif
                                                    
                                                @endfor
                                            </select>
                                        </div> 
                                    </div> 
                                    <input type="hidden" class="id" value="{{$subdata->id}}">
                                    <script src="../../assets/app/js/jquery1.9.1.min.js" type="text/javascript"></script>
                                <script src="../../assets/app/js/jquery-ui1.9.1.min.js" type="text/javascript"></script>
                               <script language="javascript" type="text/javascript">
                                        $(document).ready(function () {
                                            // if($('#end_month').val()) 
                                             
                                                // alert($('#end_month').val() + val);
                                                $('.checkpresent').attr('checked', true);
                                                $(".endpresent").attr("disabled", "disabled");
                                                // $(".endpresent").attr("disabled", "disabled");
                                                $(".endpresent").css({"display":"none"});
                                                // $(".endpresent").css({"display":"none"});
                                                // $("#checked").val("present"); 
                                           
                                            
                                            
                                            /* Get the checkboxes values based on the class attached to each check box */
                                            $(".check_true").click(function() {
                                                var val;
                                                val = $(this).val();
                                                // alert(val);
                                                console.log(val);
                                                if ($(this).is(":checked"))
                                                            {
                                                            // it is checked
                                                            $("#end_month"+val).attr("disabled", "disabled");
                                                            $("#end_year"+val).attr("disabled", "disabled");
                                                            $("#end_month"+val).css({"display":"none"});
                                                            $("#end_year"+val).css({"display":"none"});
                                                            $("#checked"+val).val("present");   
                                                            // alert($(this).val());
                                                            }else{
                                                                $("#end_month"+val).removeAttr('disabled');
                                                                $("#end_year"+val).removeAttr('disabled');
                                                                $(".end_month"+val).removeAttr('style');
                                                                $("#end_year"+val).removeAttr('style');
                                                                $("#checked"+val).val("");    
                                                                // alert("testsss");

                                                            }
                                            });
                                        });
                                   

                                </script>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>End month</label>
                                            <select class="form-control end{{$subdata->end_month}} end_month{{$subdata->id}}" id="end_month{{$subdata->id}}" name="end_month{{$subdata->id}}">
                                                @if($subdata->end_month == 'January')
                                                            
                                                            <option selected>January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option>
                                                    @elseif($subdata->end_month == 'February')
                                                            <option >January</option>
                                                            <option selected>February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option>
                                                    @elseif($subdata->end_month == 'present')
                                                            <option >January</option>
                                                            <option>February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option>
                                                    @elseif($subdata->end_month == 'March')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option selected>March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'April')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option selected>April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'May')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option selected>May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'June')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option selected>June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'July')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option selected>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'August')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option selected>August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'September')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option selected>September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'October')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option selected>October</option>
                                                            <option >November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'November')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option selected>November</option>
                                                            <option >December</option> 
                                                    @elseif($subdata->end_month == 'December')
                                                            <option >January</option>
                                                            <option >February</option>
                                                            <option >March</option>
                                                            <option >April</option>
                                                            <option >May</option>
                                                            <option >June</option>
                                                            <option>July</option>
                                                            <option >August</option>
                                                            <option >September</option>
                                                            <option >October</option>
                                                            <option >November</option>
                                                            <option selected>December</option>   
                                                    @endif
                                                    
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>End year</label>
                                            <select class="form-control end{{$subdata->end_month}}" id="end_year{{$subdata->id}}" name="end_year{{$subdata->id}}">
                                            @for($i=2030;$i>=1990; $i--)
                                                    @if($subdata->end_year == $i)
                                                    <option selected> {{$i}} </option>
                                                    @else
                                                        <option > {{$i}} </option> 
                                                    @endif
                                                    
                                                @endfor
                                            </select>
                                        </div> 
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    <label for="currently-working-here" class="checkbox" i18n-id="bedccffba7bf5594ee57cbc421206f73" i18n-msg="currently working here"><input type="checkbox" name = "checkbox" id="currently-working-here{{$subdata->id}}" value="{{$subdata->id}}" class="ng-pristine ng-untouched check_true ng-valid check{{$subdata->end_month}}"> Currently working here</label>
                                     <!-- <input type="checkbox" id="checked">currently working here -->
                                     <input type="hidden" id="checked{{$subdata->id}}" name="checked" value="">
                                    </div> 
                                </div>
                               
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Name of Company</label>
                                        <div >
                                            <input type="text" class="form-control"  name="work_experience_company_name{{$subdata->id}}" id="comp_name_view" value="{{$subdata->country_name}}">
                                        </div>
                                    </div>
                                </div>
                                
                            </div> 
                            <div class="row" style="border-bottom: 1px solid #b5b5b5;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label>Description of position</label>
                                            <textarea class="form-control m-input" id="position_desc_view" name="work_experience_position_description{{$subdata->id}}" rows="3">{{$subdata->position_desc}}</textarea>
                                    </div> 
                                </div>
                            </div>
                    </div>
                    @endforeach
                    <button id="profile" type="submit" class="btn btn-primary">  Update </button>
                    <a href="{{route('candidates.update_data_education')}}"><button id="ajaxSubmit" type="button" class="btn btn-danger" data-dismiss="modal">Next</button></a> 
                </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</div>