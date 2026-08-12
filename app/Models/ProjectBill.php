<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectBill extends Model
{
    protected $fillable = [
        'project_id', 'bill_number', 'reference_number', 'work_order_number',
        'bill_date', 'due_date', 'subtotal', 'tax_amount', 'total_amount', 
        'notes', 'terms_conditions', 'status'
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function client()
    {
        return $this->project->client();
    }

    public static function generateBillNumber(): string
    {
        $latest = static::latest()->first();
        $number = $latest ? intval(substr($latest->bill_number, 3)) + 1 : 1;
        return 'BIL' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public static function generateReferenceNumber(): string
    {
        return 'IT-BILL-NSU-' . now()->format('dmY') . '-01';
    }

    public function generatePdf()
    {
        $data = [
            'bill' => $this,
            'project' => $this->project,
            'client' => $this->project->client,
            'company' => [
                'name' => 'Intelligent Technology',
                'address' => 'Your Company Address Here',
                'phone' => '+880 XXXX-XXXXXX',
                'email' => 'info@intelligent-tech.com',
            ],
            'amount_in_words' => $this->convertToWords($this->total_amount),
        ];

        $html = view('pdf.bill', $data)->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    private function convertToWords($number)
    {
        // Simple number to words conversion for Bangladeshi Taka
        // You might want to use a package for more robust conversion
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        
        $lakh = (int)($number / 100000);
        $thousand = (int)(($number % 100000) / 1000);
        $hundred = (int)(($number % 1000) / 100);
        $remainder = $number % 100;
        
        $words = [];
        
        if ($lakh > 0) {
            $words[] = $this->convertToWords($lakh) . ' Lac';
        }
        
        if ($thousand > 0) {
            $words[] = $this->convertToWords($thousand) . ' Thousand';
        }
        
        if ($hundred > 0) {
            $words[] = $ones[$hundred] . ' Hundred';
        }
        
        if ($remainder > 0) {
            if ($remainder < 10) {
                $words[] = $ones[$remainder];
            } elseif ($remainder < 20) {
                $words[] = $teens[$remainder - 10];
            } else {
                $words[] = $tens[(int)($remainder / 10)];
                if ($remainder % 10 > 0) {
                    $words[] = $ones[$remainder % 10];
                }
            }
        }
        
        return implode(' ', $words) . ' Taka Only';
    }
}