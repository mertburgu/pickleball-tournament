<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div class="container mt-4">
    <h2>Tournament Details - {{ $tournament->name }}</h2>

    <div class="mb-3">
        <strong>Name:</strong> {{ $tournament->name }}
    </div>
    <div class="mb-3">
        <strong>Game Format:</strong> {{ config('tournament.gameOptions')[$tournament->game_format] }}
    </div>
    <div class="mb-3">
        <strong>Score Format:</strong> {{ config('tournament.scoreOptions')[$tournament->score_format] }}
    </div>
    <div class="mb-3">
        <strong>Tournament Format:</strong> {{ config('tournament.typeOptions')[$tournament->tournament_format] }}
    </div>
    <div class="mb-3">
        <strong>Average Game Time:</strong> {{ config('tournament.timeOptions')[$tournament->average_game_time] }}
    </div>
    <div class="mb-3">
        <strong>Number of Courts:</strong> {{ $tournament->number_of_courts }}
    </div>
    <div class="mb-3">
        <strong>Player Limit:</strong> {{ $tournament->player_limit }}
    </div>
    @if ($tournament->started)
        <button class="btn btn-success" disabled>Tournament Started</button>
    @else
        <form action="{{ route('tournament.start', $tournament->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Start Tournament</button>
        </form>
    @endif
    <hr>
    <a href="{{ route('tournament.index') }}" class="btn btn-primary">Back to List</a>

    @if (!$tournament->started)
        <a href="{{ route('tournament.edit', $tournament->id) }}" class="btn btn-warning">Edit</a>
    @endif
</div>

<!-- Bootstrap JS (at the end of body) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
