<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Theater;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $randomTheater = null;
        
        // Lọc theo thành phố nếu có
        if ($request->has('city') && $request->city != '') {
            $theaters = Theater::where('City', $request->city)->get();
            
            // Lấy ngẫu nhiên 1 rạp nếu có
            if ($theaters->count() > 0) {
                $randomTheater = $theaters->random();
            }
        }
        
        // Lấy danh sách các thành phố có rạp
        $cities = Theater::select('City')
            ->distinct()
            ->orderBy('City', 'asc')
            ->pluck('City');
        
        return view('customer.contact.index', compact('randomTheater', 'cities'));
    }
}