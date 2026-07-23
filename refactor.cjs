const fs = require('fs');
let file = 'c:/Users/hassa/Desktop/GDD/resources/views/livewire/statistics-index.blade.php';
let content = fs.readFileSync(file, 'utf8');

// 1. Extract the Growth Chart HTML block
const growthChartRegex = /<!-- Growth Chart -->[\s\S]*?<\/div>\s*<\/div>\n/;
let growthChartMatch = content.match(growthChartRegex);
if (!growthChartMatch) {
    console.error('Growth chart not found');
    process.exit(1);
}
let growthChartHtml = growthChartMatch[0];

// 2. Remove the original Growth Chart block from the document
content = content.replace(growthChartRegex, '');

// 3. Replace the Trend Chart HTML block with the Growth Chart HTML block
const trendChartRegex = /<!-- Trend -->[\s\S]*?<\/div>\n\s*<!-- Category Table -->/;
content = content.replace(trendChartRegex, growthChartHtml.trim() + '\n\n        <!-- Category Table -->');

// 4. Update the growthChart Alpine component in the script to look like a line chart instead of a bar chart
let growthAlpineRegex = /Alpine\.data\('growthChart'[\s\S]*?\}\)\);/;
let newGrowthAlpine = `Alpine.data('growthChart', (labels, values) => ({
        chart: null,
        labels: labels,
        values: values,
        isDark() { return document.documentElement.classList.contains('dark'); },
        init() {
            this.$watch('labels', () => { if(this.chart) this.renderChart(); });
            let check = () => {
                if (typeof echarts !== 'undefined') {
                    this.chart = echarts.init(this.$refs.container);
                    this.renderChart();
                    createObserver(this).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                } else { setTimeout(check, 100); }
            };
            check();
        },
        renderChart() {
            const dark = this.isDark();
            this.chart.setOption({
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: dark ? 'rgba(15, 23, 42, 0.92)' : 'rgba(255, 255, 255, 0.96)',
                    borderColor: dark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
                    borderWidth: 1,
                    padding: [12, 16],
                    textStyle: { color: dark ? '#e2e8f0' : '#334155', fontSize: 13, fontWeight: 500 },
                    extraCssText: 'border-radius: 12px; backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(0,0,0,' + (dark ? '0.4' : '0.12') + ');',
                    formatter: function(params) {
                        const v = params[0].value;
                        const icon = v === null ? '●' : v >= 0 ? '▲' : '▼';
                        const clr = v === null ? '#94a3b8' : v >= 0 ? '#10b981' : '#ef4444';
                        return '<div style="font-size:14px;font-weight:700;margin-bottom:6px">' + params[0].axisValueLabel + '</div>' +
                               '<div style="font-size:20px;font-weight:800;color:' + clr + '">' + icon + ' ' + (v === null ? '—' : (v >= 0 ? '+' : '') + v.toFixed(1) + '%') + '</div>';
                    },
                    axisPointer: {
                        type: 'line',
                        lineStyle: { color: dark ? 'rgba(16, 185, 129, 0.3)' : 'rgba(16, 185, 129, 0.2)', width: 1, type: 'dashed' }
                    }
                },
                grid: { left: '3%', right: '4%', bottom: '3%', top: '8%', containLabel: true },
                xAxis: {
                    type: 'category',
                    data: this.labels,
                    axisLine: { show: false },
                    axisTick: { show: false },
                    axisLabel: { fontSize: 11, color: dark ? '#475569' : '#94a3b8', fontWeight: 600 },
                    boundaryGap: false
                },
                yAxis: {
                    type: 'value',
                    splitLine: { lineStyle: { color: dark ? 'rgba(51, 65, 85, 0.3)' : '#f1f5f9', type: 'dashed' } },
                    axisLabel: {
                        fontSize: 11,
                        color: dark ? '#475569' : '#94a3b8',
                        fontWeight: 500,
                        formatter: function(v) { return v + '%'; }
                    }
                },
                animationDuration: 1500,
                animationEasing: 'cubicInOut',
                series: [{
                    type: 'line',
                    smooth: 0.4,
                    symbol: 'circle',
                    symbolSize: 8,
                    showSymbol: false,
                    data: this.values.map(v => v === null ? 0 : v),
                    lineStyle: {
                        color: '#10B981',
                        width: 3.5,
                        shadowColor: 'rgba(16, 185, 129, 0.35)',
                        shadowBlur: 12,
                        shadowOffsetY: 6
                    },
                    itemStyle: {
                        color: '#10B981',
                        borderColor: dark ? '#1e293b' : '#ffffff',
                        borderWidth: 3,
                        shadowColor: 'rgba(16, 185, 129, 0.5)',
                        shadowBlur: 10
                    },
                    areaStyle: {
                        color: {
                            type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                            colorStops: [
                                { offset: 0, color: 'rgba(16, 185, 129, 0.25)' },
                                { offset: 0.8, color: 'rgba(16, 185, 129, 0.02)' },
                                { offset: 1, color: 'rgba(16, 185, 129, 0)' }
                            ]
                        }
                    },
                    emphasis: { focus: 'series', itemStyle: { borderWidth: 4 } }
                }]
            });
        }
    }));`;

content = content.replace(growthAlpineRegex, newGrowthAlpine);

fs.writeFileSync(file, content, 'utf8');
console.log('done');
