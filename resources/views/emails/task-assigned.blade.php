<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâche assignée</title>
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
            border-left: 4px solid #667eea;
        }
        .task-info h3 {
            margin-top: 0;
            color: #667eea;
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
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="icon">📋</div>
        <h1>Nouvelle Tâche Assignée</h1>
    </div>

    <div class="content">
        <p>Bonjour,</p>

        <p>Une nouvelle tâche vous a été assignée par <strong>{{ $assignedBy->name }}</strong>.</p>

        <div class="task-info">
            <h3>{{ $task->name }}</h3>

            <div class="info-row">
                <span class="label">Description :</span><br>
                {{ $task->description ?? 'Aucune description' }}
            </div>

            <div class="info-row">
                <span class="label">Durée estimée :</span>
                {{ $task->expected_minutes }} minutes
            </div>

            @if($task->start_at)
            <div class="info-row">
                <span class="label">Date de début :</span>
                {{ $task->start_at->format('d/m/Y à H:i') }}
            </div>
            @endif

            <div class="info-row">
                <span class="label">Statut :</span>
                @if($task->active)
                    <span style="color: #28a745; font-weight: bold;">Active</span>
                @else
                    <span style="color: #6c757d;">Inactive</span>
                @endif
            </div>
        </div>

        <p>Vous pouvez consulter cette tâche dans votre espace de travail.</p>

        <p>Cordialement,<br>
        <strong>Système de Gestion des Tâches</strong></p>
    </div>
</body>
</html>
