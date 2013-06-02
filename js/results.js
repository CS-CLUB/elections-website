/*
 * CS-CLUB Elections Website
 *
 * Copyright (C) 2013 Jonathan Gillett, Joseph Heron, Computer Science Club at DC and UOIT
 * All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* The root URL for REST api */
var rootURL = "http://election.dom/api";


$(document).ready(function () {
	/* Display the election results when page is loaded */
	if (window.location.pathname.match(/results\.php/))
	{
		voteBreakdownAll();
	}
});

/* Get the postfix identifier based on the position */
function getId(prefix, position) {
    /* Get the name of the id based on position */
    switch (position) {
        case 'President':
            prefix += 'president';
            break;
        case 'Vice President':
            prefix += 'vicepresident';
            break;
        case 'Coordinator':
            prefix += 'coordinator';
            break;
        case 'Treasurer':
            prefix += 'treasurer';
            break;
        default:
            prefix = '';
            break;
    }
    return prefix;
}

/* Get the vote breakdown for an election for all positions */
function voteBreakdownAll() {
    console.log('voteBreakdownAll');
    $.ajax({
        type: 'GET',
        url: rootURL + '/results/election',
        dataType: "json", // data type of response
        success: plotVoteBreakdown
    });
}

/* Creates a pie chart for the vote break down of each position */
function plotVoteBreakdown(data) {
    console.log('plotVoteBreakdown');
    console.log(data);
    var idPrefix = 'pie_';

    /* Create a separate pie chart for each position */
    $.each(data, function(position, value) {
        var results = value == null ? [] : (value instanceof Array ? value : [value]);
        plotPieChart(getId(idPrefix, position), position, results);
    });
}

/* Plot the results as a pie chart */
function plotPieChart(id, title, results) {
    var chart;
    var options = {
        chart: {
            renderTo: id,
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            backgroundColor:'rgba(255, 255, 255, 0.1)'
        },
        title: {
            text: title
        },
        tooltip: {
            formatter: function() {
                var s;
                if (this.point.name) { // the pie chart
                    s = this.point.name + ': <b>' + this.y + ' Votes</b>';
                } 
                else {
                    s = this.x  + ': ' + this.y;
                }
                return s;
            }
            /*
            pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            percentageDecimals: 1
            */
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    
                    
                    formatter: function() {
                        return '<b>' + this.point.name +'</b><br />' + '<b>' + this.percentage +' %</b>';
                    }
                    /*
                    formatter: function() {
                        var s;
                        if (this.point.name) { // the pie chart
                            s = this.point.name + ': <b>' + this.y + '</b> Votes';
                        } 
                        else {
                            s = this.x  + ': ' + this.y;
                        }
                        return s;
                    }*/
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Vote Breakdown',
            data: []
        }]
    };

    /* Get the enrollment data from the REST api using AJAX and add it to the series */
    console.log('plotPieChart');
    console.log(id);
    console.log(title);
    console.log(results);
    /* Sort the results based on the number of votes */
    results = results.sort(function(a, b) {
        return b.value - a.value;
    });

    /* Add the name and votes to the data series */
    var first = true;

    $.each(results, function(index, candidate) {
        /* Add a cut-out in the pie chart for the first entry */
        if (first) {
            options.series[0].data.push({
                name: candidate.name,
                y: candidate.votes,
                sliced: true,
                selected: true
            })
            first = false;
        } 
        else {
            options.series[0].data.push([candidate.name, candidate.votes]);
        }
        console.log([candidate.name, candidate.votes]);

    });

    chart = new Highcharts.Chart(options);
}