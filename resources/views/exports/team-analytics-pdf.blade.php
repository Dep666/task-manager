<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аналитика команды {{ $team->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .page-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .page-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .stat-item {
            padding: 8px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .stat-label {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .stat-value {
            font-size: 14px;
        }
        .stat-value-blue {
            color: #2563eb;
        }
        .stat-value-green {
            color: #059669;
        }
        .stat-value-red {
            color: #dc2626;
        }
        .stat-value-purple {
            color: #7c3aed;
        }
        .stat-value-yellow {
            color: #d97706;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .progress-bar-container {
            width: 100%;
            height: 10px;
            background-color: #e5e7eb;
            border-radius: 5px;
            margin-top: 5px;
        }
        .progress-bar {
            height: 10px;
            border-radius: 5px;
            background-color: #3b82f6;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <div style="text-align: center; margin-bottom: 10px;">
            <img src="{{ public_path('img/logo.png') }}" alt="TaskManager Logo" style="height: 50px; width: auto; margin: 0 auto;">
        </div>
        <div class="page-title">Аналитика команды: {{ $team->name }}</div>
        <div class="page-subtitle">Период: {{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Общая статистика команды</div>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-label">Всего задач:</div>
                <div class="stat-value stat-value-blue">{{ $teamStats['total_tasks'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Выполненных задач:</div>
                <div class="stat-value stat-value-green">{{ $teamStats['completed_tasks'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">В работе:</div>
                <div class="stat-value stat-value-blue">{{ $teamStats['in_progress_tasks'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Новые задачи:</div>
                <div class="stat-value stat-value-purple">{{ $teamStats['new_tasks'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">На доработке:</div>
                <div class="stat-value stat-value-yellow">{{ $teamStats['revision_tasks'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">На проверке:</div>
                <div class="stat-value">{{ $teamStats['review_tasks'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Просроченных задач:</div>
                <div class="stat-value stat-value-red">{{ $teamStats['overdue_tasks'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Процент выполнения:</div>
                <div class="stat-value stat-value-purple">{{ $teamStats['completion_rate'] }}%</div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: {{ $teamStats['completion_rate'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Эффективность команды</div>
        <div>
            @if($teamStats['completion_rate'] >= 70)
                <p>Отличный результат! Команда работает эффективно.</p>
            @elseif($teamStats['completion_rate'] >= 40)
                <p>Средний результат. Есть потенциал для улучшения.</p>
            @else
                <p>Низкий результат. Рекомендуется пересмотреть организацию работы.</p>
            @endif
        </div>
        
        <div class="progress-bar-container">
            <div class="progress-bar" style="width: {{ $teamStats['completion_rate'] }}%; background-color: 
                {{ $teamStats['completion_rate'] >= 70 ? '#10b981' : ($teamStats['completion_rate'] >= 40 ? '#f59e0b' : '#ef4444') }};">
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Статистика по участникам команды</div>
        @if(count($userStats) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Участник</th>
                        <th>Email</th>
                        <th>Всего задач</th>
                        <th>Выполнено</th>
                        <th>Просрочено</th>
                        <th>Выполнение (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userStats as $userId => $stats)
                        <tr>
                            <td>{{ $stats['name'] }}</td>
                            <td>{{ $stats['email'] }}</td>
                            <td>{{ $stats['total_tasks'] }}</td>
                            <td>{{ $stats['completed_tasks'] }}</td>
                            <td>{{ $stats['overdue_tasks'] }}</td>
                            <td>
                                <div>{{ $stats['completion_rate'] }}%</div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: {{ $stats['completion_rate'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>В команде пока нет участников.</p>
        @endif
    </div>

    <div class="footer">
        Отчет сгенерирован {{ now()->format('d.m.Y H:i:s') }}
    </div>
</body>
</html> 