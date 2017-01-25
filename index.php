<?php
require_once 'src/Data/CsvParser.php';
$data    = \LanguageMap\Data\CsvParser::parse_file(file('data/info.csv'));
$headers = array_keys($data[array_rand($data)]);
$id      = intval($_GET['id'] ?? '0');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Language info</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/index.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="#">Home</a></li>
        <?php foreach ($headers as $key => $header):
            if (in_array($header, ['Country', 'Title', 'Region']))
            {
                continue;
            } ?>
            <li role="presentation"><a href="index.php?id=<?php echo $key; ?>"><?php echo $header; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <h1>Language info</h1>
    <div id="container"></div>
</div>
<script>
    var countryInfo = <?php echo json_encode($data);?>;
    <?php if($id !== 0):?>
    var key = "<?php echo $headers[$id];?>";
    <?php endif;?>
</script>
<script src="//d3js.org/d3.v4.min.js"></script>
<script src="//d3js.org/topojson.v1.min.js"></script>
<script src="assets/index.js"></script>
</body>
</html>
