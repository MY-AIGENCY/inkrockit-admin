<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>InkRockIt</title>
    </head>
    <body onload="javascript: print();">
        <div style="width: 1400px; margin: auto;">
            <img src="/files/fedex/png/<?= $content ?>.png" style="
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg); /* Firefox */
            filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3); /* Internet Explorer */
            -o-transform: rotate(-90deg);">
        </div>
    </body>
</html>