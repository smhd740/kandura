<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Invoice;
use App\Notifications\OrderStatusChangedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class OrderObserver
{
    public function created(Order $order): void
    {
        $order->user->notify(new OrderStatusChangedNotification($order));
    }

    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');

            $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus));

            if ($order->status === 'completed') {
                if (!$order->invoice) {
                    $this->createInvoice($order);
                }
            }
        }
    }

    private function createInvoice(Order $order): void
    {
        $invoiceNumber = Invoice::generateInvoiceNumber();

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'order_id' => $order->id,
            'total' => $order->total_amount,
            'pdf_url' => null,
        ]);

        $pdfPath = $this->generateInvoicePDF($order, $invoice);

        $invoice->update(['pdf_url' => $pdfPath]);
    }

    private function generateInvoicePDF(Order $order, Invoice $invoice): string
    {
        $order->load(['user', 'address', 'items.design']);

        $pdf = Pdf::loadView('invoices.pdf', [
            'order' => $order,
            'invoice' => $invoice,
        ]);

        $fileName = 'invoices/' . $invoice->invoice_number . '.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }
}
