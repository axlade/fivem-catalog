<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * @var array<string, int|null>
     */
    private const PERIODS = ['7d' => 7, '30d' => 30, '90d' => 90, 'all' => null];

    public function index(Request $request): View
    {
        $period = $request->string('period')->toString();

        if (! array_key_exists($period, self::PERIODS)) {
            $period = '30d';
        }

        $to = Carbon::now();
        $from = $this->fromDateFor($period, $to);
        $granularity = $this->granularityFor($from, $to);

        return view('admin.statistics.index', [
            'period' => $period,
            'totals' => [
                'views' => ResourceEvent::views()->between($from, $to)->count(),
                'downloads' => ResourceEvent::downloads()->between($from, $to)->count(),
                'newResources' => Resource::whereBetween('created_at', [$from, $to])->count(),
                'newUsers' => User::whereBetween('created_at', [$from, $to])->count(),
            ],
            'viewsSeries' => $this->series('view', $from, $to, $granularity),
            'downloadsSeries' => $this->series('download', $from, $to, $granularity),
            'topDownloaded' => $this->topResources('download', $from, $to),
            'topViewed' => $this->topResources('view', $from, $to),
        ]);
    }

    private function fromDateFor(string $period, Carbon $to): Carbon
    {
        if ($period !== 'all') {
            return $to->copy()->subDays(self::PERIODS[$period])->startOfDay();
        }

        $earliest = ResourceEvent::min('created_at');

        return $earliest ? Carbon::parse($earliest)->startOfDay() : $to->copy()->subDays(30)->startOfDay();
    }

    private function granularityFor(Carbon $from, Carbon $to): string
    {
        $days = $from->diffInDays($to);

        return match (true) {
            $days <= 31 => 'day',
            $days <= 180 => 'week',
            default => 'month',
        };
    }

    /**
     * Daily/weekly/monthly counts for a chart, with gaps filled at day
     * granularity so the bars line up continuously.
     *
     * @return array<int, array{label: string, count: int}>
     */
    private function series(string $type, Carbon $from, Carbon $to, string $granularity): array
    {
        $groupExpr = match ($granularity) {
            'day' => 'DATE(created_at)',
            'week' => 'DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY)',
            'month' => "DATE_FORMAT(created_at, '%Y-%m-01')",
        };

        $rows = ResourceEvent::query()
            ->where('type', $type)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("{$groupExpr} as bucket, COUNT(*) as total")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('total', 'bucket');

        if ($granularity === 'day') {
            $series = [];
            $cursor = $from->copy()->startOfDay();

            while ($cursor->lte($to)) {
                $key = $cursor->format('Y-m-d');
                $series[] = [
                    'label' => $cursor->format('j'),
                    'tooltip' => $cursor->format('M j, Y'),
                    'count' => (int) ($rows[$key] ?? 0),
                ];
                $cursor->addDay();
            }

            return $series;
        }

        $labelFormat = $granularity === 'week' ? 'M j' : 'M Y';

        return $rows->map(fn ($count, $bucket) => [
            'label' => Carbon::parse($bucket)->format($labelFormat),
            'tooltip' => Carbon::parse($bucket)->format($granularity === 'week' ? 'M j, Y' : 'F Y'),
            'count' => (int) $count,
        ])->values()->all();
    }

    /**
     * Top 10 resources by view/download count within the period.
     */
    private function topResources(string $type, Carbon $from, Carbon $to): Collection
    {
        return ResourceEvent::query()
            ->where('type', $type)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('resource_id, COUNT(*) as total')
            ->groupBy('resource_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('resource:id,title,slug,thumbnail_path,user_id')
            ->get()
            ->filter(fn ($row) => $row->resource !== null)
            ->values();
    }
}
