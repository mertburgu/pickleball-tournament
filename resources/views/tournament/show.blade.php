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

    <div class="row">
        <!-- Sol Kolon -->
        <div class="col-md-3">
            <h3>{{ $tournament->name }} - Details </h3>

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
        </div>

        <!-- Sağ Kolon -->
        <div class="col-md-9">
            <h3>Game List</h3>
            <button id="start-games" class="btn btn-primary float-end">Start Games</button>
            <table class="table">
                <thead>
                <tr>
                    <th>Game</th>
                    <th>Status</th>
                    <th>Elapsed Time</th>
                    <th>Result</th> <!-- Sonuç sütunu eklendi -->
                </tr>
                </thead>
                <tbody id="game-list">
                @foreach ($tournament->games as $game)
                    <tr class="game-row" data-game-id="{{ $game->id }}" data-game-status="{{ $game->gameTracking->status ?? 'Not Started' }}">
                        <td>Game {{ $game->id }}</td>
                        <td>{{ $game->gameTracking->status ?? 'Not Started' }}</td>
                        <td class="remaining-time" data-game-id="{{ $game->id }}" data-game-status="{{ $game->gameTracking->status ?? 'Not Started' }}" data-game-start-time="{{ $game->gameTracking->start_time ?? '' }}" data-game-average-time="{{ $tournament->average_game_time }}"></td>
                        <td>
                            @if ($game->gameTracking->status == 'completed' && $game->result)
                                {{$game->result->score}}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @if (!$tournament->started)
        <a href="{{ route('tournament.edit', $tournament->id) }}" class="btn btn-warning">Edit</a>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        checkGameStatus();

        $('#start-games').click(function (e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route("tournament.start-games", $tournament->id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
            });
            location.reload();

        });

        setInterval(function () {
            updateGameDurations();
        }, 1000);
    });

    function updateGameDurations() {
        $('.remaining-time').each(function () {
            var remainingTime = $(this);
            var gameId = remainingTime.data('game-id');
            var gameStatus = remainingTime.data('game-status');
            var gameStartTime = new Date(remainingTime.data('game-start-time'));
            var gameAverageTime = parseInt(remainingTime.data('game-average-time'));
            var currentTime = new Date();

            if (gameStatus === 'ongoing') {
                var elapsedTimeInSeconds = Math.floor((currentTime - gameStartTime) / 1000);

                var gameEndTimeInSeconds = gameStartTime.getSeconds() + gameAverageTime;

                var remainingTimeInSeconds = gameEndTimeInSeconds - elapsedTimeInSeconds;

                var remainingMinutes = Math.floor(remainingTimeInSeconds / 60);
                var remainingSeconds = remainingTimeInSeconds % 60;

                remainingTime.text(remainingMinutes + ' dakika ' + remainingSeconds + ' saniye');
            }
        });
    }
    setInterval(function () {
        location.reload();
    }, 60000);

    function checkGameStatus() {
        // Tüm oyun satırlarını dön
        $('.game-row').each(function () {
            var gameRow = $(this);
            var gameStatus = gameRow.data('game-status');

            // Eğer bir oyun başladı veya oynanıyorsa Start Games düğmesini devre dışı bırak
            if (gameStatus === 'ongoing' || gameStatus === 'started') {
                $('#start-games').attr('disabled', true);
                return false; // Döngüyü sonlandır
            } else {
                $('#start-games').attr('disabled', false);
            }
        });
    }
</script>
</body>
</html>
