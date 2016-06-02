<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests\NhanvienRequest;
use App\Http\Controllers\Controller;
use App\Model\Report\Branch;
use App\Model\Report\Branch_group;
//use App\Model\Report\Employee;
use App\Model\Report\Services;
use App\ModelTime\Service;
use Khill\Lavacharts\Lavacharts;
use App\TransactionsTime as TransactionData;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ModelTime\Employee;
use App\Http\FormRequests\VoteAverageRequest;
use App\Http\Requests\SVRequestStore;
use App\Http\Requests\SVRequestCreate;
use \App\Model\SinhVien;
use Illuminate\Support\Facades\Route;
use Breadcrumbs;

class svController extends Controller {

    private $m = "post";
    private $numberinpage = 10;
    private $classlist;
    private $dantoclist;
    private $tongiaolist;
    private $mannghiepchalist;
    private $mannghiepmelist;
    private $giotinhlist;
    private $arrBreadScrumbs = array();

    public function __construct(Request $request) {
        $this->classlist = DB::table('lop')->select('malop as id', 'tenlop as name')->get();
        $this->dantoclist = DB::table('dantoc')->select('madantoc as id', 'tendantoc as name')->get();
        $this->tongiaolist = DB::table('tongiao')->select('matongiao as id', 'tentongiao as name')->get();
        $this->mannghiepchalist = DB::table('nghenghiep')->select('manghe as id', 'tennghe as name')->get();
        $this->mannghiepmelist = DB::table('nghenghiep')->select('manghe as id', 'tennghe as name')->get();
        $this->gioitinhlist = [['id' => 0, 'name' => 'Nam'], ['id' => 1, 'name' => 'Nữ']];
    }

    function getBreadcrumb($name) {
        $item = \App\RoutesMenu::getItem($name);
        
        if (isset($item->parent_name)) {
            print($item->parent_name);
            $this->arrBreadScrumbs[] = $this->getBreadcrumb($item->parent_name);
        }
        return $item;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Responses
     */
    public function index(Request $req) {
        $class = $req->input('class', '');
        $dantoc = $req->input('dantoc', '');
        $tongiao = $req->input('tongiao', '');
        $hoten = $req->input('hoten', ''); 
        $gioitinh = $req->input('gioitinh', '');
        $ngaysinh = $req->input('ngaysinh', '');
        $nameroute = Route::getCurrentRoute()->getName();
        $this->arrBreadScrumbs[] = $this->getBreadcrumb($nameroute);
        if ($req->isMethod($this->m)) {
            $svlist = SinhVien::svlist($this->numberinpage, $class, $dantoc, $tongiao, $hoten, $gioitinh, $ngaysinh);
        } else {
            $svlist = SinhVien::paginate($this->numberinpage);
        }
        $page = $req->input('page', 1);
        $startrecord = ($page - 1) * $this->numberinpage;
        return view('sv.sv_index')
                        ->with('startrecord', $startrecord)
//                        ->with('breadscrumbs', $breadscrumbs2)
                        ->with('page', $page)
                        ->with('ngaysinh', $ngaysinh)
                        ->with('svlist', $svlist)
                        ->with('class', $class)
                        ->with('dantoc', $dantoc)
                        ->with('tongiao', $tongiao)
                        ->with('hoten', $hoten)
                        ->with('gioitinh', $gioitinh)
                        ->with('classlist', $this->classlist)
                        ->with('dantoclist', $this->dantoclist)
                        ->with('tongiaolist', $this->tongiaolist)
                        ->with('mannghiepchalist', $this->mannghiepchalist)
                        ->with('mannghiepmelist', $this->mannghiepmelist)
                        ->with('gioitinhlist', $this->gioitinhlist)
        ;
    }

    public function edit($masv) {
        $data = SinhVien::where("MaSINHVIEN", "$masv")->first();
        $hoten = old('hoten') ? old('hoten') : $data->HoTen;
        $class = old("class") ? old("class") : $data->Malop;
        $gioitinh = old('gioitin') ? old('gioitin') : $data->GioiTinh;
        $ngaysinh = old("ngaysinh") ? old("ngaysinh") : $data->NgaySinh;
        $noisinh = old("noisinh") ? old("noisinh") : $data->NoiSinh;
        $dantoc = old("dantoc") ? old("dantoc") : $data->MaDanToc;
        $tongiao = old("tongiao") ? old("tongiao") : $data->MaTonGiao;
        $hotencha = old("hotencha") ? old("hotencha") : $data->HoTenCha;

        $mannghiepcha = old("mannghiepcha") ? old("mannghiepcha") : $data->MaNNghiepCha;
        $hotenme = old("hotenme") ? old("hotenme") : $data->HoTenMe;
        $mannghiepme = old("mannghiepme") ? old("mannghiepme") : $data->MaNNghiepMe;
        $dt = old("dt") ? old("dt") : $data->DienThoai;
        return view('sv.sv_add')
                        ->with('classlist', $this->classlist)
                        ->with('dantoclist', $this->dantoclist)
                        ->with('tongiaolist', $this->tongiaolist)
                        ->with('mannghiepchalist', $this->mannghiepchalist)
                        ->with('mannghiepmelist', $this->mannghiepmelist)
                        ->with('gioitinhlist', $this->gioitinhlist)
                        ->with('hoten', $hoten)
                        ->with('class', $class)
                        ->with('gioitinh', $gioitinh)
                        ->with('ngaysinh', $ngaysinh)
                        ->with('noisinh', $noisinh)
                        ->with('dantoc', $dantoc)
                        ->with('tongiao', $tongiao)
                        ->with('hotencha', $hotencha)
                        ->with('mannghiepcha', $mannghiepcha)
                        ->with('hotenme', $hotenme)
                        ->with('mannghiepme', $mannghiepme)
                        ->with('dt', $dt)
                        ->with('action', 'edit')

        ;
    }

    public function create() {
        $hoten = old('hoten');
        $class = old("class");
        $gioitinh = old('gioitin');
        $ngaysinh = old("ngaysinh");
        $noisinh = old("noisinh");
        $dantoc = old("dantoc");
        $tongiao = old("tongiao");
        $hotencha = old("hotencha");

        $mannghiepcha = old("mannghiepcha");
        $hotenme = old("hotenme");
        $mannghiepme = old("mannghiepme");
        $dt = old("dt");
        return view('sv.sv_add')
                        ->with('classlist', $this->classlist)
                        ->with('dantoclist', $this->dantoclist)
                        ->with('tongiaolist', $this->tongiaolist)
                        ->with('mannghiepchalist', $this->mannghiepchalist)
                        ->with('mannghiepmelist', $this->mannghiepmelist)
                        ->with('gioitinhlist', $this->gioitinhlist)
                        ->with('hoten', $hoten)
                        ->with('class', $class)
                        ->with('gioitinh', $gioitinh)
                        ->with('ngaysinh', $ngaysinh)
                        ->with('noisinh', $noisinh)
                        ->with('dantoc', $dantoc)
                        ->with('tongiao', $tongiao)
                        ->with('hotencha', $hotencha)
                        ->with('mannghiepcha', $mannghiepcha)
                        ->with('hotenme', $hotenme)
                        ->with('mannghiepme', $mannghiepme)
                        ->with('dt', $dt)
                        ->with('action', 'add')

        ;
    }

    public function store(SVRequestCreate $req) {
//    public function store(Request $req) {

        $maSINHVIEN = $req->input("maSINHVIEN", '');
        $hoten = $req->input("hoten");
        $action = $req->input("action");
        $class = $req->input("class");
        $gioitinh = $req->input("gioitinh");
        $ngaysinh = $req->input("ngaysinh");
        $noisinh = $req->input("noisinh");
        $dantoc = $req->input("dantoc");
        $tongiao = $req->input("tongiao");
        $hotencha = $req->input("hotencha");
        $mannghiepcha = $req->input("mannghiepcha");
        $hotenme = $req->input("hotenme");
        $mannghiepme = $req->input("mannghiepme");
        $dt = $req->input("dt");

        if ($action == "edit") {
            //update
            SinhVien::where('MaSINHVIEN', $maSINHVIEN)
                    ->update([
                        "hoten" => $hoten,
                        "Malop" => $class,
                        "gioitinh" => $gioitinh,
                        "ngaysinh" => $ngaysinh,
                        "noisinh" => $noisinh,
                        "Madantoc" => $dantoc,
                        "Matongiao" => $tongiao,
                        "hotencha" => $hotencha,
                        "mannghiepcha" => $mannghiepcha,
                        "hotenme" => $hotenme,
                        "mannghiepme" => $mannghiepme,
                        "dienthoai" => $dt,
            ]);
        } else {
            SinhVien::insert([
                "hoten" => $hoten,
                "Malop" => $class,
                "gioitinh" => $gioitinh,
                "ngaysinh" => $ngaysinh,
                "noisinh" => $noisinh,
                "Madantoc" => $dantoc,
                "Matongiao" => $tongiao,
                "hotencha" => $hotencha,
                "mannghiepcha" => $mannghiepcha,
                "hotenme" => $hotenme,
                "mannghiepme" => $mannghiepme,
                "dienthoai" => $dt,
            ]);
        }
        $req->session()->flash('alert-success', "Thực hiện thành công");
        return redirect(route('svform'));
    }

    public function delete($masv) {
        SinhVien::where("MaSINHVIEN", $masv)
                ->delete();
        return redirect(route('svform'));
    }

//    public function get(Request $request) {
//        $list_appid = "";
//        $command = $_GET['command'];
//        $input = $request->all();
//        $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
//        switch ($command) {
//            case 'branch_group' :
//                $list_appid = DB::table('qms_branch_group')->where('region_id', $input['partner_id'])->get();
//                break;
//            case 'branch' :
//                $list_appid = DB::table('qms_branch')->where('branch_group_id', $partner_id)->get();
//                break;
//            case 'employee' :
//                $list_appid = Employee::ls_employee($partner_id);
//                break;
//            case 'service' :
//                if ($partner_id) {
//                    $list_appid = Service::ls_services();
//                }
//                break;
//        }
//        echo json_encode($list_appid);
//    }
}
