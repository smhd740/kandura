<?php

namespace App\Http\Controllers\AdminUI;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Download invoice PDF
     */
    public function download(Invoice $invoice)
    {
        // التحقق من وجود الملف
        if (!$invoice->pdf_url || !Storage::disk('public')->exists($invoice->pdf_url)) {
            return back()->with('error', 'Invoice PDF not found');
        }

        // مسار الملف الكامل
        $filePath = storage_path('app/public/' . $invoice->pdf_url);

        // تحميل الملف
        return response()->download($filePath, $invoice->invoice_number . '.pdf');
    }

    /**
     * View invoice PDF in browser
     */
    public function view(Invoice $invoice)
    {
        // التحقق من وجود الملف
        if (!$invoice->pdf_url || !Storage::disk('public')->exists($invoice->pdf_url)) {
            return back()->with('error', 'Invoice PDF not found');
        }

        // مسار الملف الكامل
        $filePath = storage_path('app/public/' . $invoice->pdf_url);

        // عرض الملف في المتصفح
        return response()->file($filePath);
    }
}
