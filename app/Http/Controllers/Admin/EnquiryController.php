<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\ProductShade;
use App\Models\User;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['product', 'shade', 'assignee']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('enquiry_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $enquiries = $query->latest()->paginate(15)->withQueryString();
        $staffUsers = User::where('status', 'active')->get();

        $statusCounts = [
            'all' => Enquiry::count(),
            'new' => Enquiry::where('status', 'new')->count(),
            'contacted' => Enquiry::where('status', 'contacted')->count(),
            'quotation_sent' => Enquiry::where('status', 'quotation_sent')->count(),
            'converted' => Enquiry::where('status', 'converted')->count(),
        ];

        return view('admin.enquiries.index', compact('enquiries', 'staffUsers', 'statusCounts'));
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['product.category', 'shade', 'assignee']);
        $staffUsers = User::where('status', 'active')->get();

        return view('admin.enquiries.show', compact('enquiry', 'staffUsers'));
    }

    public function updateStatus(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,quotation_sent,follow_up,converted,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        $enquiry->update($validated);

        return redirect()->route('admin.enquiries.show', $enquiry)
            ->with('success', "Enquiry status updated to '{$enquiry->status}'.");
    }
}
