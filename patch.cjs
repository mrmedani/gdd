const fs = require('fs');
let file = 'c:/Users/hassa/Desktop/GDD/resources/views/livewire/statistics-index.blade.php';
let content = fs.readFileSync(file, 'utf8');

content = content.replace(/<div wire:key=\"cat-chart\" class=\"relative h-64 w-full md:w-1\/2\"[\s\S]*?x-effect=\"[\s\S]*?\">/m, 
    '<div wire:key=\"cat-chart\" class=\"relative h-64 w-full md:w-1/2\"\n                             x-data=\"categoryChart(@js(array_column($expensesByCategory, \'label\')), @js(array_column($expensesByCategory, \'total\')), @js(array_column($expensesByCategory, \'color\')))\">'
);

content = content.replace(/<div wire:key=\"trend-chart-stat\" class=\"relative h-72 w-full\"[\s\S]*?x-effect=\"[\s\S]*?\">/m, 
    '<div wire:key=\"trend-chart-stat\" class=\"relative h-72 w-full\"\n                     x-data=\"trendChart(@js(array_column($monthlyTrend, \'month\')), @js(array_column($monthlyTrend, \'total\')))\">'
);

content = content.replace(/<div wire:key=\"growth-chart\" class=\"relative h-72 w-full\"[\s\S]*?x-effect=\"[\s\S]*?\">/m, 
    '<div wire:key=\"growth-chart\" class=\"relative h-72 w-full\"\n                 x-data=\"growthChart(@js(array_column($growthTrend, \'label\')), @js(array_column($growthTrend, \'rate\')))\">'
);

let alpineScripts = `
<script>
document.addEventListener('alpine:init', () => {
    function createObserver(component) {
        return new MutationObserver(() => { 
            if (component.chart) { 
                component.chart.dispose(); 
                component.chart = echarts.init(component.$refs.container); 
                component.renderChart(); 
            } 
        });
    }

    Alpine.data('categoryChart', (labels, values, colors) => ({
        chart: null,
        labels: labels,
        values: values,
        colors: colors,
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
            const total = this.values.reduce((a, b) => a + b, 0);
            this.chart.setOption({
                tooltip: {
                    trigger: 'item',
                    backgroundColor: dark ? 'rgba(15, 23, 42, 0.92)' : 'rgba(255, 255, 255, 0.96)',
                    borderColor: dark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
                    borderWidth: 1,
                    padding: [12, 16],
                    textStyle: { color: dark ? '#e2e8f0' : '#334155', fontSize: 13, fontWeight: 500 },
                    extraCssText: 'border-radius: 12px; backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(0,0,0,' + (dark ? '0.4' : '0.12') + ');',
                    formatter: function(params) {
                        const pct = total > 0 ? ((params.value / total) * 100).toFixed(1) : 0;
                        return '<div style="font-size:14px;font-weight:700;margin-bottom:4px">' + params.marker + ' ' + params.name + '</div>' +
                               '<div style="font-size:13px;color:' + (dark ? '#94a3b8' : '#64748b') + '">' + new Intl.NumberFormat().format(params.value) + ' DZD <span style="float:right;font-weight:700;color:' + (dark ? '#e2e8f0' : '#1e293b') + ';margin-left:12px">' + pct + '%</span></div>';
                    }
                },
                legend: { show: false },
                graphic: [{
                    type: 'text',
                    left: 'center',
                    top: '42%',
                    style: {
                        text: new Intl.NumberFormat().format(total),
                        fontSize: 20,
                        fontWeight: 800,
                        fill: dark ? '#f1f5f9' : '#1e293b',
                        fontFamily: 'Instrument Sans, system-ui, sans-serif'
                    }
                }, {
                    type: 'text',
                    left: 'center',
                    top: '54%',
                    style: {
                        text: 'DZD',
                        fontSize: 11,
                        fontWeight: 600,
                        fill: dark ? '#64748b' : '#94a3b8',
                        fontFamily: 'Instrument Sans, system-ui, sans-serif'
                    }
                }],
                animationDuration: 1200,
                animationEasing: 'cubicInOut',
                series: [{
                    type: 'pie',
                    radius: ['52%', '78%'],
                    center: ['50%', '50%'],
                    avoidLabelOverlap: true,
                    label: { show: false },
                    emphasis: {
                        scale: true,
                        scaleSize: 8,
                        itemStyle: {
                            shadowBlur: 20,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.25)'
                        }
                    },
                    itemStyle: {
                        borderRadius: 8,
                        borderColor: dark ? '#0f172a' : '#ffffff',
                        borderWidth: 3
                    },
                    data: this.labels.map((label, i) => ({
                        value: this.values[i],
                        name: label,
                        itemStyle: { color: this.colors[i] }
                    }))
                }]
            });
        }
    }));

    Alpine.data('trendChart', (labels, values) => ({
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
                        return '<div style="font-size:14px;font-weight:700;margin-bottom:6px">' + params[0].axisValueLabel + '</div>' +
                               '<div style="font-size:22px;font-weight:800;color:#6366F1">' + new Intl.NumberFormat().format(params[0].value) + ' <span style="font-size:12px;font-weight:600;color:' + (dark ? '#64748b' : '#94a3b8') + '">DZD</span></div>';
                    },
                    axisPointer: {
                        type: 'line',
                        lineStyle: { color: dark ? 'rgba(99, 102, 241, 0.3)' : 'rgba(99, 102, 241, 0.2)', width: 1, type: 'dashed' }
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
                        formatter: function(v) { return new Intl.NumberFormat('en', { notation: 'compact' }).format(v); }
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
                    data: this.values,
                    lineStyle: {
                        color: '#6366F1',
                        width: 3.5,
                        shadowColor: 'rgba(99, 102, 241, 0.35)',
                        shadowBlur: 12,
                        shadowOffsetY: 6
                    },
                    itemStyle: {
                        color: '#6366F1',
                        borderColor: dark ? '#1e293b' : '#ffffff',
                        borderWidth: 3,
                        shadowColor: 'rgba(99, 102, 241, 0.5)',
                        shadowBlur: 10
                    },
                    areaStyle: {
                        color: {
                            type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                            colorStops: [
                                { offset: 0, color: 'rgba(99, 102, 241, 0.25)' },
                                { offset: 0.8, color: 'rgba(99, 102, 241, 0.02)' },
                                { offset: 1, color: 'rgba(99, 102, 241, 0)' }
                            ]
                        }
                    },
                    emphasis: { focus: 'series', itemStyle: { borderWidth: 4 } }
                }]
            });
        }
    }));

    Alpine.data('growthChart', (labels, values) => ({
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
                        type: 'shadow',
                        shadowStyle: { color: dark ? 'rgba(99, 102, 241, 0.06)' : 'rgba(99, 102, 241, 0.04)' }
                    }
                },
                grid: { left: '3%', right: '4%', bottom: '3%', top: '8%', containLabel: true },
                xAxis: {
                    type: 'category',
                    data: this.labels,
                    axisLine: { show: false },
                    axisTick: { show: false },
                    axisLabel: { fontSize: 11, color: dark ? '#475569' : '#94a3b8', fontWeight: 600 }
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
                animationDuration: 800,
                animationEasing: 'cubicOut',
                animationDelay: function(idx) { return idx * 120; },
                series: [{
                    type: 'bar',
                    barWidth: '45%',
                    barMaxWidth: 48,
                    data: this.values.map(v => {
                        if (v === null) {
                            return { value: 0, itemStyle: { color: dark ? '#334155' : '#e2e8f0', borderRadius: [6, 6, 0, 0] } };
                        }
                        return {
                            value: v,
                            itemStyle: {
                                color: v >= 0 ? {
                                    type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                                    colorStops: [
                                        { offset: 0, color: 'rgba(16, 185, 129, 0.95)' },
                                        { offset: 1, color: 'rgba(5, 150, 105, 0.65)' }
                                    ]
                                } : {
                                    type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                                    colorStops: [
                                        { offset: 0, color: 'rgba(239, 68, 68, 0.65)' },
                                        { offset: 1, color: 'rgba(239, 68, 68, 0.95)' }
                                    ]
                                },
                                borderRadius: v >= 0 ? [6, 6, 0, 0] : [0, 0, 6, 6],
                                shadowColor: v >= 0 ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)',
                                shadowBlur: 8,
                                shadowOffsetY: v >= 0 ? 4 : -4
                            }
                        };
                    }),
                    markLine: {
                        silent: true,
                        symbol: 'none',
                        lineStyle: { color: dark ? '#475569' : '#cbd5e1', type: 'dashed', width: 1.5 },
                        data: [{ yAxis: 0 }],
                        label: { show: false }
                    },
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 16,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.2)'
                        }
                    }
                }]
            });
        }
    }));
});
</script>
`;

content = content.replace(/<script>\s*\(function\(\) \{\s*let resizeTimer;[\s\S]*?<\/script>/, alpineScripts + '\n<script>\n(function() {\n    let resizeTimer;\n    window.addEventListener(\'resize\', function() {\n        clearTimeout(resizeTimer);\n        resizeTimer = setTimeout(function() {\n            document.querySelectorAll(\'[x-ref=\"container\"]\').forEach(function(el) {\n                var inst = echarts.getInstanceByDom(el);\n                if (inst) inst.resize({ animation: { duration: 300, easing: \'cubicOut\' } });\n            });\n        }, 150);\n    });\n})();\n</script>');

fs.writeFileSync(file, content, 'utf8');
console.log('done');
