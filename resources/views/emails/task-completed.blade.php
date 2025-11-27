<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâche terminée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .task-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .task-info h3 {
            margin-top: 0;
            color: #28a745;
        }
        .info-row {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #6c757d;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #17a2b8;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 5px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="icon">✅</div>
        <h1>Tâche Terminée</h1>
    </div>

    <div class="content">
        <p>Bonjour,</p>

        <p>Une tâche vient d'être marquée comme terminée par <strong>{{ $completedBy->name }}</strong>.</p>

        <div class="task-info">
            <h3>{{ $task->name }}</h3>

            <div class="info-row">
                <span class="label">Description :</span><br>
                {{ $task->description ?? 'Aucune description' }}
            </div>

            <div class="info-row">
                <span class="label">Durée prévue :</span>
                {{ $task->expected_minutes }} minutes
            </div>

            <div class="info-row">
                <span class="label">Date de début :</span>
                {{ $task->start_at ? $task->start_at->format('d/m/Y à H:i') : 'Non planifiée' }}
            </div>

            <div class="info-row">
                <span class="label">Terminée le :</span>
                {{ $task->completed_at ? $task->completed_at->format('d/m/Y à H:i') : now()->format('d/m/Y à H:i') }}
            </div>

            <div class="info-row">
                <span class="label">Équipes :</span><br>
                @foreach($task->teams as $team)
                    <span class="badge">{{ $team->name }}</span>
                @endforeach
            </div>
        </div>

        <p>Cordialement,<br>
        <strong>Système de Gestion des Tâches</strong></p>
    </div>
</body>
</html>
