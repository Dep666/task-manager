<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamAnalyticsController extends Controller
{
    /**
     * Отображение аналитики команды
     */
    public function index(Team $team, Request $request)
    {
        // Проверяем, является ли текущий пользователь владельцем команды
        if (auth()->id() !== $team->owner_id) {
            return redirect()->route('teams.index')->with('error', 'Только владелец команды может просматривать аналитику');
        }

        // Получаем даты для фильтрации
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonths(1);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        // Если даты совпадают, добавляем один день к конечной дате для корректного отображения графика
        $sameDay = $startDate->format('Y-m-d') === $endDate->format('Y-m-d');
        $displayEndDate = $sameDay ? (clone $endDate)->addDay() : $endDate;

        // Получаем всех пользователей команды
        $teamMembers = $team->users()->get();

        // Общая командная аналитика
        $teamStats = [
            'total_tasks' => Task::where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'completed_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%completed%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'in_progress_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%in_progress%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'new_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%new%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'revision_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%revision%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'review_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%review%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'overdue_tasks' => Task::where('team_id', $team->id)
                ->where('deadline', '<', Carbon::now())
                ->whereHas('status', function($query) {
                    $query->where('slug', 'not like', '%completed%');
                })
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
        ];

        // Добавляем расчет процента выполненных задач
        $teamStats['completion_rate'] = $teamStats['total_tasks'] > 0 
            ? round(($teamStats['completed_tasks'] / $teamStats['total_tasks']) * 100, 1) 
            : 0;

        // Аналитика по пользователям
        $userStats = [];
        foreach ($teamMembers as $member) {
            $totalTasks = Task::where('team_id', $team->id)
                ->where('assigned_user_id', $member->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count();
                
            $completedTasks = Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%completed%');
                })
                ->where('team_id', $team->id)
                ->where('assigned_user_id', $member->id)
                ->whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count();
                
            $overdueTasks = Task::where('team_id', $team->id)
                ->where('assigned_user_id', $member->id)
                ->where('deadline', '<', Carbon::now())
                ->whereHas('status', function($query) {
                    $query->where('slug', 'not like', '%completed%');
                })
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count();
                
            $userStats[$member->id] = [
                'name' => $member->name,
                'email' => $member->email,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'overdue_tasks' => $overdueTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            ];
        }

        // Статистика задач по дням (для графика)
        $dailyStats = Task::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('team_id', $team->id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->toArray();
            
        $completionStats = Task::select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('COUNT(*) as completed')
            )
            ->whereHas('status', function($query) {
                $query->where('slug', 'like', '%completed%');
            })
            ->where('team_id', $team->id)
            ->whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->toArray();

        // Подготовка данных для графиков
        $chartDates = [];
        $chartTotalTasks = [];
        $chartCompletedTasks = [];
        
        if ($sameDay) {
            // Для одного дня создаем две точки данных (утро и вечер)
            $dateStr = $startDate->format('Y-m-d');
            
            // Получаем данные за утро (первая половина дня)
            $morningTotalTasks = Task::where('team_id', $team->id)
                ->whereBetween('created_at', [
                    $startDate->copy()->startOfDay(), 
                    $startDate->copy()->startOfDay()->addHours(12)
                ])
                ->count();
                
            $morningCompletedTasks = Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%completed%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('updated_at', [
                    $startDate->copy()->startOfDay(), 
                    $startDate->copy()->startOfDay()->addHours(12)
                ])
                ->count();
            
            // Получаем данные за вечер (вторая половина дня)
            $eveningTotalTasks = Task::where('team_id', $team->id)
                ->whereBetween('created_at', [
                    $startDate->copy()->startOfDay()->addHours(12), 
                    $startDate->copy()->endOfDay()
                ])
                ->count();
                
            $eveningCompletedTasks = Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%completed%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('updated_at', [
                    $startDate->copy()->startOfDay()->addHours(12), 
                    $startDate->copy()->endOfDay()
                ])
                ->count();
            
            // Первая точка (утро)
            $chartDates[] = $startDate->format('d.m.Y') . ' (утро)';
            $chartTotalTasks[] = $morningTotalTasks;
            $chartCompletedTasks[] = $morningCompletedTasks;
            
            // Вторая точка (вечер) с реальными данными
            $chartDates[] = $startDate->format('d.m.Y') . ' (вечер)';
            $chartTotalTasks[] = $eveningTotalTasks;
            $chartCompletedTasks[] = $eveningCompletedTasks;
        } else {
            // Стандартная обработка для диапазона дат
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $chartDates[] = $currentDate->format('d.m.Y');
                $chartTotalTasks[] = $dailyStats[$dateStr]['total'] ?? 0;
                $chartCompletedTasks[] = $completionStats[$dateStr]['completed'] ?? 0;
                $currentDate->addDay();
            }
        }

        $chartData = [
            'dates' => $chartDates,
            'totalTasks' => $chartTotalTasks,
            'completedTasks' => $chartCompletedTasks,
            'sameDay' => $sameDay
        ];

        return view('teams.analytics', compact('team', 'teamStats', 'userStats', 'chartData', 'startDate', 'endDate'));
    }

    /**
     * Экспорт аналитики команды в различных форматах
     */
    public function export(Request $request, $id)
    {
        // Получаем команду
        $team = Team::findOrFail($id);

        // Проверяем, является ли текущий пользователь владельцем команды
        if (auth()->id() !== $team->owner_id) {
            return redirect()->route('teams.index')->with('error', 'Только владелец команды может экспортировать аналитику');
        }

        // Получаем даты для фильтрации
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonths(1);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        // Получаем формат экспорта
        $format = $request->input('format', 'pdf');

        // Общая командная аналитика (используем ту же логику, что и в методе index)
        $teamStats = [
            'total_tasks' => Task::where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'completed_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%completed%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'in_progress_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%in_progress%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'new_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%new%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'revision_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%revision%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'review_tasks' => Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%review%');
                })
                ->where('team_id', $team->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
                
            'overdue_tasks' => Task::where('team_id', $team->id)
                ->where('deadline', '<', Carbon::now())
                ->whereHas('status', function($query) {
                    $query->where('slug', 'not like', '%completed%');
                })
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
        ];

        // Добавляем расчет процента выполненных задач
        $teamStats['completion_rate'] = $teamStats['total_tasks'] > 0 
            ? round(($teamStats['completed_tasks'] / $teamStats['total_tasks']) * 100, 1) 
            : 0;

        // Аналитика по пользователям
        $userStats = [];
        foreach ($team->users as $member) {
            $totalTasks = Task::where('team_id', $team->id)
                ->where('assigned_user_id', $member->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count();
                
            $completedTasks = Task::whereHas('status', function($query) {
                    $query->where('slug', 'like', '%completed%');
                })
                ->where('team_id', $team->id)
                ->where('assigned_user_id', $member->id)
                ->whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count();
                
            $overdueTasks = Task::where('team_id', $team->id)
                ->where('assigned_user_id', $member->id)
                ->where('deadline', '<', Carbon::now())
                ->whereHas('status', function($query) {
                    $query->where('slug', 'not like', '%completed%');
                })
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count();
                
            $userStats[$member->id] = [
                'name' => $member->name,
                'email' => $member->email,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'overdue_tasks' => $overdueTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            ];
        }

        // Формируем имя файла
        $fileName = 'team_analytics_' . $team->id . '_' . date('Y-m-d');

        // Экспорт в зависимости от формата
        switch ($format) {
            case 'pdf':
                return $this->exportToPDF($team, $teamStats, $userStats, $startDate, $endDate, $fileName);
            
            case 'csv':
                return $this->exportToCSV($team, $teamStats, $userStats, $startDate, $endDate, $fileName);
            
            case 'excel':
                return $this->exportToExcel($team, $teamStats, $userStats, $startDate, $endDate, $fileName);
            
            default:
                return redirect()->back()->with('error', 'Неподдерживаемый формат экспорта');
        }
    }

    /**
     * Экспорт в PDF
     */
    private function exportToPDF($team, $teamStats, $userStats, $startDate, $endDate, $fileName)
    {
        // Подготавливаем данные для отображения в PDF
        $data = [
            'team' => $team,
            'teamStats' => $teamStats,
            'userStats' => $userStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'fileName' => $fileName
        ];

        // Создаем PDF с использованием шаблона blade
        $pdf = \PDF::loadView('exports.team-analytics-pdf', $data);
        
        // Устанавливаем параметры PDF документа
        $pdf->setPaper('a4', 'portrait');
        
        // Возвращаем PDF для скачивания
        return $pdf->download($fileName . '.pdf');
    }

    /**
     * Экспорт в CSV
     */
    private function exportToCSV($team, $teamStats, $userStats, $startDate, $endDate, $fileName)
    {
        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($team, $teamStats, $userStats, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Добавляем BOM для корректного отображения кириллицы в Excel/CSV
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Заголовок файла с информацией
            fputcsv($file, ['Аналитика команды: ' . $team->name], ';');
            fputcsv($file, ['Период: с ' . $startDate->format('d.m.Y') . ' по ' . $endDate->format('d.m.Y')], ';');
            fputcsv($file, [], ';');
            
            // Общая статистика команды
            fputcsv($file, ['Общая статистика команды'], ';');
            fputcsv($file, ['Показатель', 'Значение'], ';');
            fputcsv($file, ['Всего задач', $teamStats['total_tasks']], ';');
            fputcsv($file, ['Выполненных задач', $teamStats['completed_tasks']], ';');
            fputcsv($file, ['В работе', $teamStats['in_progress_tasks']], ';');
            fputcsv($file, ['Новые задачи', $teamStats['new_tasks']], ';');
            fputcsv($file, ['На доработке', $teamStats['revision_tasks']], ';');
            fputcsv($file, ['На проверке', $teamStats['review_tasks']], ';');
            fputcsv($file, ['Просроченных задач', $teamStats['overdue_tasks']], ';');
            fputcsv($file, ['Процент выполнения', $teamStats['completion_rate'] . '%'], ';');
            fputcsv($file, [], ';');
            
            // Статистика по участникам
            fputcsv($file, ['Статистика по участникам команды'], ';');
            fputcsv($file, ['Имя', 'Email', 'Всего задач', 'Выполнено', 'Просрочено', 'Выполнение (%)'], ';');
            
            foreach ($userStats as $userId => $stats) {
                fputcsv($file, [
                    $stats['name'],
                    $stats['email'],
                    $stats['total_tasks'],
                    $stats['completed_tasks'],
                    $stats['overdue_tasks'],
                    $stats['completion_rate'] . '%'
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Экспорт в Excel
     */
    private function exportToExcel($team, $teamStats, $userStats, $startDate, $endDate, $fileName)
    {
        // Создаем файл Excel в формате HTML
        $headers = [
            "Content-type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName.xls",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // HTML-контент для Excel
        $content = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" 
                  xmlns:x="urn:schemas-microsoft-com:office:excel" 
                  xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta name="ProgId" content="Excel.Sheet">
                <meta name="Generator" content="Microsoft Excel 11">
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 4px 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                        font-weight: bold;
                    }
                    .header {
                        font-size: 16pt;
                        font-weight: bold;
                        margin-bottom: 10px;
                    }
                    .subheader {
                        font-size: 12pt;
                        margin-bottom: 15px;
                    }
                    .section-title {
                        font-size: 14pt;
                        font-weight: bold;
                        margin: 15px 0 5px 0;
                        background-color: #e9ecef;
                        padding: 5px;
                    }
                    .value-green {
                        color: #008000;
                    }
                    .value-red {
                        color: #FF0000;
                    }
                    .value-blue {
                        color: #0000FF;
                    }
                    .value-purple {
                        color: #800080;
                    }
                </style>
            </head>
            <body>
                <div class="header">Аналитика команды: ' . $team->name . '</div>
                <div class="subheader">Период: с ' . $startDate->format('d.m.Y') . ' по ' . $endDate->format('d.m.Y') . '</div>
                
                <div class="section-title">Общая статистика команды</div>
                <table>
                    <tr>
                        <th>Показатель</th>
                        <th>Значение</th>
                    </tr>
                    <tr>
                        <td>Всего задач</td>
                        <td class="value-blue">' . $teamStats['total_tasks'] . '</td>
                    </tr>
                    <tr>
                        <td>Выполненных задач</td>
                        <td class="value-green">' . $teamStats['completed_tasks'] . '</td>
                    </tr>
                    <tr>
                        <td>В работе</td>
                        <td>' . $teamStats['in_progress_tasks'] . '</td>
                    </tr>
                    <tr>
                        <td>Новые задачи</td>
                        <td>' . $teamStats['new_tasks'] . '</td>
                    </tr>
                    <tr>
                        <td>На доработке</td>
                        <td>' . $teamStats['revision_tasks'] . '</td>
                    </tr>
                    <tr>
                        <td>На проверке</td>
                        <td>' . $teamStats['review_tasks'] . '</td>
                    </tr>
                    <tr>
                        <td>Просроченных задач</td>
                        <td class="value-red">' . $teamStats['overdue_tasks'] . '</td>
                    </tr>
                    <tr>
                        <td>Процент выполнения</td>
                        <td class="value-purple">' . $teamStats['completion_rate'] . '%</td>
                    </tr>
                </table>
                
                <div class="section-title">Статистика по участникам команды</div>
                <table>
                    <tr>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Всего задач</th>
                        <th>Выполнено</th>
                        <th>Просрочено</th>
                        <th>Выполнение (%)</th>
                    </tr>';
        
        foreach ($userStats as $userId => $stats) {
            $content .= '
                    <tr>
                        <td>' . $stats['name'] . '</td>
                        <td>' . $stats['email'] . '</td>
                        <td>' . $stats['total_tasks'] . '</td>
                        <td>' . $stats['completed_tasks'] . '</td>
                        <td>' . $stats['overdue_tasks'] . '</td>
                        <td>' . $stats['completion_rate'] . '%</td>
                    </tr>';
        }
        
        $content .= '
                </table>
                
                <div style="margin-top: 20px; font-size: 10pt; color: #666;">
                    Отчет сгенерирован ' . now()->format('d.m.Y H:i:s') . '
                </div>
            </body>
            </html>
        ';

        return response($content)->withHeaders($headers);
    }
} 