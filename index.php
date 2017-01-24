<?php $array = array_map('str_getcsv', file('data/info.csv'));
$newarr      = [];
$headers     = $array[0];
unset($array[0]);
foreach ($array as $item)
{
    foreach ($item as $key => $value)
    {
        $item[$headers[$key]] = $value;
        unset($item[$key]);
    }
    $newarr[$item['Country']] = $item;
}
$json = json_encode($newarr);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Language info</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .country:hover {
            stroke: #fff;
            stroke-width: 1.5px;
        }

        .text {
            font-size: 10px;
            text-transform: capitalize;
        }

        #container {
            margin: 10px 0;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.4) inset;
            height: 100%;
            overflow: hidden;
            background: #F0F8FF;
        }

        .hidden {
            display: none;
        }

        div.tooltip {
            color: #222;
            background: #fff;
            padding: .5em;
            text-shadow: #f5f5f5 0 1px 0;
            border-radius: 2px;
            box-shadow: 0px 0px 2px 0px #a6a6a6;
            opacity: 0.9;
            position: absolute;
        }

        h5 {
            margin: 0;
        }

    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Language info</h1>

    <div id="container"></div>
</div>
<script src="//d3js.org/d3.v4.min.js"></script>
<script src="//d3js.org/topojson.v1.min.js"></script>


<script>
    d3.select(window).on("resize", throttle);
    var countryInfo = <?= $json;?>;

    var zoom = d3.zoom()
    //.extent([1,9])
        .scaleExtent([1, 9])
        .on("zoom", move);

    var c = document.getElementById('container');
    var width = c.offsetWidth;
    var height = width / 2;

    //offsets for tooltips
    var offsetL = c.offsetLeft + 20;
    var offsetT = c.offsetTop + 10;

    var topo, projection, path, svg, g;

    var tooltip = d3.select("#container").append("div").attr("class", "tooltip hidden");

    setup(width, height);

    function setup(width, height) {
        //projection = d3.geo.mercator()
        projection = d3.geoMercator()
            .translate([(width / 2), (height / 2)])
            .scale(width / 2 / Math.PI);

        //path = d3.geo.path().projection(projection);
        path = d3.geoPath().projection(projection);

        svg = d3.select("#container").append("svg")
            .attr("width", width)
            .attr("height", height)
            .call(zoom)
            //.on("click", click)
            .append("g");

        g = svg.append("g")
            .on("click", click);

    }

    d3.json("data/world.min.json", function (error, world) {
        topo = topojson.feature(world, world.objects.countries).features;
        draw(topo);
    });

    function hoverForInfo(info) {
        if (info == undefined) return "";

        var html = "<table class='table'>";
        html += "<tr><th>" + info['Title'] + "</th><th></th></tr>";
        html += "<tr><td>Bath words: </td><td>" + info['Bath words'] + "</td></tr>";
        html += "<tr><td>Circle: </td><td>" + info['Circle'] + "</td></tr>";
        html += "<tr><td>FORCE-NORTH contrast: </td><td>" + info['FORCE-NORTH contrast'] + "</td></tr>";
        html += "<tr><td>STRUT/FOOT contrast: </td><td>" + info['STRUT/FOOT contrast'] + "</td></tr>";
        html += "<tr><td>H-dropping: </td><td>" + info['H-dropping'] + "</td></tr>";
        html += "<tr><td>Happy words: </td><td>" + info['Happy words'] + "</td></tr>";
        html += "<tr><td>Medial /t/ glottalisation: </td><td>" + info['Medial /t/ glottalisation'] + "</td></tr>";
        html += "<tr><td>Rhoticity: </td><td>" + info['Rhoticity'] + "</td></tr>";
        html += "</table>";
        return html;
    }
    function handleMouseOver() {
        var mouse = d3.mouse(svg.node()).map(function (d) {
            return parseInt(d);
        });

        tooltip.classed("hidden", false)
            .attr("style", "left:" + (mouse[0] + offsetL) + "px;top:" + (mouse[1] + offsetT) + "px")
            .html(hoverForInfo(this.__data__.properties.info));
    }

    function handleMouseOut() {
        tooltip.classed("hidden", true);
    }


    function draw(topo) {
        var country = g.selectAll(".country").data(topo);
        console.log(countryInfo);
        country.enter().insert("path")
            .attr("class", "country")
            .attr("d", path)
            .attr("id", function (d, i) {
                return d.id;
            })
            .attr("title", function (d, i) {
                d.properties.info = countryInfo[d.properties.code];
                return d.properties.name;
            })
            .style("fill", function (d, i) {
                var e = (countryInfo[d.properties.code] == undefined) ? null : countryInfo[d.properties.code]["H"];
                switch (e) {
                    case null:
                        return "#DDD";
                    case "yes":
                        return "#33cc1c";
                    case "no":
                        return "#cc2b27";
                }
            })
            .on("mouseover", handleMouseOver)
            .on("mouseout", handleMouseOut);
    }


    function redraw() {
        width = c.offsetWidth;
        height = width / 2;
        d3.select('svg').remove();
        setup(width, height);
        draw(topo);
    }


    function move() {
        //var t = d3.event.translate;
        var t = [d3.event.transform.x, d3.event.transform.y];
        //var s = d3.event.scale;
        var s = d3.event.transform.k;
        zscale = s;
        var h = height / 4;

        t[0] = Math.min(
            (width / height) * (s - 1),
            Math.max(width * (1 - s), t[0])
        );

        t[1] = Math.min(
            h * (s - 1) + h * s,
            Math.max(height * (1 - s) - h * s, t[1])
        );

        //zoom.translateBy(t);
        g.attr("transform", "translate(" + t + ")scale(" + s + ")");

        //adjust the country hover stroke width based on zoom level
        d3.selectAll(".country").style("stroke-width", 1.0 / s);

    }

    var throttleTimer;
    function throttle() {
        window.clearTimeout(throttleTimer);
        throttleTimer = window.setTimeout(function () {
            redraw();
        }, 200);
    }


    //geo translation on mouse click in map
    function click() {
        var latlon = projection.invert(d3.mouse(this));
        console.log(latlon);
    }


    //function to add points and text to the map (used in plotting capitals)
    function addpoint(lon, lat, text) {

        var gpoint = g.append("g").attr("class", "gpoint");
        var x = projection([lon, lat])[0];
        var y = projection([lon, lat])[1];

        gpoint.append("svg:circle")
            .attr("cx", x)
            .attr("cy", y)
            .attr("class", "point")
            .attr("r", 1.5);

        //conditional in case a point has no associated text
        if (text.length > 0) {

            gpoint.append("text")
                .attr("x", x + 2)
                .attr("y", y + 2)
                .attr("class", "text")
                .text(text);
        }

    }
</script>
</body>
</html>
