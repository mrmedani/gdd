const fs = require('fs');
let file = 'c:/Users/hassa/Desktop/GDD/resources/views/livewire/statistics-index.blade.php';
let content = fs.readFileSync(file, 'utf8');

const regex = /Alpine\.data\('growthChart'[\s\S]*?\}\)\);/;

const newComponent = `Alpine.data('growthChart', (labels, values) => ({
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
                    
                    window.addEventListener('resize', () => {
                        if(this.chart) this.chart.resize();
                    });
                } else { setTimeout(check, 100); }
            };
            check();
        },
        renderChart() {
            const dark = this.isDark();
            
            const colorPrimary = '#10B981';
            const colorSecondary = '#34D399';
            const colorGlow = 'rgba(16, 185, 129, 0.5)';
            
            // Format number with spaces (e.g. 1 000 000.00)
            const formatMoney = (val) => {
                return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val) + ' DA';
            };
            
            this.chart.setOption({
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: dark ? 'rgba(15, 23, 42, 0.85)' : 'rgba(255, 255, 255, 0.9)',
                    borderColor: dark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
                    borderWidth: 1,
                    padding: [16, 20],
                    textStyle: { color: dark ? '#f8fafc' : '#1e293b', fontSize: 13, fontWeight: 500 },
                    extraCssText: 'border-radius: 16px; backdrop-filter: blur(16px); box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.3);',
                    formatter: function(params) {
                        const v = params[0].value;
                        return '<div style="font-size:13px;font-weight:600;color:' + (dark ? '#94a3b8' : '#64748b') + ';margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px">' + params[0].axisValueLabel + '</div>' +
                               '<div style="display:flex;align-items:center;gap:8px;">' +
                                 '<div style="font-size:24px;font-weight:800;color:' + (dark ? '#fff' : '#0f172a') + '; letter-spacing:-0.5px;">' + formatMoney(v) + '</div>' +
                               '</div>';
                    },
                    axisPointer: {
                        type: 'line',
                        lineStyle: { 
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(16, 185, 129, 0)'}, {offset: 0.5, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0)'}]), 
                            width: 2, 
                            type: 'solid' 
                        }
                    }
                },
                grid: { left: '2%', right: '3%', bottom: '2%', top: '10%', containLabel: true },
                xAxis: {
                    type: 'category',
                    data: this.labels,
                    axisLine: { lineStyle: { color: dark ? '#334155' : '#e2e8f0', width: 2 } },
                    axisTick: { show: false },
                    axisLabel: { fontSize: 12, color: dark ? '#94a3b8' : '#64748b', fontWeight: 600, margin: 16 },
                    boundaryGap: false
                },
                yAxis: {
                    type: 'value',
                    splitLine: { 
                        lineStyle: { 
                            color: dark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.6)', 
                            type: 'dashed',
                            width: 1
                        } 
                    },
                    axisLabel: {
                        fontSize: 12,
                        color: dark ? '#94a3b8' : '#64748b',
                        fontWeight: 600,
                        formatter: function(v) { 
                            if(v >= 1000000) return (v / 1000000).toFixed(1) + 'M DA';
                            if(v >= 1000) return (v / 1000).toFixed(1) + 'K DA';
                            return v + ' DA'; 
                        },
                        margin: 16
                    }
                },
                animationDuration: 2000,
                animationEasing: 'cubicOut',
                series: [{
                    type: 'line',
                    smooth: 0.5,
                    symbol: 'circle',
                    symbolSize: 0,
                    showSymbol: false,
                    data: this.values,
                    lineStyle: {
                        width: 4,
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            { offset: 0, color: colorPrimary },
                            { offset: 1, color: colorSecondary }
                        ]),
                        shadowColor: colorGlow,
                        shadowBlur: 20,
                        shadowOffsetY: 8
                    },
                    itemStyle: {
                        color: '#fff',
                        borderColor: colorPrimary,
                        borderWidth: 3,
                        shadowColor: colorGlow,
                        shadowBlur: 15
                    },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(52, 211, 153, 0.4)' },
                            { offset: 0.5, color: 'rgba(16, 185, 129, 0.1)' },
                            { offset: 1, color: 'rgba(16, 185, 129, 0)' }
                        ])
                    },
                    emphasis: { 
                        focus: 'series',
                        itemStyle: { 
                            color: colorPrimary,
                            borderColor: '#fff',
                            borderWidth: 4,
                            symbolSize: 12 
                        } 
                    }
                }]
            });
        }
    }));`;

content = content.replace(regex, newComponent);
fs.writeFileSync(file, content);
console.log('Graph updated to show raw gains amount.');
