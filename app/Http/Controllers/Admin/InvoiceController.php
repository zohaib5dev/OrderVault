<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Settings;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        try {
            $vendor = Auth::user()->vendor;

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found'
                ], 404);
            }

            $query = Invoice::with('vendor', 'plan');

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%")
                        ->orWhere('payment_method', 'LIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sorting
            $sort = $request->get('sort', 'newest');
            switch ($sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'amount_high':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'amount_low':
                    $query->orderBy('amount', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $invoices = $query->paginate($perPage);

            // Stats
            $stats = [
                'total' => Invoice::count(),
                'pending' => Invoice::where('status', 'pending')->count(),
                'paid' => Invoice::where('status', 'paid')->count(),
                'overdue' => Invoice::where('status', 'overdue')->count(),
                'total_amount' => Invoice::sum('amount'),
                'paid_amount' => Invoice::where('status', 'paid')->sum('amount'),
                'pending_amount' => Invoice::where('status', 'pending')->sum('amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $invoices,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show($id)
    {
        try {
            $invoice = Invoice::with(['plan', 'vendor'])
                ->find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadInvoice(Request $request, $invoiceId)
    {

        $invoice = Invoice::where('id', $invoiceId)
            ->with(['plan', 'vendor'])
            ->first();

        $vendor = Vendor::where('id', $invoice->vendor_id)->first();
        //return $invoice;
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $settings = Settings::where('is_active', true)->get();
        $company_name = $settings->where('key', 'name')->value('value');
        $company_address = $settings->where('key', 'address')->value('value');
        $company_email = $settings->where('key', 'email')->value('value');
        $company_phone = $settings->where('key', 'phone')->value('value');
        $company_logo = $settings->where('key', 'logo')->value('value');

        $logoPath = null;
        if ($company_logo) {
            $logoPath = public_path('storage/company-logos/' . $company_logo);
        }

        try {
            // Prepare invoice data
            $data = [
                'invoice' => $invoice,
                'vendor' => $vendor,
                'plan' => $invoice->plan,
                'company_name' => $company_name ?? config('app.name', 'OrderValut'),
                'company_address' => $company_address ?? config('app.address', '123 Business Street, City, Country'),
                'company_email' => $company_email ?? config('app.email', 'support@example.com'),
                'company_phone' => $company_phone ?? config('app.phone', '+1 234 567 890'),
                'company_logo' => $company_logo,
                'logoPath' => $logoPath,
                'generated_at' => Carbon::now(),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('plan.invoice', $data);

            // Set PDF options
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            // Generate filename
            $filename = "invoice-{$invoice->invoice_number}.pdf";

            // Return PDF download
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Invoice download failed', [
                'invoice_id' => $invoiceId,
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $invoice = Invoice::where('id', $id)->first();
        $invoice->payment_method = $request->payment_method;
        $invoice->paid_at = $request->paid_at;
        $invoice->notes = $request->payment_note;
        $invoice->status = $request->status;
        $invoice->save();

        $vendor = Vendor::where('id',$invoice->vendor_id)->first();
        $vendor->subscription_status = 'active';
        $vendor->save();

        return response()->json([
            'message' => 'Invoice status updated successfully',
            'data' => $invoice,
            'status' => 'success'
        ]);
    }
}
