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
<div class="container mt-4">
    <h2>Tournament List</h2>
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
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tournaments as $tournament)
            <tr>
                <td>{{ $tournament->name }}</td>
                <td>{{ $tournament->game_format }}</td>
                <td>{{ $tournament->score_format }}</td>
                <td>{{ $tournament->tournament_format }}</td>
                <td>{{ $tournament->average_game_time }}</td>
                <td>{{ $tournament->number_of_courts }}</td>
                <td>{{ $tournament->player_limit }}</td>
                <td>
                    <a href="{{ route('tournament.show', $tournament->id) }}" class="btn btn-primary btn-sm">Detail</a>
                    <a href="{{ route('tournament.edit', $tournament->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('tournament.destroy', $tournament->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
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
