<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class HomeController extends Controller {
    public function index() {
        return view('home',[]);
    }
}


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

