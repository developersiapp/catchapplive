<?php
namespace catchapp\Http\Controllers\Backend;
use Carbon\Carbon;
use catchapp\Helpers\MediaHelper;
use catchapp\Http\Controllers\Controller;
use catchapp\Models\Club;
use catchapp\Models\DJ;
use catchapp\Models\User;
use ConsoleTVs\Charts\Facades\Charts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 4/6/19
 * Time: 6:29 PM
 */
class DashboardController extends Controller
{
    public function index()
    {
//MediaHelper::generateThumbnail();



        $clubQuery = Club::query();
        $C1Month= $clubQuery->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $C6Month= $clubQuery
            ->where("created_at",">", Carbon::now()->subMonths(6))
            ->count();
        $CYear= $clubQuery->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $data[]= array(
            'label' =>'Last 1 Month',
            'value' => $C1Month
        );
        $data[]= array(
            'label' =>'Last 6 Month',
            'value' => $C6Month
        );
        $data[]= array(
            'label' =>'One Year',
            'value' => $CYear
        );


        $range = \Carbon\Carbon::now()->subDays(30);

        $stats = Club::where('created_at', '>=', $range)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as value')
            ])
            ->toJSON();



        $chart = Charts::create('donut', 'highcharts')
            ->title('Clubs')
            ->labels(['Last 1 Month', 'Last 6 Months', 'This Year'])
//            ->values([$C1Month,$C6Month,$CYear])
            ->values([5,10,20])

            ->dimensions(600,300)
            ->responsive(true);
        return view('backend.dashboard.index',['stats'=> $stats, 'chart' =>$chart,'data'=> \GuzzleHttp\json_encode($data)]);
    }

    public function getData()
    {

        $clubQuery = Club::query();
        $clubQuery2 =clone $clubQuery;
        $clubQuery3 = clone  $clubQuery2;

        $C1Month= $clubQuery->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $C6Month= $clubQuery2
            ->where("created_at",">", Carbon::now()->subMonths(6))
            ->count();
        $CYear= $clubQuery3->whereYear('created_at', Carbon::now()->year)
            ->where("created_at",">", Carbon::now()->subMonths(12))
            ->count();
        $data=[];
        $club=[];

        $C1 = ['label' =>'This Month',
            'value' => $C1Month];
        $C2 = [
            'label' =>'Last 6 Month',
            'value' => $C6Month];

        $C3 = ['label' =>'One Year',
            'value' => $CYear];
        array_push($club, $C1, $C2, $C3);
        array_push($data, $club);


//        USERS
        $userQuery = User::query();
        $userQuery2 =clone $userQuery;
        $userQuery3 = clone  $userQuery2;

        $U1Month= $userQuery->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $U6Month= $userQuery2
            ->where("created_at",">", Carbon::now()->subMonths(6))
            ->count();
        $UYear= $userQuery3->whereYear('created_at', Carbon::now()->year)
            ->where("created_at",">", Carbon::now()->subMonths(12))
            ->count();
        $user=[];

        $U1 = ['label' =>'This Month',
            'value' => $U1Month];
        $U2 = [
            'label' =>'Last 6 Month',
            'value' => $U6Month];

        $U3 = ['label' =>'One Year',
            'value' => $UYear];
        array_push($user, $U1, $U2, $U3);
        array_push($data, $user);

//        DJ
        $djQuery = DJ::query();
        $djQuery2 =clone $djQuery;
        $djQuery3 = clone  $djQuery2;

        $D1Month= $djQuery->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $D6Month= $djQuery2
            ->where("created_at",">", Carbon::now()->subMonths(6))
            ->count();
        $DYear= $djQuery3->whereYear('created_at', Carbon::now()->year)
            ->where("created_at",">", Carbon::now()->subMonths(12))
            ->count();
        $dj=[];

        $D1 = ['label' =>'This Month',
            'value' => $D1Month];
        $D2 = [
            'label' =>'Last 6 Month',
            'value' => $D6Month];

        $D3 = ['label' =>'One Year',
            'value' => $DYear];
        array_push($dj, $D1, $D2, $D3);
        array_push($data, $dj);

        //USERS ONLINE

        $query = User::query();
        $queryO = clone $query;
        $todayDate= Carbon::now();
        $query
//            ->where(function ($subQuery) use ($todayDate) {
//            $startDate = Carbon::now()->copy()->startOfDay();
//            $subQuery->where('updated_at', '>=', $startDate->format(Carbon::DEFAULT_TO_STRING_FORMAT));
//         })
            ->where('is_active','=',1);
        $online = $query->count();
        $offline= ($queryO->count() - $online);
        $OLU = ['label' =>'Online Users',
            'value' => $online];
        $OFU = [
            'label' =>'Offline Users',
            'value' => $offline];
        $user=[];
        array_push($user, $OLU, $OFU);
        array_push($data, $user);


        return response()->json(['data' => $data]);
    }
    public function getFilteredData(Request $request)
    {
        $date1 = $request->input('date');
        $f_date = date("Y-m-d H:i:s", strtotime($date1));
        $user_count = User::query()
            ->whereDate('created_at','<=',$f_date)
            ->count();
        $club_count = Club::query()
            ->whereDate('created_at','<=',$f_date)
            ->count();
        $dj_count = DJ::query()
            ->whereDate('created_at','<=',$f_date)
            ->count();
        $data=[];
        array_push($data, $user_count, $club_count, $dj_count, $f_date, $date1);


        return response()->json(['data' => $data]);
    }

}
