<?php
require_once 'src/Data/CsvParser.php';
$data    = \LanguageMap\Data\CsvParser::parse_file(file('data/info.csv'));
$headers = array_diff(array_keys($data[array_rand($data)]), ['Country', 'Title', 'Region', 'Region Code']);
$id      = intval($_GET['id'] ?? '0');
$country = $_GET['country'] ?? 'world';
$units   = [
    'world' => [
        'subunit'  => 'countries',
        'selector' => 'code',
    ],
    'UK'    => [
        'subunit'  => 'subunits',
        'selector' => 'GU_A3',
    ],
    'US'    => [
        'subunit'  => 'states',
        'selector' => 'code',
    ],
];
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
        <li role="presentation" <?php if ($id === 0): ?>class="active"<?php endif; ?>><a href="index.php">Home</a></li>
        <?php foreach ($headers as $key => $header): ?>
            <li role="presentation" <?php if ($id === $key): ?>class="active"<?php endif; ?>><a
                        href="index.php?id=<?php echo $key; ?>&country=<?php echo $country; ?>"><?php echo $header; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
    <h1>Language info
        <small class="pull-right">
            <a href="index.php?id=<?php echo $key; ?>">World</a>,
            <a href="index.php?id=<?php echo $key; ?>&country=UK">UK</a>,
            <a href="index.php?id=<?php echo $key; ?>&country=US">US</a>
        </small>
    </h1>
    <div id="container"></div>
</div>
<script>
    var countryInfo = <?php echo json_encode($data);?>;
    var headers = <?php echo json_encode(array_values($headers));?>;
    var map = "<?php echo $country;?>";
    <?php foreach ($units[$country] as $key=>$value):?>
    var <?php echo $key;?> = "<?php echo $value;?>";
    <?php endforeach;?>
    <?php if($id !== 0):?>
    var key = "<?php echo $headers[$id];?>";
    <?php endif;?>
</script>
<script src="//d3js.org/d3.v4.min.js"></script>
<script src="//d3js.org/topojson.v2.min.js"></script>
<script src="assets/<?php echo $country;?>.js"></script>
</body>
</html>
