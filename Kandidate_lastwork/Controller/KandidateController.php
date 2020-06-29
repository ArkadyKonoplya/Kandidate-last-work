<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Kandidate;
use App\Mail\sendFestanstellungMail;
use App\Models\CandidateInvite;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Response;
use Session;
use Validator;

class KandidateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * @return array|mixed
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $permission = [];
        if (!$user->isAdmin) {
            $permission = DB::table('emp_permission')->where('emp_id', $user->id)->first();
        }
        $rate = Kandidate::HOURLY_RATES;
        $role = Kandidate::ROLES;
        $availability = array(
            "1" => "A1",
            "2" => "A2",
            "3" => "B1",
            "4" => "B2",
            "5" => "C1",
            "6" => "C2",
        );
        $skills = \DB::table('competences_skill')->select('id', 'skill')->get()->toArray();
        $willingnessToRelocate = [
            1 => 'Weltweit',
            2 => 'Europaweit',
            3 => 'Deutschlandweit',
            4 => 'Bundesland',
            5 => 'Stadt',
        ];

        $candidates = $this->getCandidates($request);

        return view('kandidate.index', compact('rate', 'role', 'availability', 'skills', 'permission', 'willingnessToRelocate', 'candidates'));
    }

    public function show($id)
    {
        $candidate = Kandidate::findOrFail($id);
        $skills = \DB::table('competences_skill')->select('id', 'skill')->get()->toArray();
        $number = $candidate['number'];
        $data = DB::table('experiences')
        ->where('client_id',$number)
        ->get();
        $data_education = DB::table('educations')
        ->where('client_id',$number)
        ->get();
        // var_dump($data);exit();
        return view('kandidate.show', compact('candidate', 'skills','data','data_education'));
    }

    private function getCandidates(Request $request)
    {
        $permission = [];
        $freelancer_data = [];
        $rate = (isset($request->datatable['query']['rate']) && ($request->datatable['query']['rate'] != "all") ? $request->datatable['query']['rate'] : '');
        $role = (isset($request->datatable['query']['role']) && ($request->datatable['query']['role'] != "all") ? $request->datatable['query']['role'] : '');
        $skills = (isset($request->datatable['query']['skills']) && ($request->datatable['query']['skills'] != "all") ? $request->datatable['query']['skills'] : '');
        $can_travel_to_germany = (isset($request->datatable['query']['can_travel_to_germany']) && ($request->datatable['query']['can_travel_to_germany'] != "all") ? $request->datatable['query']['can_travel_to_germany'] : '');
        $free_availabilty = (isset($request->datatable['query']['free_availabilty']) && ($request->datatable['query']['free_availabilty'] != "all") ? $request->datatable['query']['free_availabilty'] : '');
        $free_per_week = (isset($request->datatable['query']['free_per_week']) && ($request->datatable['query']['free_per_week'] != "all") ? $request->datatable['query']['free_per_week'] : '');
        $free_per_week_en = (isset($request->datatable['query']['free_per_week_en']) && ($request->datatable['query']['free_per_week_en'] != "all") ? $request->datatable['query']['free_per_week_en'] : '');
        $cv_recieved = (isset($request->datatable['query']['cv_recieved']) && ($request->datatable['query']['cv_recieved'] != "all") ? $request->datatable['query']['cv_recieved'] : '');

        $userfilter = "SELECT `number` AS S_No,ag_kandidates.*,(SELECT 0) AS list_id FROM ag_kandidates WHERE id IS NOT NULL";

        if(!Auth::user()->isAdmin) {
            $userfilter .= ' AND is_active = 1 AND is_confirmed = 1';
        }

        if ($rate != '') {
            $userfilter .= " AND (";
            foreach ($rate as $key => $value) {
                $userfilter .= " FIND_IN_SET('" . $value . "',hourly_rate) OR";
            }
            $userfilter = substr($userfilter, 0, -3);
            $userfilter .= " ) ";
            $rate = implode(',', $rate);
        }

        if ($role != '') {
            $userfilter .= " AND (";
            foreach ($role as $key => $value) {
                $userfilter .= " FIND_IN_SET('" . $value . "',role_definition) OR";
            }
            $userfilter = substr($userfilter, 0, -3);
            $userfilter .= " ) ";
            $role = implode(',', $role);
        }
        if ($free_per_week != '') {
            $userfilter .= " AND (";
            foreach ($free_per_week as $key => $value) {
                $userfilter .= " FIND_IN_SET('" . $value . "',availability_per_week) OR";
            }
            $userfilter = substr($userfilter, 0, -3);
            $userfilter .= " ) ";
            $free_per_week = implode(',', $free_per_week);
        }
        if ($free_per_week_en != '') {
            $userfilter .= " AND (";
            foreach ($free_per_week_en as $key => $value) {
                $userfilter .= " FIND_IN_SET('" . $value . "',availability_per_week_en) OR";
            }
            $userfilter = substr($userfilter, 0, -3);
            $userfilter .= " ) ";
            $free_per_week_en = implode(',', $free_per_week_en);
        }
        if ($free_availabilty != '') {
            $userfilter .= " AND availability = '" . $free_availabilty . "'";
        }
        if ($cv_recieved != '') {
            $userfilter .= " AND cv_recieved = '" . $cv_recieved . "'";
        }

        if(is_array($can_travel_to_germany)) {
            foreach ($can_travel_to_germany as $key => $value) {
                $userfilter .= " AND ( ";
                $userfilter .= DB::raw('FIND_IN_SET(' . DB::connection()->getPdo()->quote($value) . ', travelling)') . " OR ";
                $userfilter .= DB::raw('FIND_IN_SET(' . DB::connection()->getPdo()->quote($value) . ', traveling_state)') . " OR ";
                $userfilter .= DB::raw('FIND_IN_SET(' . DB::connection()->getPdo()->quote($value) . ', traveling_city)');
                $userfilter .= ")";
            }
        }

        if ($skills != '') {
            $userfilter .= " AND (";
            foreach ($skills as $key => $value) {
                $userfilter .= " FIND_IN_SET('" . $value . "',category_skills) OR";
            }
            $userfilter = substr($userfilter, 0, -3);
            $userfilter .= " ) ";
            $skills = implode(',', $skills);
        }

        $userfilter .= " order By `number` asc";

        $users = DB::select($userfilter);

        foreach ($users as $key => $value) {
            $freelancer_data[] = (array)$value;
            if (!currentUser()->isAdmin) {
                $freelancer_data[$key]['permission'] = currentUser()->employeePermission;
            } else {
                $permission['admin'] = 'admin';
                $freelancer_data[$key]['permission'] = ['admin' => 'admin'];
            }
            $freelancer_data[$key]['rate'] = $rate;
            $freelancer_data[$key]['role'] = $role;
            $freelancer_data[$key]['skills'] = $skills;
            $freelancer_data[$key]['free_per_week'] = $free_per_week;
            $freelancer_data[$key]['free_per_week_en'] = $free_per_week_en;
            $freelancer_data[$key]['traveling'] = is_array($can_travel_to_germany) ? implode(',', $can_travel_to_germany) : '';
            $freelancer_data[$key]['can_travel_to_germany'] = is_array($can_travel_to_germany) ? implode(',', $can_travel_to_germany) : '';
            $freelancer_data[$key]['free_availabilty'] = $free_availabilty;
            $freelancer_data[$key]['cv_url'] = !empty($value->attached_cv) ? '/public/uploads/'. $value->attached_cv : false;
            $freelancer_data[$key]['view_url'] = route('candidates.view', $value->id);
        }

        $skills = \DB::table('competences_skill')->select('id', 'skill')->get()->keyBy('id');
        $candidates = collect($freelancer_data)->map(function($candidate) use($skills) {
            $currentSkills = array_map(function($skillId) use($skills) {
                return isset($skills[$skillId]) ? '<div>' . $skills[$skillId]->skill . '</div>' : '';
            }, explode(',', $candidate['category_skills']));

            $salaryExpectations = array_map(function($hourlyRateId) {
                return !empty(Kandidate::HOURLY_RATES_SHORT[$hourlyRateId]) ? Kandidate::HOURLY_RATES_SHORT[$hourlyRateId] : '';
            }, explode(',', $candidate['hourly_rate']));

            $linguisticProficiency = array_map(function($item) {
                return !empty(Kandidate::LINGUISTIC_PROFICIENCY[$item]) ? Kandidate::LINGUISTIC_PROFICIENCY[$item] : '';
            }, explode(',', $candidate['availability_per_week']));

            $possibleLocations = array_map(function($location) use($candidate) {
                $appendix = '';
                if($location == 4 && !empty($candidate['traveling_state'])) {
                    $appendix = " - {$candidate['traveling_state']}";
                } elseif($location == 5 && !empty($candidate['traveling_city'])) {
                    $appendix = " - {$candidate['traveling_city']}";
                }
                return !empty(Kandidate::POSSIBLE_LOCATIONS[$location]) ? Kandidate::POSSIBLE_LOCATIONS[$location] . $appendix : '';
            }, explode(',', $candidate['travelling']));

            return [
                'image' => !empty($candidate['picture']) ? '/public/uploads/' . $candidate['picture'] : '/no-photo.jpg',
                'salaryExpectations' => implode(', ', $salaryExpectations),
                'linguisticProficiency' => implode(', ', $linguisticProficiency),
                'firstName' => $candidate['first_name'],
                'techSkills' => implode(' ', $currentSkills),
                'possibleLocation' => implode(', ', $possibleLocations),
                'viewUrl' => $candidate['view_url'],
                'cvUrl' => $candidate['cv_url'],
                'hasVideo' => $candidate['video'],
                'isActive' => $candidate['is_active'],
                'isConfirmed' => $candidate['is_confirmed'],
                'raw' => $candidate
            ];
        })->filter(function($candidate) use($request) {
            $q = $request->input('q');
            if(empty($q)) {
                return true;
            }

            return in_array($q, explode(', ', $candidate['techSkills']));
        });

        return $candidates;
    }

    public function addKandidateview()
    {
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }

        $candidate = new Kandidate();

        return view('kandidate.addkandidate', compact('competences', 'permission', 'candidate'));
    }

    public function addKandidate(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|unique:kandidates,number'
        ]);

       
        $user = new Kandidate;
        $user->title = '';
        $user->education = implode(',', (array) $request->get('education'));
        $user->work_experience_sector = $request->get('work_experience_sector');
        $user->work_experience_period = $request->get('work_experience_period');
        $user->work_experience_company_name = $request->get('work_experience_company_name');
        $user->work_experience_position = $request->get('work_experience_position');
        $user->work_experience_position_description = $request->get('work_experience_position_description');
        $user->first_name = $request->get('first_name');
        $user->optional_interview = $request->get('optional_interview');
        $user->last_name = '';
        $user->email = 'generic'.time().'@example.com';
        $user->Mobile = '';
        $user->home_number = '';
        $user->password = bcrypt(time());
        $availability_date = '';
        $category_skills = '';
        $travelling = '';
        $hourly_rate = '';
        $freelancer_roles = '';
        $availabile_days = '';
        $availabile_days_en = '';
        $core_competences = '';
        $freelancer_source = '';
        if ($request->has(['core_category'])) {
            $core_competences = implode(",", $request->input('core_category'));
        }
        if ($request->has(['hourly_rate'])) {
            $hourly_rate = implode(",", $request->input('hourly_rate'));
        }
        if ($request->has(['freelancer_roles'])) {
            $freelancer_roles = implode(",", $request->input('freelancer_roles'));
        }
        if ($request->has(['availabile_days'])) {
            $availabile_days = implode(",", $request->input('availabile_days'));
        }
        if ($request->has(['availabile_days_en'])) {
            $availabile_days_en = implode(",", $request->input('availabile_days_en'));
        }
        if ($request->has(['can_travel_to_germany'])) {
            $travelling = implode(",", $request->input('can_travel_to_germany'));
        }
        if ($request->has(['category_skills'])) {
            $category_skills = implode(",", $request->input('category_skills'));
        }
        if ($request->has(['freelancer_source'])) {
            $freelancer_source = implode(",", $request->input('freelancer_source'));
        }
        $hourly_rate_other_input = '';
        if ($request->has(['hourly_rate_other_input'])) {
            $hourly_rate_other_input = $request->input('hourly_rate_other_input');
        }
        $freelancer_roles_other_input = '';
        if ($request->has(['freelancer_roles_other_input'])) {
            $freelancer_roles_other_input = $request->input('freelancer_roles_other_input');
        }

        $user->reference = $request->input('reference');

        if($request->file('attached_cv')) {
            $user->attached_cv = $request->file('attached_cv')->store('cv');
        }

        if($request->file('picture')) {
            $user->picture = $request->file('picture')->store('picture');
        }
        $user->client_name = $request->input('client_name');
        $user->manager_name = $request->input('manager_name');
        $user->reference_mobile = $request->input('reference_mobile');
        $user->info_field = $request->input('info_field');
        $user->hourly_rate = $hourly_rate;
        $user->role_definition = $freelancer_roles;
        $user->availability = $request->input('part_or_full_time');
        $user->availability_date = $availability_date;
        $user->availability_per_week = $availabile_days;
        $user->availability_per_week_en = $availabile_days;
        $user->travelling = $travelling;
        $user->possible_extension = $request->input('possible_extension');
        $user->extension_text = $request->input('extension_text');
        $user->other_interview = $request->input('other_interview');
        $user->comment_area_text = $request->input('comment_area_text');
        $user->source = $freelancer_source;
        $user->category_skills = $category_skills;
        $user->general_notes = $request->input('general_notes');
        $user->core_competences = $core_competences;
        $user->traveling_state = implode(',', $request->get('traveling_state', []));
        $user->traveling_city = implode(',', $request->get('traveling_city', []));
        $user->video = $request->get('video');
        $user->number = $request->get('number');
        $user->is_confirmed = $request->has('is_confirmed');
        $user->save();
        $request->session()->flash('status', 'Successfully Added');
        return redirect(route('candidates.index'));
    }
    public function create_end(Request $request)
    {
        $request->session()->flash('status', 'Successfully Added');
        return redirect(route('candidates.index'));
        
    }
    public function update_end(Request $request)
    {
        $request->session()->flash('status', 'Successfully updated');
        return redirect(route('candidates.index'));
        
    }

    public function addKandidate_profile(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|unique:kandidates,number'
        ]);

        $this->validate($request, [
            'number' => 'required|unique:kandidates,number'
        ]);

       
        $user = new Kandidate;
        $user->title = '';
        $user->education = implode(',', (array) $request->get('education'));
        $user->work_experience_sector = $request->get('work_experience_sector');
        $user->work_experience_period = $request->get('work_experience_period');
        $user->work_experience_company_name = $request->get('work_experience_company_name');
        $user->work_experience_position = $request->get('work_experience_position');
        $user->work_experience_position_description = $request->get('work_experience_position_description');
        $user->first_name = $request->get('first_name');
        $user->optional_interview = $request->get('optional_interview');
        $user->last_name = '';
        $user->email = 'generic'.time().'@example.com';
        $user->Mobile = '';
        $user->home_number = '';
        $user->password = bcrypt(time());
        $availability_date = '';
        $category_skills = '';
        $travelling = '';
        $hourly_rate = '';
        $freelancer_roles = '';
        $availabile_days = '';
        $availabile_days_en = '';
        $core_competences = '';
        $freelancer_source = '';
        if ($request->has(['core_category'])) {
            $core_competences = implode(",", $request->input('core_category'));
        }
        if ($request->has(['hourly_rate'])) {
            $hourly_rate = implode(",", $request->input('hourly_rate'));
        }
        if ($request->has(['freelancer_roles'])) {
            $freelancer_roles = implode(",", $request->input('freelancer_roles'));
        }
        if ($request->has(['availabile_days'])) {
            $availabile_days = implode(",", $request->input('availabile_days'));
        }
        if ($request->has(['availabile_days_en'])) {
            $availabile_days_en = implode(",", $request->input('availabile_days_en'));
        }
        if ($request->has(['can_travel_to_germany'])) {
            $travelling = implode(",", $request->input('can_travel_to_germany'));
        }
        if ($request->has(['category_skills'])) {
            $category_skills = implode(",", $request->input('category_skills'));
        }
        if ($request->has(['freelancer_source'])) {
            $freelancer_source = implode(",", $request->input('freelancer_source'));
        }
        $hourly_rate_other_input = '';
        if ($request->has(['hourly_rate_other_input'])) {
            $hourly_rate_other_input = $request->input('hourly_rate_other_input');
        }
        $freelancer_roles_other_input = '';
        if ($request->has(['freelancer_roles_other_input'])) {
            $freelancer_roles_other_input = $request->input('freelancer_roles_other_input');
        }

        $user->reference = $request->input('reference');

        if($request->file('attached_cv')) {
            $user->attached_cv = $request->file('attached_cv')->store('cv');
        }

        if($request->file('picture')) {
            $user->picture = $request->file('picture')->store('picture');
        }
        $user->client_name = $request->input('client_name');
        $user->manager_name = $request->input('manager_name');
        $user->reference_mobile = $request->input('reference_mobile');
        $user->info_field = $request->input('info_field');
        $user->hourly_rate = $hourly_rate;
        $user->role_definition = $freelancer_roles;
        $user->availability = $request->input('part_or_full_time');
        $user->availability_date = $availability_date;
        $user->availability_per_week = $availabile_days;
        $user->availability_per_week_en = $availabile_days;
        $user->travelling = $travelling;
        $user->possible_extension = $request->input('possible_extension');
        $user->extension_text = $request->input('extension_text');
        $user->other_interview = $request->input('other_interview');
        $user->comment_area_text = $request->input('comment_area_text');
        $user->source = $freelancer_source;
        $user->category_skills = $category_skills;
        $user->general_notes = $request->input('general_notes');
        $user->core_competences = $core_competences;
        $user->traveling_state = implode(',', $request->get('traveling_state', []));
        $user->traveling_city = implode(',', $request->get('traveling_city', []));
        $user->video = $request->get('video');
        $user->number = $request->get('number');
        $user->is_confirmed = $request->get('is_confirmed');
        $user->is_active = $request->get('is_approved');
        $user->save();
        $client_number = $request->get('number');
        Session::put('variableName', $client_number);
        $client_create = Session::get('variableName');
        return redirect(route('candidates.add_data'));
    }

    public function create_exp()
    {
  
            $client_number = Session::get('variableName');

        
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        return view('kandidate/add_expkandidate', compact('competences', 'permission', 'candidate','client_number'));
    }
    public function add_exp()
    {
        $client_number = Session::get('update_id');
        // var_dump($client_number);exit();
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        return view('kandidate/add_experiences', compact('competences', 'permission', 'candidate','client_number'));
    }
    public function create_education()
    {

        $client_number = Session::get('variableName');
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        return view('kandidate/add_education_kandidate', compact('competences', 'permission', 'candidate','client_number'));
    }
    public function add_education()
    {

        $client_number = Session::get('update_id');
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        return view('kandidate/add_education', compact('competences', 'permission', 'candidate','client_number'));
    }
    public function create_education_save(Request $request)
    {
        $client_number = Session::get('variableName');
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        $topic = $request->input('education_topic');
        $start_month = $request->get('start_month');
        $start_year = $request->get('start_year');
        $check = $request->get('checked');
        // var_dump($check);exit();
        if($check != NULL){
            // $end_month = date('F');
            // $end_year = date('Y');
            $end_month = "present";
            $end_year = "present";
        }else{
            $end_month = $request->get('end_month');
            $end_year = $request->get('end_year');
        }
        $university_name = $request->input('university_name');
        $degrees_list = implode(',', $request->get('education', []));
        $data=array('client_id'=>$client_number,"education_topic"=>$topic,"start_month"=>$start_month,"start_year"=>$start_year,"end_year"=>$end_year,"end_month"=>$end_month,"education_uni"=>$university_name,"education_list"=>$degrees_list);
        DB::table('educations')->insert($data);
        $data = DB::table('educations')
                    ->where('client_id',$client_number)
                    ->get();
        return view('kandidate/add_education_kandidate', compact('competences', 'permission', 'candidate','client_number','data'));
    }
    public function add_education_save(Request $request)
    {
        $client_number = Session::get('update_id');
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        $topic = $request->input('education_topic');
        $start_month = $request->get('start_month');
        $start_year = $request->get('start_year');
        $check = $request->get('checked');
        if($check != NULL){
            // $end_month = date('F');
            // $end_year = date('Y');
            $end_month = "present";
            $end_year = "present";
        }else{
            $end_month = $request->get('end_month');
            $end_year = $request->get('end_year');
        }
        $university_name = $request->input('university_name');
        $degrees_list = implode(',', $request->get('education', []));
        $data=array('client_id'=>$client_number,"education_topic"=>$topic,"start_month"=>$start_month,"start_year"=>$start_year,"end_year"=>$end_year,"end_month"=>$end_month,"education_uni"=>$university_name,"education_list"=>$degrees_list);
        DB::table('educations')->insert($data);
        $data = DB::table('educations')
                    ->where('client_id',$client_number)
                    ->get();
        return redirect(route('candidates.add_education'));
    }
    public function update_exp()
    {

        $client_number = Session::get('update_id');
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        $data = DB::table('experiences')
        ->where('client_id',$client_number)
        ->get();
        return view('kandidate/update_experience_pro', compact('competences', 'permission', 'candidate','client_number', 'data'));
    }
    public function update_education()
    {

        $client_number = Session::get('update_id');
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }

        $candidate = new Kandidate();
        $data = DB::table('educations')
        ->where('client_id',$client_number)
        ->get();
        return view('kandidate/update_education', compact('competences', 'permission', 'candidate','client_number', 'data'));
    }
    public function create_exp_post(Request $request)
    {
        $client_number = Session::get('variableName');
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        $sector = $request->get('work_experience_sector');
        $start_month = $request->get('start_month');
        $start_year = $request->get('start_year');
        $check = $request->get('checked');
        // var_dump($check);exit();
        if($check != NULL){
            // $end_month = date('F');
            // $end_year = date('Y');
            $end_month = "present";
            $end_year = "present";
        }else{
            $end_month = $request->get('end_month');
            $end_year = $request->get('end_year');
        }
        // var_dump($end_month);exit();
        $comp_name = $request->get('work_experience_company_name');
        $position = $request->get('work_experience_position');
        $position_desc = $request->get('work_experience_position_description');
        $data=array('client_id'=>$client_number,"sector"=>$sector,"start_month"=>$start_month,"start_year"=>$start_year,"end_month"=>$end_month,"end_year"=>$end_year,"country_name"=>$comp_name,"position"=>$position,"position_desc"=>$position_desc);
        DB::table('experiences')->insert($data);
        $data = DB::table('experiences')
                    ->where('client_id',$client_number)
                    ->get();
                    // var_dump($data);exit();
        return view('kandidate/add_expkandidate', compact('competences', 'permission', 'candidate','client_number','data'));
    }
    public function create_experience_post(Request $request)
    {
        $client_number = Session::get('update_id');
        // var_dump("test");exit();
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        $sector = $request->get('work_experience_sector');
        $start_month = $request->get('start_month');
        $start_year = $request->get('start_year');
        $check = $request->get('checked');
        if($check != NULL){
            // $end_month = date('F');
            // $end_year = date('Y');
            $end_month = "present";
            $end_year = "present";
        }else{
            $end_month = $request->get('end_month');
            $end_year = $request->get('end_year');
        }
        // var_dump($end_month);exit();
        $comp_name = $request->get('work_experience_company_name');
        $position = $request->get('work_experience_position');
        $position_desc = $request->get('work_experience_position_description');
        $data=array('client_id'=>$client_number,"sector"=>$sector,"start_month"=>$start_month,"start_year"=>$start_year,"end_month"=>$end_month,"end_year"=>$end_year,"country_name"=>$comp_name,"position"=>$position,"position_desc"=>$position_desc);
        DB::table('experiences')->insert($data);
        $data = DB::table('experiences')
                    ->where('client_id',$client_number)
                    ->get();
                    // var_dump($client_number);exit();
        return redirect(route('candidates.add_dataexp'));
    }
    public function update_exp_post(Request $request)
    {
        $client_number = Session::get('update_id');
        $experiences_ids = DB::table('experiences')
        ->select('id')
        ->where('client_id',$client_number)
        ->get();
        $count = DB::table('experiences')
        ->select('id')
        ->where('client_id',$client_number)
        ->count();
        $a = $request->get('end_month19');
        // var_dump($a);exit();
        for($i=0;$i<$count;$i++)
        {
            $temp = $experiences_ids[$i]->id;
            if(isset($temp))
            {
                $sector = $request->get('work_experience_sector'.$temp);
                $start_month = $request->get('start_month'.$temp);
                $start_year = $request->get('start_year'.$temp);
                $check = $request->get('end_month'.$temp);
                if($check != NULL){
                    $end_month = $request->get('end_month'.$temp);
                $end_year = $request->get('end_year'.$temp);
                }else{
                    $end_month = "present";
                    $end_year = "present";
                }

                
                $comp_name = $request->get('work_experience_company_name'.$temp);
                $position = $request->get('work_experience_position'.$temp);
                $position_desc = $request->get('work_experience_position_description'.$temp);
                $data=array('client_id'=>$client_number,"sector"=>$sector,"start_month"=>$start_month,"start_year"=>$start_year,"end_year"=>$end_year,"end_month"=>$end_month,"country_name"=>$comp_name,"position"=>$position,"position_desc"=>$position_desc);
                DB::table('experiences')->where('id',$temp)->update($data);
            }

        }
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        $data = DB::table('experiences')
                    ->where('client_id',$client_number)
                    ->get();      
        return redirect(route('candidates.update_data'));
    }
    public function education_save(Request $request)
    {
        $client_number = Session::get('update_id');
        $experiences_ids = DB::table('educations')
        ->select('id')
        ->where('client_id',$client_number)
        ->get();
        $count = DB::table('educations')
        ->select('id')
        ->where('client_id',$client_number)
        ->count();
        for($i=0;$i<$count;$i++)
        {
            $temp = $experiences_ids[$i]->id;
            if(isset($temp))
            {
                $education_topic = $request->input('education_topic'.$temp);
                $start_month = $request->get('start_month'.$temp);
                $start_year = $request->get('start_year'.$temp);
                $check = $request->get('end_month'.$temp);
                if($check != NULL){
                    $end_month = $request->get('end_month'.$temp);
                    $end_year = $request->get('end_year'.$temp);
                }else{
                    $end_month = "present";
                    $end_year = "present";
                }
                // var_dump($end_month);exit();
                $university_name = $request->input('university_name'.$temp);
                $education = implode(',', $request->get('education'.$temp, []));
                $data=array('client_id'=>$client_number,"education_topic"=>$education_topic,"start_month"=>$start_month,"start_year"=>$start_year,"end_year"=>$end_year,"end_month"=>$end_month,"education_uni"=>$university_name,"education_list"=>$education);
                DB::table('educations')->where('id',$temp)->update($data);
            }

        }
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $candidate = new Kandidate();
        $data = DB::table('educations')
                    ->where('client_id',$client_number)
                    ->get();      
        return redirect(route('candidates.update_data_education'));
    }
    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllFestanstellung(Request $request)
    {
        return Response::json($this->getCandidates($request));
    }

    public function getAllSkills(Request $request)
    {
        $query = $request->input('query');
        if ($query != '') {
            $data = DB::table('competences')
                ->select('competences_skill.skill', 'competences_skill.competences_id')
                ->join('competences_skill', 'competences_skill.competences_id', '=', 'competences.id')
                ->where('competences_skill.skill', 'like', $request->input('query') . '%')
                ->orderBy('competences_skill.skill', 'asc')
                ->get();
            return Response::json($data);
        }
        $data = DB::table('competences')
            ->select('competences_skill.skill', 'competences_skill.competences_id')
            ->join('competences_skill', 'competences_skill.competences_id', '=', 'competences.id')
            ->orderBy('competences_skill.skill', 'asc')
            ->get();
        return Response::json($data);
    }

    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function view()
    {
        $users = new user;
        $users = DB::table('users')
            ->where('user_role', '0')
            ->orderBy('id', 'asc')
            ->get();
        return view('freelancer.view', ['users' => $users]);
    }

    /**
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function editKandidate($id)
    {
        $emp_id = SESSION::get('id');
        $permission = [];
        $competences = $this->getCompetencesData();
        $candidate = Kandidate::findOrFail($id);
        if ($emp_id != 1) {
            $permission = DB::table('emp_permission')->where('emp_id', $emp_id)->first();
        }
        $previous_count = 0;
        $next_count = 0;
        // var_dump("test");exit();
        return view('kandidate.editkandidate', compact(
            'previous_count',
            'next_count',
            'candidate',
            'competences',
            'permission'
        ));
    }

    /**
     * @param $id
     * @return false|string
     */
    public function delete($id)
    {
        $status = "success";
        try {
            $users = DB::table('kandidates')->where('id', $id)->delete();
            $result = "Festanstellung Data Deleted Successfully";
        } catch (QueryException $ex) {
            $status = "error";
            $result = $ex->getMessage();
        }
        return (json_encode(array('status' => $status, 'message' => $result)));
    }
    public function experience_sub_delete($id)
    {   
        // var_dump($id);exit();
       
            $users = DB::table('experiences')->where('id', $id)->delete();
           
        return redirect(route('candidates.update_data'));
    }
    public function education_sub_delete($id)
    {   
        // var_dump($id);exit();
       
            $users = DB::table('educations')->where('id', $id)->delete();
           
        return redirect(route('candidates.update_data_education'));
    }
    /**
     * @return array
     */
    public function getCompetencesData()
    {
        $competences = DB::table('competences')->get()->toArray();
        if (!empty($competences)) {
            $competences_array = array();
            $i = 1;
            foreach ($competences as $competences_val) {
                $competences_skill = DB::table('competences_skill')->Where('competences_id',
                    $competences_val->id)->get()->toArray();
                $competences_val->keys = $i;
                $competences_val->competences_skill = $competences_skill;
                $competences_array[] = $competences_val;
                $i++;
            }
            return $competences_array;
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'number' => 'required|unique:kandidates,number,'.$id
        ]);
        
        $user = Kandidate::findOrFail($id);
        $user->title = '';

        $user->education = implode(',', $request->get('education', []));
        $user->work_experience_sector = $request->get('work_experience_sector');
        $user->work_experience_period = $request->get('work_experience_period');
        $user->work_experience_company_name = $request->get('work_experience_company_name');
        $user->work_experience_position = $request->get('work_experience_position');
        $user->work_experience_position_description = $request->get('work_experience_position_description');
        $user->first_name = $request->get('first_name');
        $user->optional_interview = $request->get('optional_interview');
        $user->last_name = '';
        $user->Mobile = '';
        $user->home_number = '';
        $user->password = bcrypt(time());
        $availability_date = '';
        $category_skills = '';
        $travelling = '';
        $hourly_rate = '';
        $freelancer_roles = '';
        $availabile_days = '';
        $availabile_days_en = '';
        $core_competences = '';
        $freelancer_source = '';
        if ($request->has(['core_category'])) {
            $core_competences = implode(",", $request->input('core_category'));
        }
        if ($request->has(['hourly_rate'])) {
            $hourly_rate = implode(",", $request->input('hourly_rate'));
        }
        if ($request->has(['freelancer_roles'])) {
            $freelancer_roles = implode(",", $request->input('freelancer_roles'));
        }
        if ($request->has(['availabile_days'])) {
            $availabile_days = implode(",", $request->input('availabile_days'));
        }
        if ($request->has(['availabile_days_en'])) {
            $availabile_days_en = implode(",", $request->input('availabile_days_en'));
        }
        if ($request->has(['can_travel_to_germany'])) {
            $travelling = implode(",", $request->input('can_travel_to_germany'));
        }
        if ($request->has(['category_skills'])) {
            $category_skills = implode(",", $request->input('category_skills'));
        }
        if ($request->has(['freelancer_source'])) {
            $freelancer_source = implode(",", $request->input('freelancer_source'));
        }
        $hourly_rate_other_input = '';
        if ($request->has(['hourly_rate_other_input'])) {
            $hourly_rate_other_input = $request->input('hourly_rate_other_input');
        }
        $freelancer_roles_other_input = '';
        if ($request->has(['freelancer_roles_other_input'])) {
            $freelancer_roles_other_input = $request->input('freelancer_roles_other_input');
        }

        $user->reference = $request->input('reference');
        if($request->file('attached_cv')) {
            \File::delete($user->attached_cv);
            $user->attached_cv = $request->file('attached_cv')->store('cv');
        }
        if($request->file('picture')) {
            \File::delete($user->picture);
            $user->picture = $request->file('picture')->store('picture');
        }
        $user->client_name = $request->input('client_name');
        $user->manager_name = $request->input('manager_name');
        $user->reference_mobile = $request->input('reference_mobile');
        $user->info_field = $request->input('info_field');
        $user->hourly_rate = $hourly_rate;
        $user->role_definition = $freelancer_roles;
        $user->availability = $request->input('part_or_full_time');
        $user->availability_date = $availability_date;
        $user->availability_per_week = $availabile_days;
        $user->availability_per_week_en = $availabile_days_en;
        $user->travelling = $travelling;
        $user->possible_extension = $request->input('possible_extension');
        $user->extension_text = $request->input('extension_text');
        $user->other_interview = $request->input('other_interview');
        $user->comment_area_text = $request->input('comment_area_text');
        $user->source = $freelancer_source;
        $user->category_skills = $category_skills;
        $user->general_notes = $request->input('general_notes');
        $user->core_competences = $core_competences;
        $user->traveling_state = implode(',', $request->get('traveling_state', []));
        $user->traveling_city = implode(',', $request->get('traveling_city', []));
        $user->video = $request->get('video');
        $user->number = $request->get('number');
        $user->is_confirmed = $request->get('is_confirmed');
        $user->is_active = $request->get('is_approved');
        $user->save();
        $f = $request->get('numberf');
        $l = $request->get('number');  
        DB::table('experiences')
                ->where('client_id', $f)
                ->update(['client_id' => $l]);
        DB::table('educations')
                ->where('client_id', $f)
                ->update(['client_id' => $l]);
        // var_dump($f);exit();


        $client_number = $request->get('number');
        Session::put('update_id', $client_number);
        $client_create = Session::get('update_id');
        return redirect(route('candidates.update_data'));
    }

    /**
     * @param $id
     * @param $list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editFestanstellung($id, $list)
    {
        $permission = [];
        $competences = $this->getCompetencesData();

        $candidate = Kandidate::findOrFail($id);

        $next = DB::table('kandidates')
            ->select('kandidates.id', 'first_name', 'last_name')
            ->where('kandidates.id', '>', $id)->first();
        $next_count = DB::table('kandidates')
            ->select('id')
            ->where('id', '>', $id)->count();

        $previous = DB::table('kandidates')
            ->select('kandidates.id', 'first_name', 'last_name')
            ->orderBy('kandidates.id', 'desc')
            ->where('kandidates.id', '<', $id)->first();

        $previous_count = DB::table('kandidates')
            ->select('id')
            ->where('id', '<', $id)->count();

        if (!currentUser()->isAdmin) {
            $permission = currentUser()->employeePermission;
        }
        return view('kandidate.editkandidate', [
            'candidate' => $candidate,
            'competences' => $competences,
            'permission' => $permission,
            'next' => $next,
            'next_count' => $next_count,
            'previous' => $previous,
            'previous_count' => $previous_count,
            'list' => $list
        ]);
    }

    public function openMailPanel(Request $request)
    {

        $id = explode(',', $request->festanstellung_id);

        $festanstellung = $request->festanstellung_id;
        $festanstellung_data = Kandidate::where('id', $festanstellung)->first();
        $title_for_display = "Hallo " . $festanstellung_data->title . ' ' . $festanstellung_data->last_name;
        $title = $title_for_display;
        $users_data = DB::table('kandidates')->select('email', 'first_name', 'last_name')->whereIn('id', $id)->get();
        return view('kandidate.send_mail_panel',
            ['users_data' => $users_data, 'title' => $title, 'festanstellung' => $festanstellung]);
    }

    public function sendMail(Request $request)
    {


        $status = 'success';
        $value = explode(',', $request->freelancer_id);
        $body = $request->freelancer_mail_body;
        $users = DB::table('kandidates')->select('id', 'email', 'first_name', 'last_name',
            DB::Raw(" IFNULL( `title`, '' ) as u_title"))->whereIn('id', $value)->get();
        $data = array();
        $subject = $request->freelancer_mail_sub;
        foreach ($users as $value) {

            $body = str_replace("((Name))", $value->u_title . ' ' . $value->first_name . ' ' . $value->last_name . ', ',
                $body);

            $data['email'] = $value->email;
            $data['subject'] = $subject;

            Mail::send('mail.mail_template', ['data' => $data, 'content' => $body], function ($message) use ($data) {
                $message->from('avinashmishra.vll@gmail.com', 'Argon Strategy');

                $message->to($data['email']);

                $message->subject($data['subject']);
            });
        }
        return (json_encode(array('status' => $status, 'message' => 'Email Send Successfully')));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function passwordChange(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
            'new_password' => [
                'required',
                'min:12',
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
                'confirmed'
            ]
        ]);

        if(Hash::check($request->get('password'), Auth::user()->password)){
            currentUser()->password = Hash::make($request->get('new_password'));
            currentUser()->save();

            return response()->json([
                'success' => true
            ]);
        }

        return response(['password' => ['Old password does not match'],], \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function activate(Request $request, $id)
    {
        $candidate = Kandidate::findOrFail($id);

        $candidate->is_active = 1;
        $candidate->save();

        $request->session()->flash('status', 'Kandidate was activated');
        return redirect(route('candidates.index'));
    }

    public function deactivate(Request $request, $id)
    {
        $candidate = Kandidate::findOrFail($id);

        $candidate->is_active = 0;
        $candidate->save();

        $request->session()->flash('status', 'Kandidate was deactivated');
        return redirect(route('candidates.index'));
    }

    public function invite()
    {
        if (!Auth::user()->isAdmin) {
            abort(404);
        }
        return view('kandidate.invite');
    }

    public function sendInvite(Request $request)
    {
        if (!Auth::user()->isAdmin) {
            abort(404);
        }

        $this->validate($request, [
            'email' => 'required|email|unique:candidates_invites,email'
        ]);

        $invite = new CandidateInvite();
        $invite->email = $request->get('email');
        $invite->invitation_code = Str::random(35);
        $invite->created_at = Carbon::now();
        $invite->sent_at = Carbon::now();
        $invite->save();

        $inviteLink = route('candidate.public_form', $invite->invitation_code);

        Mail::send('mail.invite', compact('inviteLink'), function ($message) use($invite) {
            $message->to($invite->email);
            $message->subject('Invitation to join kandidaten.org');
        });

        $request->session()->flash('status', 'Successfully Invited');
        return redirect()->to(route('candidates.index'));
    }
}