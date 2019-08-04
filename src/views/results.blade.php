<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laravel Request Benchmark</title>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/4d74ee54fc.js"></script>
    <style>
        i {
            font-size: 0.8em;
        }

        .fill {
            min-height: 100vh;
            min-width: 100vw;
            width: 100vw;
        }
    </style>
</head>
<body>
@php
    $row = 1;

    function isBest(string $request, string $timestamp, $bestRequests) {
        if (isset($bestRequests[$request])) {
            return $bestRequests[$request]->timestamp === $timestamp;
        }

        return false;
    }

    function isWorst(string $request, string $timestamp, $worstRequests) {
        if (isset($worstRequests[$request])) {
            return $worstRequests[$request]->timestamp === $timestamp;
        }

        return false;
    }
@endphp
<div class="table-responsive fill">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Request</th>
            <th>Time</th>
            <th>Pre memory usage</th>
            <th>Post memory usage</th>
            <th></th>
        </tr>
        </thead>
        @forelse ($requests as $request => $data)
            <tbody>
            <tr class="clickable"
                data-toggle="collapse"
                data-target="#request-{{$row}}"
                aria-expanded="false"
                aria-controls="request-{{$row}}"
            >
                <td>
                    <i class="fas fa-hashtag"></i>
                    {{$request}}
                </td>
                <td>
                    {{$data['time']}}
                </td>
                <td>
                    {{$data['pre_memory_usage']}}
                </td>
                <td>
                    {{$data['post_memory_usage']}} ({{$data['actual_memory_usage']}})
                </td>

                <td>
                    @if (isset($history[$request]))
                        <i class="far fa-calendar"></i>
                    @endif
                </td>
            </tr>
            </tbody>
            @if (isset($history[$request]))
                <tbody id="request-{{$row}}"
                       class="collapse"
                >
                @foreach ($history[$request] as $timestamp => $data)
                    @if (isBest($request, $timestamp, $bestRequests))
                        <tr class="bg-success">
                    @elseif (isWorst($request, $timestamp, $worstRequests))
                        <tr class="bg-danger">
                    @else
                        <tr>
                    @endif
                        <td>
                            &nbsp;
                            <i class="far fa-clock"></i>
                            {{$timestamp}}
                        </td>
                        <td>
                            {{$data['time']}}
                        </td>
                        <td>
                            {{$data['pre_memory_usage']}}
                        </td>
                        <td>
                            {{$data['post_memory_usage']}} ({{$data['actual_memory_usage']}})
                        </td>
                        <td></td>
                    </tr>
                @endforeach
                </tbody>
            @endif

            @php
                $row++;
            @endphp
        @empty
            <tbody>
            <tr>
                <td>...</td>
                <td>...</td>
                <td>...</td>
                <td>...</td>
                <td></td>
            </tr>
            </tbody>
        @endforelse
    </table>
</div>
</body>
</html>
