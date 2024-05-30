'use strict';
$(function () {
    chart5(dateData);
});

function chart5(dateData) {
    var dynamicData = dateData.map(function (item) {
        return {
            period: item.keys[0],
            clicks: item.clicks,
            impressions: item.impressions,
            ctr: item.ctr,
            position: item.position
        };
    });

    var series = [
        { name: 'Clicks', type: 'line', data: dynamicData.map(function (item) { return item.clicks; }) },
        { name: 'Impressions', type: 'line', data: dynamicData.map(function (item) { return item.impressions; }) },
        { name: 'CTR', type: 'line', data: dynamicData.map(function (item) { return item.ctr; }) },
        { name: 'Position', type: 'line', data: dynamicData.map(function (item) { return item.position; }) }
    ];

    var labels = dynamicData.map(function (item) { return item.period; });

    var options = {
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: false
            }
        },
        series: series,
        title: {
            text: 'Traffic Sources'
        },
        labels: labels,
        xaxis: {
            type: 'datetime',
            labels: {
                style: {
                    colors: '#9aa0ac'
                }
            }
        },
        yaxis: [
            {
                title: {
                    text: 'Clicks/Impressions'
                },
                labels: {
                    style: {
                        color: '#9aa0ac'
                    }
                }
            },
            {
                opposite: true,
                title: {
                    text: 'CTR/Position'
                },
                labels: {
                    style: {
                        color: '#9aa0ac'
                    }
                }
            }
        ]
    };

    var chart = new ApexCharts(
        document.querySelector("#chart5"),
        options
    );

    chart.render();
}
