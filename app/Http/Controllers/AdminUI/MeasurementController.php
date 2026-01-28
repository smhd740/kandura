<?php

namespace App\Http\Controllers\AdminUI;

use App\Http\Controllers\Controller;
use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    /**
     * Display a listing of measurements (sizes).
     */
    public function index(Request $request)
    {
        // الأحجام الثابتة
        $availableSizes = Measurement::availableSizes();

        // إحصائيات لكل حجم
        $sizeStats = [];
        foreach ($availableSizes as $size) {
            $sizeStats[$size] = [
                'total_designs' => \App\Models\Design::bySize($size)->count(),
                'total_users' => Measurement::bySize($size)->distinct('user_id')->count('user_id'),
            ];
        }

        // إحصائيات عامة
        $stats = [
            'total_sizes' => count($availableSizes),
            'total_designs' => \App\Models\Design::count(),
            'most_popular_size' => $this->getMostPopularSize($sizeStats),
        ];

        return view('admin.measurements.index', compact('availableSizes', 'sizeStats', 'stats'));
    }

    /**
     * Get most popular size
     */
    private function getMostPopularSize($sizeStats)
    {
        $maxDesigns = 0;
        $popularSize = 'N/A';

        foreach ($sizeStats as $size => $stats) {
            if ($stats['total_designs'] > $maxDesigns) {
                $maxDesigns = $stats['total_designs'];
                $popularSize = $size;
            }
        }

        return $popularSize;
    }
}
