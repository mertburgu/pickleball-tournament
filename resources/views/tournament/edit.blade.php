<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tournament</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Tournament</h2>
    <form method="POST" action="{{ route('tournament.update', $tournament->id) }}">
        @csrf
        @method('PUT') <!-- HTTP PUT metodu ile güncelleme işlemi -->

        <div class="mb-3">
            <label for="name">Tournament Name <strong style="color: darkred;">*</strong></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $tournament->name }}" required>
        </div>
        <div class="mb-3">
            <label for="game_format" class="form-label">Game Format <strong style="color: darkred;">*</strong></label>
            <select disabled class="form-select" id="game_format" name="game_format" required>
                @foreach(config('tournament.gameOptions') as $key => $value)
                    <option value="{{ $key }}" {{ $key == $tournament->game_format ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="score_format" class="form-label">Score Format <strong style="color: darkred;">*</strong></label>
            <select class="form-select" id="score_format"  name="score_format" required>
                @foreach(config('tournament.scoreOptions') as $key => $value)
                    <option value="{{ $key }}" {{ $key == $tournament->score_format ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="tournament_format" class="form-label">Tournament Type <strong style="color: darkred;">*</strong></label>
            <select class="form-select" id="tournament_format"  name="tournament_format" required>
                @foreach(config('tournament.typeOptions') as $key => $value)
                    <option value="{{ $key }}" {{ $key == $tournament->tournament_format ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="average_game_time" class="form-label">Average Game Type <strong style="color: darkred;">*</strong></label>
            <select class="form-select" id="average_game_time" name="average_game_time" required>
                @foreach(config('tournament.timeOptions') as $key => $value)
                    <option value="{{ $key }}" {{ $key == $tournament->average_game_time ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="number_of_courts">Court Count <strong style="color: darkred;">*</strong></label>
            <input readonly type="number" class="form-control" id="number_of_courts" name="number_of_courts" min="1" max="999" value="{{ $tournament->number_of_courts }}" required>
            <small class="form-text text-muted">Please enter a value between 1 and 999.</small>
        </div>
        <div class="mb-3">
            <label for="player_limit">Player Limit <strong style="color: darkred;">*</strong></label>
            <input readonly type="number" class="form-control" id="player_limit" name="player_limit" min="2" max="999" value="{{ $tournament->player_limit }}" required>
            <small class="form-text text-muted">Please enter a value between 2 and 999.</small>
        </div>
        <button type="submit" class="btn btn-success">Update Tournament</button>
    </form>
    <hr>
    <a href="{{ route('tournament.index') }}" class="btn btn-primary">Back to List</a>

</div>
<!-- Bootstrap JS (at the end of body) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
