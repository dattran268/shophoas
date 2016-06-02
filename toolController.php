<?php

/*
  created by: huynt
  time:
  description:
 */

namespace App\Http\Controllers;

use Illuminate\Routing\Route;
use App\GroupRoutes;
use Dotenv;
use Sentry;
use App\Groups;
use App\Routes as Routes;
use App\UserRoutes;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use App\Http\MaintainDb\database;
use App\Http\MaintainDb\MysqlDump;
use App\Http\MaintainDb\Locale;
use App\Http\MaintainDb\ValidateInput;
use App\Http\FormRequests\RestoreDbRequest;
use App\RoutesMenu;

class toolController extends Controller {

    private $secret_key = "qms";

    public function changelanguage(Request $request) {
        $uri_ret = $request->input("curi");
        $uri_ret = urldecode($uri_ret);
        if (ends_with($uri_ret, "sl=1"))
            $uri_ret = substr_replace($uri_ret, '', strlen($uri_ret) - 5);
        $locale = \App::getLocale();
        if (strpos($uri_ret, '/public') !== false)
            $pos = strpos($uri_ret, '/public') + 7;
        else {
            $pos = strpos($uri_ret, '/', strlen('/' . env('app_root')));
        }

        if ($locale == "en") {
            $new_locale = "vi";
            if (strpos($uri_ret, '/en?') !== false)
                $strRet = substr_replace($uri_ret, '/', $pos, 3);
            else
                $strRet = substr_replace($uri_ret, '/', $pos, 4);
        } else {
            $new_locale = "en";
            $strRet = substr($uri_ret, 0, $pos) . '/en' . substr($uri_ret, $pos);
            if (ends_with($strRet, "/en/"))
                $strRet = substr_replace($strRet, '', strlen($strRet) - 1);
        }

        \App::setLocale($new_locale);
        $strRet = "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER['SERVER_PORT'] . $strRet;
        if (strpos($uri_ret, "?")) {
            $strRet.="&sl=1";
        } else {
            $strRet.="?sl=1";
        }
        return redirect($strRet);
    }

    public function index() {
        return View::make('home')
        ;
    }

    /*
     * menu section
     */

    public function routesMenuUpdate(Request $req) {
        $id = $req->input('id_update');
        $order = $req->input('order');
        $parent_name = $req->input('parent_name');
        $name_display = $req->input('name_display');
        try {
//            Routes::select('set names utf-8');
            RoutesMenu::updateInline(['parent_name' => $parent_name, 'order' => $order, 'name_display' => $name_display], $id);
            $strRet = "success";
        } catch (\Illuminate\Database\QueryException $e) {
            $strRet = "error";
        }
        return $strRet;
    }

//    public function getRouteMenu() {
//        RoutesMenu::truncate();
//        $routeCollection = \Route::getRoutes();
//        foreach ($routeCollection as $value) {
//            if (
//                    in_array(env('APP_MIDDLEWARE'), $value->middleware())
//            ) {
//                $arrInsert["method"] = implode($value->methods(), ",");
//                $arrInsert["domain"] = $value->domain();
//                $arrInsert["action"] = $value->getActionName();
//                $arrInsert["middleware"] = implode($value->middleware(), ",");
//                $arrInsert["uri"] = str_replace("/\/{\w+}/", "/0", $value->getPath());
//                $arrInsert["name"] = ($value->getName() ? $value->getName() : str_replace("/", "_", $arrInsert["uri"]));
//                RoutesMenu::insert_route($arrInsert);
//            }
//        }
//        \Session::flash('alert-success', trans('auth.route_have_insert_to_db'));
//        return \Redirect::route("routes_menu");
//    }

    public function getRouteMenu() {
        RoutesMenu::truncate();
        Routes::truncate();
        \Session::flash('alert-success', 'Cập nhật thành công');
        return \Redirect::route("routes_menu");
    }

    public function routesMenu() {
        $routes = RoutesMenu::ls_routes_menu();
        return View::make('phanquyen.routes_menu')
                        ->with('routes', $routes)
        ;
    }

    ////////////////////////
    /*
     * group section
     */
    public function groups() {
        $groups = Groups::ls_groups();
        return View::make('phanquyen.groups')
                        ->with('groups', $groups);
    }

    public function routesGroup($id) {
        $group_id = $id;
        $routes = Routes::ls_routes_group();
        $check_routes = GroupRoutes::ls_check_routes($group_id);
        $check_routes = lsObjDbToArr($check_routes);
        return View::make('phanquyen.routes_group')
                        ->with('group_id', $id)
                        ->with('routes', $routes)
                        ->with('check_routes', $check_routes)
        ;
    }

    public function routesUser($id) {
        $user_id = $id;
        $routes = Routes::ls_routes_user();
        $check_routes = UserRoutes::ls_check_routes($user_id);
        $check_routes = lsObjDbToArr($check_routes);
        return View::make('phanquyen.routes_user')
                        ->with('user_id', $id)
                        ->with('routes', $routes)
                        ->with('check_routes', $check_routes)
        ;
    }

    public function storeRoutesGroup(Request $request) {
        $group_id = $request['group_id'];
        GroupRoutes::resetWithGroup($group_id);
        if (isset($request["permission"])) {
            foreach ($request["permission"] as $route_id => $value) {
                //insert after reset
                GroupRoutes::updateGroupWith($route_id, $group_id);
            }
        }
        \Session::flash('alert-success', trans('auth.permissions_have_updated'));
        return \Redirect::route("routes_group_phanquyen", $group_id);
    }

    public function storeRoutesUser(Request $request) {
        $user_id = $request['user_id'];
        UserRoutes::resetWithUser($user_id);
        if (isset($request["permission"])) {
            foreach ($request["permission"] as $route_id => $value) {
                //insert after reset
                UserRoutes::updateUserWith($route_id, $user_id);
            }
        }
        \Session::flash('alert-success', trans('auth.permissions_have_updated'));
        return \Redirect::route("routes_user_phanquyen", $user_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function routesGroupDestroy($id) {
        Routes::destroy_route($id);
        \Session::flash('alert-success', trans('auth.route_have_deleted'));
        return \Redirect::route('routes_group_phanquyen');
    }

    public function routesUserDestroy($id) {
        Routes::destroy_route($id);
        \Session::flash('alert-success', trans('auth.route_have_deleted'));
        return \Redirect::route('routes_user_phanquyen');
    }

    public function sldl() {
        error_reporting(E_ALL & ~E_DEPRECATED);
        $mysql_dump = new MysqlDump();
        $mysql = \Config::get("database.connections.mysql");
        foreach ($mysql as $key => $value) {
            $$key = $value;
        }
        $mysql_dump->setDBHost($host, $username, $password);
        $sql = $mysql_dump->dumpDB($database);
        $mysql_dump->download_sql($sql, $database . "_" . date("Y-m-d") . ".sql");
    }

    public function phdlpost(RestoreDbRequest $request) {
        error_reporting(E_ALL & ~E_DEPRECATED);
        $mysql_dump = new MysqlDump();
        $mysql = \Config::get("database.connections.mysql");
        foreach ($mysql as $key => $value) {
            $$key = $value;
        }
        $mysql_dump->setDBHost($host, $username, $password);
        if ($mysql_dump->restoreDB($_FILES['filebk']['tmp_name'])) {
            \Session::flash('alert-success', "Quá trình phục hồi cơ sở dữ liệu thành công");
        } else {
            \Session::flash('alert-error', "Quá trình phục hồi cơ sở dữ liệu không thành công");
        }
        return \Redirect::route('phdl');
    }

    function phdl() {
        return View::make('phanquyen.phdl');
    }

    private function authenticate($sha1_hash, $branch_id, $transaction_time) {
        $my_hash = sha1($this->secret_key . $branch_id . $transaction_time);
        if ($my_hash == $sha1_hash)
            return true;
        else {
            return false;
        }
    }

    public function addTransaction() {
        error_reporting(E_ALL & ~E_DEPRECATED);
        $mysql = \Config::get("database.connections.mysql");
        foreach ($mysql as $key => $value) {
            $$key = $value;
        }
        $db = new database($host, $username, $password, $database, $prefix);
        //$sha1_hash,$branch_id,$transaction_time,$transaction_id,$ticket_number,$service_id,$station_id,$time_in,$time_served,$time_out,$vote_score
        if ($_POST) {
            $sha1_hash = $_POST['sha1_hash'];
            $branch_id = $_POST['branch_id'];
            $transaction_time = $_POST['transaction_time'];
            $transaction_id = $_POST['transaction_id'];
            $ticket_number = $_POST['ticket_number'];
            $service_id = $_POST['service_id'];
            $station_id = $_POST['station_id'];
            $time_in = strtotime($_POST['time_in']);
            $time_served = strtotime($_POST['time_served']);
            $time_out = strtotime($_POST['time_out']);
            $vote_score = $_POST['vote_score'];
            $employee_id = $_POST['employee_id'];
            $isVip = $_POST['is_vip'];
            $vote_comment = base64_decode($_POST['vote_comment']);
            $customer_info = $_POST['customer_info'];

            //print $vote_comment;
            if ($this->authenticate($sha1_hash, $branch_id, $transaction_time)) {
                // update to database
                $sql = "insert into qms_transactions (ticket_number, branch_id, service_id, station_id,time_in,time_served,time_out,vote_score,employee_id,isVIP,vote_comment,customer_info)
		values($ticket_number,'$branch_id', $service_id, $station_id, $time_in, $time_served , $time_out, $vote_score,'$employee_id',$isVip,'$vote_comment','$customer_info')";
                mysql_query("SET NAMES utf8");
                if (mysql_query($sql))
                    print 'STECH_QMS_POST_OK'; //.$sql;
                else
                    $this->message_api('STECH_QMS_POST_ERROR');
            }
            else {
                $this->message_api('STECH_QMS_POST_ERROR');
            }
        } else {
            $this->message_api('No have data Post: $sha1_hash,$branch_id,$transaction_time,$transaction_id,$ticket_number,$service_id,$station_id,$time_in,$time_served,$time_out,$vote_score');
        }
        exit;
    }

    private function message_api($msg) {
        echo $msg;
    }

}
