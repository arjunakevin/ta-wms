<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data['name'] }}</title>

    <style>
        body {
            margin: 30px;
            padding: 0;
        }

        * {
            font-family: serif;
        }

        table {
            width: 100%;
        }

        table, th, td {
            border: 1px solid #000;
            border-collapse: collapse;
        }

        th, td {
            padding: 5px 10px;
        }

        td {
            padding: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .header {
            background-color: #efefef;
        }

        .no-border {
            border: none;
        }
    </style>
</head>
<body>
    <table  class="no-border" style="margin-bottom: 10px">
        <thead>
            <tr>
                <th colspan="5" class="no-border" style="margin: 0; padding: 0"><h2>{{ $data['name'] }}</h2></th>
            </tr>
            <tr>
                <th class="no-border" rowspan="5">
                    {!! $data['qr'] !!}
                    <strong>{{ $data['id'] }}</strong>
                </th>
            </tr>
            <tr>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(1, $data['header'])) {{ $data['header'][1]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(1, $data['header'])) : {{ $data['header'][1]['value'] }} @endif</span></td>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(2, $data['header'])) {{ $data['header'][2]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(2, $data['header'])) : {{ $data['header'][2]['value'] }} @endif</span></td>
            </tr>
            <tr>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(3, $data['header'])) {{ $data['header'][3]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(3, $data['header'])) : {{ $data['header'][3]['value'] }} @endif</span></td>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(4, $data['header'])) {{ $data['header'][4]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(4, $data['header'])) : {{ $data['header'][4]['value'] }} @endif</span></td>
            </tr>
            <tr>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(5, $data['header'])) {{ $data['header'][5]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(5, $data['header'])) : {{ $data['header'][5]['value'] }} @endif</span></td>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(6, $data['header'])) {{ $data['header'][6]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(6, $data['header'])) : {{ $data['header'][6]['value'] }} @endif</span></td>
            </tr>
            <tr>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(7, $data['header'])) {{ $data['header'][7]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(7, $data['header'])) : {{ $data['header'][7]['value'] }} @endif</span></td>
                <th class="text-left no-border" style="width: 15%">@if (array_key_exists(8, $data['header'])) {{ $data['header'][8]['title'] }} @endif</th>
                <td class="text-left no-border" style="width: 35%; margin: 0; padding: 0"><span style="margin: 0">@if (array_key_exists(8, $data['header'])) : {{ $data['header'][8]['value'] }} @endif</span></td>
            </tr>
        </thead>
    </table>
    <hr style="border: 1px solid #000">
    <table>
        <thead>
            <tr>
                <th style="width: 5%" class="header text-center">No.</th>
                <th style="width: 20%" class="header text-left">Product Code</th>
                <th style="width: 45%" class="header text-left">Description</th>
                <th style="width: 15%" class="header text-right">Base Quantity</th>
                <th style="width: 15%" class="header text-left">UoM</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['details'] as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $detail['product_code'] }}</td>
                    <td class="text-left">{{ $detail['description'] }}</td>
                    <td class="text-right">{{ $detail['base_quantity'] }}</td>
                    <td class="text-left">{{ $detail['uom_name'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>