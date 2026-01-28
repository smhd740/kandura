<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // التحقق إذا تغيرت الحالة إلى completed
        if ($order->isDirty('status') && $order->status === 'completed') {
            // التحقق إذا ما في فاتورة موجودة
            if (!$order->invoice) {
                $this->createInvoice($order);
            }
        }
    }

    /**
     * Create invoice for the order
     */
    private function createInvoice(Order $order): void
    {
        // توليد رقم الفاتورة
        $invoiceNumber = Invoice::generateInvoiceNumber();

        // إنشاء الفاتورة
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'order_id' => $order->id,
            'total' => $order->total_amount,
            'pdf_url' => null, // رح نحدثه بعد توليد الـ PDF
        ]);

        // توليد PDF
        $pdfPath = $this->generateInvoicePDF($order, $invoice);

        // تحديث مسار الـ PDF
        $invoice->update(['pdf_url' => $pdfPath]);
    }

    /**
     * Generate PDF for invoice
     */
    private function generateInvoicePDF(Order $order, Invoice $invoice): string
    {
        // تحميل البيانات
        $order->load(['user', 'address', 'items.design']);

        // توليد الـ PDF
        $pdf = Pdf::loadView('invoices.pdf', [
            'order' => $order,
            'invoice' => $invoice,
        ]);

        // المسار
        $fileName = 'invoices/' . $invoice->invoice_number . '.pdf';

        // حفظ الـ PDF
        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }
}
