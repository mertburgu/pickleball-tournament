<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Tournament Form -->
<div class="container">
    <h2>Create a New Tournament</h2>
    <form method="POST" action="{{ route('tournament.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name">Tournament Name <strong style="color: darkred;">*</strong></label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter tournament name">
        </div>
        <div class="mb-3">
            <label for="game_format" class="form-label">Game Format <strong style="color: darkred;">*</strong></label>
            <select class="form-select" id="game_format" name="game_format"  required>
                @foreach(config('tournament.gameOptions') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="score_format" class="form-label">Score Format <strong style="color: darkred;">*</strong></label>
            <select class="form-select" id="score_format"  name="score_format" required>
                @foreach(config('tournament.scoreOptions') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="tournament_format" class="form-label">Tournament Type <strong style="color: darkred;">*</strong></label>
            <select class="form-select" id="tournament_format"  name="tournament_format" required>
                @foreach(config('tournament.typeOptions') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="average_game_time" class="form-label">Average Game Type <strong style="color: darkred;">*</strong></label>
            <select class="form-select" id="average_game_time" name="average_game_time" required>
                @foreach(config('tournament.timeOptions') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="number_of_courts">Court Count <strong style="color: darkred;">*</strong></label>
            <input type="number" class="form-control" id="number_of_courts" name="number_of_courts" min="1" max="999" required placeholder="Enter court count">
            <small class="form-text text-muted">Please enter a value between 1 and 999.</small>
        </div>
        <div class="mb-3">
            <label for="player_limit">Player Limit <strong style="color: darkred;">*</strong></label>
            <input type="number" class="form-control" id="player_limit" name="player_limit" min="2" max="999" required placeholder="Enter player limit">
            <small class="form-text text-muted">Please enter a value between 2 and 999.</small>
        </div>
        <button type="submit" class="btn btn-primary">Create Tournament</button>
    </form>
</div>

<!-- Bootstrap JS (at the end of body) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
