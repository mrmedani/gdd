const fs = require('fs');
let file = 'c:/Users/hassa/Desktop/GDD/resources/views/livewire/dashboard.blade.php';
let content = fs.readFileSync(file, 'utf8');

const regex = /chartInstances\.category\.setOption\(\{[\s\S]*?\]\n\s*\}\)\]\n\s*\}\);/;

const newComponent = `chartInstances.category.setOption({
        tooltip: {
            trigger: 'item',
            backgroundColor: dark ? 'rgba(15, 23, 42, 0.85)' : 'rgba(255, 255, 255, 0.9)',
            borderColor: dark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
            borderWidth: 1,
            padding: [16, 20],
            textStyle: { color: dark ? '#f8fafc' : '#1e293b', fontSize: 13, fontWeight: 500 },
            extraCssText: 'border-radius: 16px; backdrop-filter: blur(16px); box-shadow: 0 10px 40px -10px rgba(0,0,0,' + (dark ? '0.5' : '0.15') + ');',
            formatter: function(params) {
                return '<div style="font-size:13px;font-weight:600;color:' + (dark ? '#94a3b8' : '#64748b') + ';margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px">' + params.marker + ' ' + params.name + '</div>' +
                       '<div style="font-size:20px;font-weight:800;color:' + (dark ? '#fff' : '#0f172a') + '; letter-spacing:-0.5px;">' + new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(params.value) + ' <span style="font-size:14px;font-weight:600">{{ getCurrency() }}</span></div>' +
                       '<div style="font-size:13px;font-weight:600;color:' + params.color + ';margin-top:4px;">' + params.percent + '% du total</div>';
            }
        },
        legend: {
            type: 'scroll',
            orient: isMobile ? 'horizontal' : 'vertical',
            right: isMobile ? 'center' : '2%',
            bottom: isMobile ? 0 : 'center',
            top: isMobile ? 'auto' : 'center',
            icon: 'circle',
            itemWidth: 12,
            itemHeight: 12,
            itemGap: 16,
            textStyle: { fontSize: 12, fontWeight: 600, color: dark ? '#cbd5e1' : '#475569' },
            pageIconColor: '#3b82f6',
            pageTextStyle: { color: dark ? '#cbd5e1' : '#475569' }
        },
        animationDuration: 1500,
        animationEasing: 'cubicOut',
        series: [{
            type: 'pie',
            roseType: 'radius', // Makes it a beautiful rose chart (Nightingale chart)
            radius: ['35%', '80%'], // Slightly smaller inner, larger outer
            center: isMobile ? ['50%', '40%'] : ['35%', '50%'], // Shift to the left on desktop to leave room for legend
            avoidLabelOverlap: true,
            itemStyle: {
                borderRadius: 12,
                borderColor: dark ? '#0f172a' : '#ffffff',
                borderWidth: 4,
                shadowBlur: 15,
                shadowColor: 'rgba(0, 0, 0, 0.1)',
                shadowOffsetX: 0,
                shadowOffsetY: 5
            },
            emphasis: {
                scale: true,
                scaleSize: 10,
                itemStyle: {
                    shadowBlur: 25,
                    shadowOffsetX: 0,
                    shadowOffsetY: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.3)'
                }
            },
            label: { show: false },
            data: [
                @foreach($categoryData as $cat)
                { value: {{ $cat['total'] }}, name: '{!! addslashes($cat['label']) !!}', itemStyle: { color: '{{ $cat['color'] }}' } },
                @endforeach
            ].sort(function (a, b) { return b.value - a.value; }) // Sort by value for a perfect rose effect
        }]
    });`;

if (content.match(regex)) {
    content = content.replace(regex, newComponent);
    fs.writeFileSync(file, content);
    console.log('Category chart updated successfully.');
} else {
    console.log('Regex did not match.');
}
