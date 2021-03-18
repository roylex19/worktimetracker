<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>NIALCOM - Учет рабочего времени</title>
    <link href=" {{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="SHORTCUT ICON" href="/favicon.ico" type="image/x-icon">
</head>
<body>
<div id="app">
    <app></app>
</div>
<script src="{{ mix('js/app.js') }}"></script>
<script src="{{ mix('js/bootstrap.js') }}"></script>
</body>
</html>
