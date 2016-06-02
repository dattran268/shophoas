<?php

namespace App\Http\Controllers;

use DB;
use App\Model\Chung;
use App\Model\Category\Schooll;
use App\Model\Category\Region;
use App\Model\Category\Thanhpho;
use App\Model\Category\Quanhuyen;
use App\Model\Category\Khoa;
use App\Model\Category\Hedaotao;
use App\Model\Category\Monhoc;
use App\Model\Category\Hocvi;
use App\Model\Category\Loaigiangvien;
use App\Model\Category\Giangvien;
use App\Model\Category\Khoahoc;
use App\Model\Category\Giangduong;
use App\Model\Category\Tiethoc;
use App\Model\Category\Hocky;
use App\Model\Category\Lop;
use App\Model\Thoikhoabieu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AssignedRequest;

class AssignedController extends Controller {

    private $numrecord = 10;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $theo_truong = array("MaKhoaHoc" => "Khóa học",
            "MaLop" => "Lớp học",
            "MaMonHoc" => "Môn học",
            "MaGIANGVIEN" => "Giảng viên",
            "MaGiangDuong" => "Giảng đường",
            "BatDau" => "Thời gian bắt đầu môn",
            "KetThuc" => "Thời gian kết thúc môn",
            "MaTiet" => "Tiết học",
            "MaHocKy" => "Học kỳ"
        );
        $thutu = array("DESC" => "Giảm dần", "ASC" => "Tăng dần",);
        if (!isset($_GET['vung']) && !isset($_GET['city']) && !isset($_GET['huyen']) && !isset($_GET['school']) && !isset($_GET['khoa']) && !isset($_GET['hedaotao']) && !isset($_GET['monhoc']) && !isset($_GET['khoahoc']) && !isset($_GET['giangvien']) && !isset($_GET['giangduong']) && !isset($_GET['dateform']) && !isset($_GET['dateto']) && !isset($_GET['tiethoc']) && !isset($_GET['hocky']) && !isset($_GET['allow']) && !isset($_GET['tt'])) {
            $option_vung = Region::option_vung();
            $option_city = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_huyen = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_school = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_khoa = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_hedaotao = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_monhoc = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_giangvien = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_khoahoc = Khoahoc::option_khoahoc();
            $option_giangduong = Giangduong::option_giangduong();
            $option_tiethoc = Tiethoc::option_danhmuctiethoc();
            $option_hocky = Hocky::option_hocky();
            $option_lop = '<option value="">' . trans('commonForm.dropdownAll') . '</option>';
            $option_trang = '';
            $thoikhoabieu = Thoikhoabieu::list_thoikhoabieu('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $this->numrecord);
            $phan_trang = $thoikhoabieu->render();
            $option_theo = Chung::chon($theo_truong);
            $option_tt = Chung::chon($thutu);
            $dateto = date("01-01-2030");
            $datefrom = date("01-01-1970");
        } else {
            $option_vung = Region::option_vung_timkiem($_GET['vung']);
            $option_city = Thanhpho::option_thanhpho_timkiem($_GET['vung'], $_GET['city']);
            $option_huyen = Quanhuyen::option_quan_huyen_timkiem($_GET['vung'], $_GET['city'], $_GET['huyen']);
            $option_school = Schooll::option_truong_timkiem($_GET['vung'], $_GET['city'], $_GET['huyen'], $_GET['school']);
            $option_khoa = Khoa::option_khoa_timkiem($_GET['vung'], $_GET['city'], $_GET['huyen'], $_GET['school'], $_GET['khoa']);
            $option_hedaotao = Hedaotao::option_hedaotao_timkiem($_GET['khoa'], $_GET['hedaotao']);
            $option_monhoc = Monhoc::option_monhoc_timkiem($_GET['hedaotao'], $_GET['monhoc']);
            $option_giangvien = Giangvien::option_giangvien_timkiem($_GET['monhoc'], $_GET['giangvien']);
            $option_khoahoc = Khoahoc::option_khoahoc_timkiem($_GET['khoahoc']);
            $option_giangduong = Giangduong::option_giangduong_timkiem($_GET['giangduong']);
            $option_tiethoc = Tiethoc::option_danhmuctiethoc_timkiem($_GET['tiethoc']);
            $option_hocky = Hocky::option_hocky_timkiem($_GET['hocky']);
            $option_lop = Lop::option_lop_timkiem($_GET['khoa'], $_GET['lop']);
            $option_trang = 'vung=' . $_GET['vung'] . '&city=' . $_GET['city'] . '&huyen=' . $_GET['huyen'] . ''
                    . '&school=' . $_GET['school'] . '&khoa=' . $_GET['khoa'] . '&hedaotao=' . $_GET['hedaotao'] . ''
                    . '&monhoc=' . $_GET['monhoc'] . '&giangvien=' . $_GET['giangvien'] . '&giangduong=' . $_GET['giangduong'] . '&khoahoc=' . $_GET['khoahoc'] . '&giangduong=' . $_GET['giangduong'] . ''
                    . '&tiethoc=' . $_GET['tiethoc'] . '&hocky=' . $_GET['hocky'] . '&lop=' . $_GET['lop'] . ''
                    . '&dateform=' . $_GET['dateform'] . '&dateto=' . $_GET['dateto'] . '&allow=' . $_GET['allow'] . '&tt=' . $_GET['tt'] . '';
            $thoikhoabieu = Thoikhoabieu::list_thoikhoabieu($_GET['vung'], $_GET['city'], $_GET['huyen'], $_GET['school'], $_GET['khoa'], $_GET['lop'], $_GET['hedaotao'], $_GET['monhoc'], $_GET['giangvien'], $_GET['giangduong'], $_GET['tiethoc'], $_GET['hocky'], $_GET['khoahoc'], $_GET['dateform'], $_GET['dateto'], $_GET['allow'], $_GET['tt'], $this->numrecord);
            $phan_trang = $thoikhoabieu->appends(array(
                        'vung' => $_GET['vung'],
                        'city' => $_GET['city'],
                        'huyen' => $_GET['huyen'],
                        'school' => $_GET['school'],
                        'khoa' => $_GET['khoa'],
                        'hedaotao' => $_GET['hedaotao'],
                        'monhoc' => $_GET['monhoc'],
                        'giangvien' => $_GET['giangvien'],
                        'giangduong' => $_GET['giangduong'],
                        'khoahoc' => $_GET['khoahoc'],
                        'tiethoc' => $_GET['tiethoc'],
                        'hocky' => $_GET['hocky'],
                        'lop' => $_GET['lop'],
                        'dateform' => $_GET['dateform'],
                        'dateto' => $_GET['dateto'],
                        'allow' => $_GET['allow'],
                        'tt' => $_GET['tt']
                            )
                    )->render();
            $option_theo = Chung::chon_tk($theo_truong, $_GET['allow']);
            $option_tt = Chung::chon_tk($thutu, $_GET['tt']);
            $dateto = $_GET['dateto'];
            $datefrom = $_GET['dateform'];
        }
        if (isset($_GET["page"])) {
            $page = $_GET['page'];
            $trang = $option_trang . '&page=' . $_GET['page'] . '&';
        } else {
            $page = 1;
            $trang = $option_trang . '&';
        }
        return view('pages.Thoikhoabieu.list_assigned', [
            'phan_trang' => $phan_trang, 'datefrom' => $datefrom, 'dateto' => $dateto,
            'thoikhoabieu' => $thoikhoabieu, 'sobanghi' => $this->numrecord, 'page' => $page,
            'option_vung' => $option_vung, 'option_city' => $option_city, 'option_huyen' => $option_huyen,
            'option_school' => $option_school, 'option_khoa' => $option_khoa, 'option_hedaotao' => $option_hedaotao,
            'option_monhoc' => $option_monhoc, 'option_giangvien' => $option_giangvien, 'option_giangduong' => $option_giangduong,
            'option_khoahoc' => $option_khoahoc, 'option_tiethoc' => $option_tiethoc, 'option_hocky' => $option_hocky, 'option_lop' => $option_lop,
            'option_theo' => $option_theo, 'option_tt' => $option_tt]);
    }

    public function post(Request $request) {
        $command = $_POST['command'];
        $input = $request->all();
        switch ($command) {
            case 'vung' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('thanhpho')->where('region_id', $input['partner_id'])->get();
                echo json_encode($list_appid);
                break;
            case 'city' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('quan_huyen')->where('MaThanhPho', $partner_id)->get();
                echo json_encode($list_appid);
                break;
            case 'huyen' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('truong')->where('MaQuanHuyen', $partner_id)->get();
                echo json_encode($list_appid);
                break;
            case 'school' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('khoa')->where('MaTruong', $partner_id)->get();
                echo json_encode($list_appid);
                break;
            case 'khoa' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('hedaotao')->where('MaKhoa', $partner_id)->get();
                echo json_encode($list_appid);
                break;
            case 'lop' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('lop')->where('MaKhoa', $partner_id)->get();
                echo json_encode($list_appid);
                break;
            case 'hedaotao' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('monhoc')->where('MaHe', $partner_id)->get();
                echo json_encode($list_appid);
                break;
            case 'monhoc' :
                $partner_id = isset($input['partner_id']) ? $input['partner_id'] : '';
                $list_appid = DB::table('giangvien')->where('MaMonHoc', $partner_id)->get();
                echo json_encode($list_appid);
                break;
        }
    }

    public function create() {
        $option_vung = Region::option_vung_add(old('vung'));
        $option_khoahoc = Khoahoc::option_khoahoc_add(old('khoahoc'));
        $option_tiethoc = Tiethoc::option_danhmuctiethoc_add(old('tiethoc'));
        $option_hocky = Hocky::option_hocky_add(old('hocky'));
        $option_giangduong = Giangduong::option_giangduong_add(old('giangduong'));
        $option_city = Thanhpho::option_thanhpho_old(old('vung'), old('city'));
        $option_huyen = Quanhuyen::option_quan_huyen_old(old('city'), old('huyen'));
        $option_truong = Schooll::option_truong_old(old('huyen'), old('school'));
        $option_khoa = Khoa::option_khoa_old(old('school'), old('khoa'));
        $option_lop = Lop::option_lop_old(old('khoa'), old('lop'));
        $option_hedaotao = Hedaotao::option_hedaotao_old(old('khoa'), old('hedaotao'));
        $option_monhoc = Monhoc::option_monhoc_old(old('hedaotao'), old('monhoc'));
        $option_giangvien = Giangvien::option_giangvien_old(old('monhoc'), old('giangvien'));
        return view('pages.Thoikhoabieu.assigned_add', ['option_vung' => $option_vung, 'option_khoahoc' => $option_khoahoc
            , 'option_tiethoc' => $option_tiethoc, 'option_hocky' => $option_hocky, 'option_giangduong' => $option_giangduong
            , 'option_city' => $option_city, 'option_huyen' => $option_huyen, 'option_truong' => $option_truong
            , 'option_khoa' => $option_khoa, 'option_lop' => $option_lop, 'option_hedaotao' => $option_hedaotao,
            'option_monhoc' => $option_monhoc, 'option_giangvien' => $option_giangvien]);
    }

    /*     * add
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(AssignedRequest $request) {
        $dateform = strtotime($request->input('dateform') . ' 00:00:00');
        $dateto = strtotime($request->input('dateto') . ' 23:59:59');
        $id = DB::table('thoikhoabieu')->insertGetId(
                [
                    'MaKhoaHoc' => $request->input('khoahoc'),
                    'MaLop' => $request->input('lop'),
                    'MaMonHoc' => $request->input('monhoc'),
                    'MaGIANGVIEN' => $request->input('giangvien'),
                    'MaGiangDuong' => $request->input('giangduong'),
                    'BatDau' => $dateform,
                    'KetThuc' => $dateto,
                    'MaTiet' => $request->input('tiethoc'),
                    'MaHocKy' => $request->input('hocky')
                ]
        );
        $request->session()->flash('alert-success', 'Lịch phân công giảng dạy (' . $id . ') đã được thêm mới thành công!');
        return redirect(route('list_assigned'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $df=  Thoikhoabieu::thoikhoabieu_edit($id);
        $vung= DB::table('vung')->where('id', $df->region_id)->first();
        $option_vung='<option value="'.$vung->id.'">'.$vung->name.'</option>';
        $thanhpho=DB::table('thanhpho')->where('id', $df->MaThanhPho)->first();
        $option_city='<option value="'.$thanhpho->id.'">'.$thanhpho->name.'</option>';
        $quanhuyen=DB::table('quan_huyen')->where('MaQuanHuyen', $df->MaQuanHuyen)->first();
        $option_huyen='<option value="'.$quanhuyen->id.'">'.$quanhuyen->Ten.'</option>';
        $truong=DB::table('truong')->where('MaTruong', $df->MaTruong)->first();
        $option_truong='<option value="'.$truong->MaTruong.'">'.$truong->TenTruong.'</option>';
        $khoa=DB::table('khoa')->where('MaKhoa', $df->MaKhoa)->first();
        $option_khoa = '<option value="'.$khoa->MaKhoa.'">'.$khoa->TenKhoa.'</option>';
        $hedaotao=DB::table('hedaotao')->where('MaHe', $df->MaHe)->first();
        $option_hedaotao= '<option value="'.$hedaotao->MaHe.'">'.$hedaotao->TenHe.'</option>';
        $monhoc=DB::table('monhoc')->where('MaMonHoc', $df->MaMonHoc)->first();
        $option_monhoc='<option value="'.$monhoc->MaMonHoc.'">'.$monhoc->TenMonHoc.'</option>';
        $option_giangvien=  Giangvien::option_giangvien_old_edit($df->MaGIANGVIEN, $df->MaMonHoc,old('giangvien'));
        $lop=DB::table('lop')->where('MaLop', $df->MaLop)->first();
        $option_lop='<option value="'.$lop->MaLop.'">'.$lop->TenLop.'</option>';
        $option_khoahoc = Khoahoc::option_khoahoc_edit($df->MaKhoaHoc,old('khoahoc'));
        $option_tiethoc = Tiethoc::option_danhmuctiethoc_edit($df->MaTiet,old('tiethoc'));
        $option_hocky = Hocky::option_hocky_edit($df->MaHocKy,old('hocky'));
        $option_giangduong = Giangduong::option_giangduong_edit($df->MaGiangDuong,old('giangduong'));
        $dateform=  Chung::old(old('dateform'),date('d-m-Y', $df->BatDau));
        $dateto=  Chung::old(old('dateto'),date('d-m-Y', $df->KetThuc));
        return view('pages.Thoikhoabieu.assigned_edit', ['option_vung' => $option_vung, 'option_khoahoc' => $option_khoahoc
            , 'option_tiethoc' => $option_tiethoc, 'option_hocky' => $option_hocky, 'option_giangduong' => $option_giangduong
            , 'option_city' => $option_city, 'option_huyen' => $option_huyen, 'option_truong' => $option_truong
            , 'option_khoa' => $option_khoa, 'option_lop' => $option_lop, 'option_hedaotao' => $option_hedaotao,
            'option_monhoc' => $option_monhoc, 'option_giangvien' => $option_giangvien
                ,'dateform'=>$dateform,'dateto'=>$dateto,'id'=>$id]);
    }
    public function update(AssignedRequest $request, $id) {
         $dateform = strtotime($request->input('dateform') . ' 00:00:00');
        $dateto = strtotime($request->input('dateto') . ' 23:59:59');
        DB::table('thoikhoabieu')
                ->where('STT', $id)
                ->update(array(
                    'MaKhoaHoc' => $request->input('khoahoc'),
                    'MaLop' => $request->input('lop'),
                    'MaMonHoc' => $request->input('monhoc'),
                    'MaGIANGVIEN' => $request->input('giangvien'),
                    'MaGiangDuong' => $request->input('giangduong'),
                    'BatDau' => $dateform,
                    'KetThuc' => $dateto,
                    'MaTiet' => $request->input('tiethoc'),
                    'MaHocKy' => $request->input('hocky')
        ));
        $request->session()->flash('alert-success', 'Lịch phân công giảng dạy (' . $id . ') đã được sửa thành công!');
        return redirect(route('list_assigned'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request) {
        DB::table('thoikhoabieu')->where('STT', $id)->delete();
        $request->session()->flash('alert-success', 'Lịch phân công (' . $id . ') đã xóa thành công!');
        return redirect(route('list_assigned'));
    }

}
