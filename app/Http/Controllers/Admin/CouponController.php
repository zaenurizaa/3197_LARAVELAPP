<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Event;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('title')->get();
        $coupons = Coupon::with('event')->latest()->paginate(10);
        return view('admin.coupons', compact('coupons', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'            => 'required|string|unique:coupons,code',
            'type'            => 'required|in:fixed,percent',
            'discount_amount' => 'required|numeric|min:1',
            'max_uses'        => 'nullable|integer|min:1',
            'expires_at'      => 'nullable|date',
            'event_id'        => 'nullable|exists:events,id',
        ]);

        Coupon::create([
            'code'           => strtoupper(trim($request->code)),
            'type'           => $request->type,
            'discount_value' => $request->discount_amount,
            'quota'          => $request->max_uses ?? 999999, // default quota
            'expires_at'     => $request->expires_at,
            'event_id'       => $request->event_id,
        ]);

        return redirect()->back()->with('success', 'Kupon baru berhasil ditambahkan!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->back()->with('success', 'Kupon berhasil dihapus!');
    }
}
