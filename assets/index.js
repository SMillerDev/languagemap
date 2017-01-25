/**
 * Created by sean on 25/01/2017.
 */

d3.select(window).on("resize", throttle);

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
            if (key == undefined && countryInfo[d.properties.code] !== undefined){
                return "#777";
            }
            var e = (countryInfo[d.properties.code] == undefined || countryInfo[d.properties.code] == "") ? null : countryInfo[d.properties.code][key];
            switch (e) {
                case null:
                    return "#DDD";
                case "Yes":
                    return "#33cc1c";
                case "No":
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