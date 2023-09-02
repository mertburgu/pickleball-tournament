<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container mt-4">
    <h2>Tournament List</h2>
    <a href="{{ route('tournament.create') }}" class="btn btn-success mb-3 float-end">Create Tournament</a>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Game Format</th>
            <th>Score Format</th>
            <th>Tournament Format</th>
            <th>Average Game Time</th>
            <th>Number of Courts</th>
            <th>Player Limit</th>
            <th>Matches in Progress</th>
            <th>Completed Matches</th>
            <th>Remaining Matches</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tournaments as $tournament)
            <tr>
                <td>{{ $tournament->name }}</td>
                <td>{{ config('tournament.gameOptions.' . $tournament->game_format, 'Unknown') }}</td>
                <td>{{ config('tournament.scoreOptions.' . $tournament->score_format, 'Unknown') }}</td>
                <td>{{ config('tournament.typeOptions.' . $tournament->tournament_format, 'Unknown') }}</td>
                <td>{{ config('tournament.timeOptions.' . $tournament->average_game_time, 'Unknown') }}</td>
                <td>{{ $tournament->number_of_courts }}</td>
                <td>{{ $tournament->player_limit }}</td>
                <td>{{ $tournament->matchesInProgress }}</td>
                <td>{{ $tournament->completedMatches }}</td>
                <td>{{ $tournament->remainingMatches }}</td>
                <td>
                    <a href="{{ route('tournament.show', $tournament->id) }}" class="btn btn-primary btn-sm">Detail</a>
                    @if (!$tournament->started)
                        <a href="{{ route('tournament.edit', $tournament->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('tournament.destroy', $tournament->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    @else
                        <button class="btn btn-warning btn-sm" disabled>Edit</button>
                        <button class="btn btn-danger btn-sm" disabled>Delete</button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<!-- Bootstrap JS (at the end of body) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
