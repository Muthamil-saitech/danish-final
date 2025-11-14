<?php

namespace App\Exports;

use App\CustomerOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class POReportExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $order = CustomerOrder::where('del_status', 'Live');

        if (!empty($this->request->startDate)) {
            $startDate = date('Y-m-d 00:00:00', strtotime($this->request->startDate));
            $order->where('created_at', '>=', $startDate);
        }
        if (!empty($this->request->endDate)) {
            $endDate = date('Y-m-d 23:59:59', strtotime($this->request->endDate));
            $order->where('created_at', '<=', $endDate);
        }
        if (!empty($this->request->customer_id)) {
            $order->where('customer_id', $this->request->customer_id);
        }
        if (!empty($this->request->search_po)) {
            $search_po = $this->request->search_po;
            $order->where(function ($query) use ($search_po) {
                $query->where('reference_no', 'like', "%{$search_po}%")
                    ->orWhere('order_type', 'like', "%{$search_po}%");
            });
        }
        if ($this->request->has('order_status') && $this->request->order_status !== '') {
            $order_status = (int) $this->request->order_status;
            $order->whereHas('details', function ($q) use ($order_status) {
                $q->where('order_status', $order_status);
            });
            $order->with(['details' => function ($q) use ($order_status) {
                $q->where('order_status', $order_status);
            }]);
        } else {
            $order->with('details');
        }

        return $order->get()
            ->map(function ($order) {
                $detail = $order->details->first();
                return [
                    'SN' => $order->id,
                    'PO Date' => $order->created_at ? date('d/m/Y', strtotime($order->created_at)) : '',
                    'PO Number' => $order->reference_no ?? '',
                    'Nature of Business' => $order->order_type == "Quotation" ? "Labor" : "Sales",
                    'Customers' => $order->customer->name ?? '',
                    'Total Value' => '₹' . number_format($detail->sub_total ?? 0, 2),
                    'Status For Quotation' => $detail->order_status ? ['0' => 'Pending', '1' => 'Confirmed', '2' => 'Cancelled'][$detail->order_status] : '',
                    'Created By' => getUserName($order->created_by),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'SN',
            'PO Date',
            'PO Number',
            'Nature of Business',
            'Customers',
            'Total Value',
            'Status For Quotation',
            'Created By',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF']]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '₹',
        ];
    }
}