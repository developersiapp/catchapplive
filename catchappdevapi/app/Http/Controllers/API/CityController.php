<?php


namespace catchapp\Http\Controllers\API;


use catchapp\Http\Controllers\Controller;
use catchapp\Models\City;
use Illuminate\Http\Request;
use catchapp\Models\Club;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CityController extends Controller
{
    public function citiesList()
    {
        $response = [];
        try {
            $data=[];
            $cities = City::query()->select('id','name')->get();
            foreach ($cities as $city) {
                $Clubs = Club::query()->where('city','=', $city->id)->count();
                if ($Clubs >0)
                {
                    $data[]= $city;
                }
            }
            DB::commit();
            $response['error'] = false;
            $response['status_code'] = '200';
            $response['message'] = 'List of cities.';
            $response['data'] = $data;
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    public function topCities()
    {

        $response = [];
        try {
            $data=[];
            $cities = City::query()->select('id','name')->get();
            foreach ($cities as $city) {
                $counts = Club::query()->where('city','=', $city->id)->count();
                if ($counts >0)
                {
                    $city['count'] = $counts;
                    $data[]= $city;
                }
            }
            usort($data, function ($a, $b) {

                if ($a['count'] == $b['count']) {
                    return 0;
                }
                return ($a['count'] > $b['count']) ? -1 : 1;
            });

            $newArray = array_slice($data, 0, 6, true);
            DB::commit();
            $response['error'] = false;
            $response['status_code'] = '200';
            $response['message'] = 'Top cities.';
            $response['data'] = $newArray;
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    public function searchCity(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'text' => 'required',
            ]);
            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            $value=$request->text;
            $data=[];
            $cities = City::query()
//                ->where('name','LIKE','%'.$value.'%')
                ->select('id','name')->get()->toArray();

            foreach ($cities as $city) {
                $counts = Club::query()->where('city','=', $city['id'])->count();
                if (($counts>0) && (strpos(strtoupper($city['name']), strtoupper($request->input('text')))  !== false))
                {
                    $city['count'] = $counts;
                    $data[] = $city;
                }
            }

            DB::commit();
            $response['error'] = false;
            $response['status_code'] = '200';
            $response['message'] = 'Searched Cities';
            $response['data'] = $data;
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

}