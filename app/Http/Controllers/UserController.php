<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    function index(){
        return "anil sidhu";
    }
    function addUser(){
        return "Hello from UserController";
    }
    function getUser($id){
        return "User id is: ".$id;
    }
}
