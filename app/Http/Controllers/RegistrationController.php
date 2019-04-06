<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use Calendar;
use App\Registration;
use Auth;
use DateTime;
use DatePeriod;
use DateInterval;
class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }
    /**
     * Вывод страницы со списком врачей
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $users = User::all();
        return view('registration.index', compact('users'));
    }
    /**
     * Страница регистрации к врачу
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function registerTo(Request $request){
        $user = User::where('slug','=', $request->slug)->get();
        $userName = $user[0]->name;
        $calendar = $this->getCalendar();
        $doctor_id = $user[0]->id;

        return view('registration.register', compact('userName', 'calendar','doctor_id', 'user'));
    }

    /**
     * Ajax функция для выбора даты и времени
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerTime(Request $request){
        $selectedDate  = $request['date'];
        $url = $request['url'];
        $parsedUrl = parse_url($url);
        $explodeParsedUrl = explode('/',$parsedUrl['path']);
        $doctor = User::where('slug','=',$explodeParsedUrl[3])->get();

        $splittedTime = $this->splitTime($doctor,$selectedDate);

        return response()->json(array('success' => 'true', 'splittedTime' => $splittedTime));
    }
    /**
     * Функция делит время на промежутки
     *
     * @param $doctor
     * @param $selectedDate
     * @return array
     */
    private function splitTime($doctor, $selectedDate){
        $startTime = strtotime($selectedDate . ' ' . $doctor[0]['doctors']['start_time']); //Начало и конец смены врача
        $endTime = strtotime($selectedDate . ' ' . $doctor[0]['doctors']['end_time']);
        $interval = "25";
        $time=$startTime;
        while ($time < $endTime) {
            $array[] =  date('H:i', $time);
            $time = strtotime('+'.$interval.' minutes', $time);
        }

        return $array;
    }

    private function getCalendar(){
        $events = [];
        $data = Registration::all();
        if($data->count()) {
            foreach ($data as $key => $value) {
                $events[] = Calendar::event(
                    $value->title,
                    false,
                    new \DateTime($value->start_date),
                    new \DateTime($value->end_date.' +25 minutes'),
                    null,
                    [
                        'color' => '#f05050',
                    ]
                );
            }
        }
        $calendar = Calendar::addEvents($events);

        return $calendar;
    }
    /**
     * Регистрация записи к врачу
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addRegistrationEvent(Request $request){
        $insertedDateTime = date( 'Y-m-d H:i:s',
            strtotime($request['start_date'] . $request['booking-time']));
        $user = Auth::user();
        $request['title'] = $user->name;
        $request['start_date'] = $insertedDateTime;
        $request['end_date'] = $insertedDateTime;
        $user->registrations()->create($request->all());

        return redirect('/doctors/list');
    }
}
