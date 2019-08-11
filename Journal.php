<?php

namespace App\Http\Controllers;
use App\Group;
use App\User;
use App\Caregiver;
use Illuminate\Http\Request;
use App\Http\Requests\JournalRequest;
use Session;
class Journal extends Controller
{
    public function index()
    {
    	
    	$users= User::latest()->get();
    	//$groups = Group::latest()->get();
    	$groups =  Group::orderBy('name')->get();
    	return view('Groups.index',compact('groups','users'));
    }
    public function edit($id)
    {
     	$group = Group::findOrFail($id);
        return view('Groups.edit')->with('group', $group);
    } 

     public function update($id, JournalRequest $request)
    {   	
        $users= User::latest()->get();
        $tab=$request->all();      
        $k=$tab['email'];
        foreach ($users as $u )
        { 
            if($u->email==$k)
            {
                $p=$u->id;
            }
        }  
        $k=User::find($p);
        $group = Group::findOrFail($id);
        $c= New Caregiver;
        if($group->caregivers!=null)
        {
            $group->caregivers->delete();
            $group->caregivers()->save($c);
            $k->caregivers()->save($c);
        }
        else
        {
              $group->caregivers()->save($c);
              $k->caregivers()->save($c);
        }
      
        $group->update($request->all());      
        return redirect('journal');
    }

    public function destroy($id)
    {
    	$group= Group::findOrFail($id);
    	$group->delete();
    	return redirect('journal');
    }

    
      public function create()
    {
    	return view('Groups.create');
    }

    public function store(JournalRequest $request)
    {
    	$users= User::latest()->get();
        $tab=$request->all();      
        $k=$tab['email'];
        foreach ($users as $u )
        { 
            if($u->email==$k)
            {
                $p=$u->id;
            }
        }  
        $k=User::find($p); 
        if($k->hasRole('Student') !=true)
        {  	
           
            $group = Group::create($request->all());
            $c= New Caregiver;
            $group->caregivers()->save($c);
            $k->caregivers()->save($c);
        }
        else
        {
            Session::flash('group_error','Podany użytkownik nie może być opiekunem grópy');
        }
        return redirect('journal');
    }
}
