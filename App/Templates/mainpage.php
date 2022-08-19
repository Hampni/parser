<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
    <title>Weather Forecast</title>
</head>
<body>

<div class="text-center" style="margin-top: 100px; margin-bottom: 100px">
    <p>
    <form method="post" action="" id="weather">
        <input name="city" list="cities" type="text" id="citiesInput" value="Киев">
        <datalist id="cities">
            <?php
            foreach ($this->data['cities'] as $city) {
                echo '<option value="' . $city->name . '"></option>';
            }
            ?>
        </datalist>
        <input type="submit" value="Get weather forecast">
    </form>
    </p>
</div>

<div class="text-center">
    <p>
    <h3 class="error"> </h3>
    <p></p>
    <div id="sheets" style="max-width: 704px;display: inline-block; height: fit-content; border: 2px solid #000000">
    </div>
    </p>
</div>

<script src="/Public/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>