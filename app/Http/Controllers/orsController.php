<?php
/*用于保存前端传来的数据*/
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\saveInfo;
use App\Destory;
use DB;
class orsController extends Controller
{
    public function objectToArray($e)
    {
        $e = (array)$e;
        foreach ($e as $k => $v)
        {
            if(gettype($v) == 'resource')
                return;
            if((gettype($v) == 'object') || gettype($v) == 'array')
            {
                $e[$k] = (array($this->objectToArray($v)));
            }
            return $e;
        }
    }

    public function store(Request $request)  //预约
    {
        $data = $request->all();
        $name = $data['name'];
        $department = $data['direction'];
        $telephone =$data['telephone'];
        $title =$data['title'];
        $identity = $data['mark'];
        $place = $data['location'];
        $date = $data['date'];
        $in_begin = strtotime($date." ".$data['start']);  //2019-09-29 15:30 timestamp
        $in_end = strtotime($date." ".$data['end']);

        $startTime = DB::table('reservation')->select('begin')
            ->whereRaw('date=? and place=?',[$date,$place])
            ->get()->toArray();
     // var_dump($startTime);
        $endTime = DB::table('reservation')->select('end')
            ->whereRaw('date=? and place=?',[$date,$place])
            ->get()->toArray();

        $m=count($startTime);
        global $flag;
        $flag = 1;
        for($i=0;$i<$m;$i++)
        {
            //strtotime($date." ".$data['start']);
            $start = strtotime($date." ".$startTime[$i]->begin);
            $end =  strtotime($date." ".$endTime[$i]->end);

   //         if(!((in1 > start[i] && in2 > end[i] && in1 > end[i]) || (in1 < start[i] && in2 < end[i] && in2 < start[i])))
            if(!(($in_begin > $start && $in_end > $end && $in_begin > $end) || ($in_begin < $start && $in_end < $end && $in_end < $start)))
            {
                $flag = 0;
            }

        }

      //  echo $flag;
      //  dd($flag);
        date_default_timezone_set("Asia/Shanghai");
        // $time = strtotime(date("Y-m-d H:i:s"));  //string 当前时间
        $time = strtotime("now");
        $time1 = strtotime($date." ".$data['start']);   //前端传进来的开始预约时间

            if($flag==0)
            {
                 return response()->json(['status'=>102,'msg'=>'Fail,reserve at the same time and place']);
            }
            if($in_end<$in_begin)
            {
                return response()->json(['status'=>106,'msg'=>'Fail,end time must longer than the start time！']);
            }
            if($time>$time1)
            {
                return response()->json(['status'=>105,'msg'=>'Fail,can not reserve in the past time']);
            }
            else
            {
                          $store = saveInfo::create(         //批量赋值
                        [
                            //'id' => $data['id'],
                            'name' => $data['name'],
                            'department' => $data['direction'],
                            'telephone' => $data['telephone'],
                            'title'=>$data['title'],
                            'identity' => $data['mark'],
                            'place' => $data['location'],
                            'begin' =>$data['start'],
                            'end' => $data['end'],
                            'date' => $date,
                        ]
                    );
                    if($store->save()) {
                        return response()->json(['status' => 200, 'msg' => 'success']);
                    }
            }
    }
    public function show(Request $request)
    {
        $data = DB::select('select id,name,title,identity,place,begin,end,date from reservation ORDER BY date desc');
        dd($data);
        if($data)
            return response()->json($data);
    }
    public function destroy(Request $request)
    {
        $sql = saveInfo::where('id','=',Input::get('id'))->exists();
        $sql1 = saveInfo::where('identity','=',Input::get('mark'))->exists();
        if($sql)
        {
            if($sql1)
            {
                DB::table('reservation')->where('id',Input::get('id'))->delete();
                return response()->json(['status'=>201,'msg'=>'删除成功！']);
            }
            else
            {
                return response()->json(['status'=>103,'msg'=>'标识符不正确,删除失败！']);
            }
        }
        else
        {
            return response()->json(['status'=>103,'msg'=>'删除失败！']);
        }
    }

}