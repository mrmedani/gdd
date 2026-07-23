const fs = require('fs');

let file = 'c:/Users/hassa/Desktop/GDD/resources/views/livewire/statistics-index.blade.php';
let content = fs.readFileSync(file, 'utf8');
let rowsContent = fs.readFileSync('c:/Users/hassa/Desktop/GDD/rows-content.html', 'utf8');

// The regex matches everything from "<!-- Row 1..." up to just before "<!-- Expenses List..."
let fullRegex = /<!-- Row 1: Category Chart \+ Payment Methods -->[\s\S]*?(?=<!-- Expenses List for this Period -->)/;

// Extract blocks from rowsContent
let block1Match = rowsContent.match(/<!-- Category Doughnut \+ Table -->[\s\S]*?(?=<!-- Payment Methods -->)/);
let block1 = block1Match[0].trim();

let block2Match = rowsContent.match(/<!-- Payment Methods -->[\s\S]*?<\/div>\n\s*<\/div>/);
let block2 = block2Match[0].replace(/<\/div>\n\s*<\/div>$/, '</div>').trim(); // Remove the row closing div

// Growth Chart has missing ending tags, let's just rewrite it cleanly
let block3 = `<!-- Growth Chart -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-8 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center font-heading">
                <span class="w-2.5 h-6 bg-emerald-500 rounded-full me-3"></span>
                {{ __('statistics.growth_trend') }}
            </h2>
            @if(count($growthTrend) > 0)
                <div wire:key="growth-chart" class="relative h-72 w-full"
                     x-data="growthChart(@js(array_column($growthTrend, 'label')), @js(array_column($growthTrend, 'rate')))">
                    <div wire:ignore x-ref="container" class="w-full h-full"></div>
                </div>
            @else
                <div class="h-72 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                    <p class="text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ __('statistics.no_data') }}</p>
                </div>
            @endif
        </div>`;

let block4Match = rowsContent.match(/<!-- Category Table -->[\s\S]*?<\/div>\n\s*<\/div>/);
let block4 = block4Match[0].replace(/<\/div>\n\s*<\/div>$/, '</div>').trim(); // Remove the row closing div

// Add col-span to block4 and block2
block4 = block4.replace('class="bg-white/80', 'class="lg:col-span-2 bg-white/80');
block2 = block2.replace('class="bg-white/80', 'class="lg:col-span-1 bg-white/80');

// Construct new layout
let newLayout = `<!-- Visual Analytics (Charts) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        ${block1}

        ${block3}
    </div>

    <!-- Data Breakdown (Tables & Lists) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        ${block4}

        ${block2}
    </div>
    
    `;

// Replace in main content
content = content.replace(fullRegex, newLayout);
fs.writeFileSync(file, content, 'utf8');

console.log('done');
