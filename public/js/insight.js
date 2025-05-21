// Variables to track current year and month for each chart
let currentYearRevenue = new Date().getFullYear();
let currentYearProfit = new Date().getFullYear();
let currentMonth = new Date().getMonth() + 1; // 1-12

// Function to update year and month display
function updateYearDisplay() {
    document.getElementById('current-year').textContent = currentYearRevenue;
    document.getElementById('current-year-profit').textContent = currentYearProfit;
    document.getElementById('current-month').textContent = `${getMonthName(currentMonth)} ${currentYearRevenue}`;
}

// Function to format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

// Function to get month name
function getMonthName(month) {
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    return months[month - 1];
}

// Function to update table data
function updateTableData(data) {
    // Update month and year displays
    document.getElementById('current-month').textContent = `${getMonthName(currentMonth)} ${currentYearRevenue}`;
    document.getElementById('current-year-summary').textContent = currentYearRevenue;

    // Update This Month data with animation
    animateValue('this-month-revenue', data.this_month.revenue);
    animateValue('this-month-cost-project', data.this_month.cost_project);
    animateValue('this-month-gross-margin', data.this_month.gross_margin);

    // Update Summary data with animation
    animateValue('summary-revenue', data.summary.revenue);
    animateValue('summary-cost-project', data.summary.cost_project);
    animateValue('summary-gross-margin', data.summary.gross_margin);

    // Update Average data with animation
    animateValue('average-revenue', data.average.revenue);
    animateValue('average-cost-project', data.average.cost_project);
    animateValue('average-gross-margin', data.average.gross_margin);
}

// Function to animate value changes
function animateValue(elementId, value) {
    const element = document.getElementById(elementId);
    const start = parseFloat(element.textContent.replace(/[^0-9.-]+/g, '')) || 0;
    const end = value;
    const duration = 1000; // 1 second
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        const current = start + (end - start) * progress;
        element.textContent = formatCurrency(current);

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

// Function to initialize charts
function initializeCharts() {
    // Tampilkan loading state
    const revenueChart = document.querySelector("#revenue-chart");
    const profitChart = document.querySelector("#profit-chart");
    
    if (revenueChart) revenueChart.innerHTML = '<div class="flex items-center justify-center h-64"><div class="text-gray-500">Loading...</div></div>';
    if (profitChart) profitChart.innerHTML = '<div class="flex items-center justify-center h-64"><div class="text-gray-500">Loading...</div></div>';

    // Kirim parameter tahun dan bulan ke backend dengan CSRF token
    fetch(`/logsheets/chart-data?year=${currentYearRevenue}&month=${currentMonth}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Unknown error occurred');
        }

        // Update table data
        updateTableData(data);

        // Data sudah dikelompokkan per bulan dari backend
        const revenueData = data.revenue;
        const costData = data.cost;
        const profitData = data.profit;

        // Revenue & Cost chart options
        const revenueOptions = {
            series: [
                {
                    name: 'Revenue',
                    data: revenueData.map(item => Math.abs(item.y)),
                    color: '#1A56DB'
                },
                {
                    name: 'Cost',
                    data: costData.map(item => Math.abs(item.y)),
                    color: '#F87171'
                }
            ],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: revenueData.map(item => {
                    return new Date(item.x).toLocaleDateString('id-ID', { month: 'long' });
                }),
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        // Check if original value was negative
                        const originalValue = revenueData.find(item => Math.abs(item.y) === value)?.y || 
                                           costData.find(item => Math.abs(item.y) === value)?.y || value;
                        return formatCurrency(originalValue < 0 ? -value : value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value, { seriesIndex, dataPointIndex, w }) {
                        // Get original value from data
                        const series = w.config.series[seriesIndex].data;
                        const originalValue = seriesIndex === 0 ? 
                            revenueData[dataPointIndex]?.y : 
                            costData[dataPointIndex]?.y;
                        return formatCurrency(originalValue < 0 ? -value : value);
                    }
                },
                shared: true,
                intersect: false
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        // Profit chart options
        const profitOptions = {
            series: [{
                name: 'Profit',
                data: profitData.map(item => Math.abs(item.y))
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: profitData.map(item => {
                    return new Date(item.x).toLocaleDateString('id-ID', { month: 'long' });
                }),
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        // Check if original value was negative
                        const originalValue = profitData.find(item => Math.abs(item.y) === value)?.y || value;
                        return formatCurrency(originalValue < 0 ? -value : value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value, { dataPointIndex }) {
                        // Get original value from data
                        const originalValue = profitData[dataPointIndex]?.y;
                        return formatCurrency(originalValue < 0 ? -value : value);
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#10B981'] // Warna hijau untuk profit
        };

        // Render charts
        if (revenueChart) revenueChart.innerHTML = '';
        if (profitChart) profitChart.innerHTML = '';
        
        if (revenueChart) new ApexCharts(revenueChart, revenueOptions).render();
        if (profitChart) new ApexCharts(profitChart, profitOptions).render();
    })
    .catch(error => {
        console.error('Error loading chart data:', error);
        const errorMessage = '<div class="flex items-center justify-center h-64"><div class="text-red-500">Error loading data. Please try again later.</div></div>';
        if (revenueChart) revenueChart.innerHTML = errorMessage;
        if (profitChart) profitChart.innerHTML = errorMessage;
    });
}

// Function to refresh charts
function refreshCharts() {
    initializeCharts();
}

// Function to handle month navigation
function handleMonthNavigation(direction) {
    if (direction === 'prev') {
        if (currentMonth === 1) {
            currentMonth = 12;
            currentYearRevenue--;
            currentYearProfit--;
        } else {
            currentMonth--;
        }
    } else if (direction === 'next') {
        if (currentMonth === 12) {
            currentMonth = 1;
            currentYearRevenue++;
            currentYearProfit++;
        } else {
            currentMonth++;
        }
    }
    updateYearDisplay();
    refreshCharts();
}

// Event listeners for year and month navigation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('prev-year-revenue')?.addEventListener('click', function() {
        currentYearRevenue--;
        updateYearDisplay();
        refreshCharts();
    });

    document.getElementById('next-year-revenue')?.addEventListener('click', function() {
        currentYearRevenue++;
        updateYearDisplay();
        refreshCharts();
    });

    document.getElementById('prev-year-profit')?.addEventListener('click', function() {
        currentYearProfit--;
        updateYearDisplay();
        refreshCharts();
    });

    document.getElementById('next-year-profit')?.addEventListener('click', function() {
        currentYearProfit++;
        updateYearDisplay();
        refreshCharts();
    });

    document.getElementById('prev-month')?.addEventListener('click', function() {
        handleMonthNavigation('prev');
    });

    document.getElementById('next-month')?.addEventListener('click', function() {
        handleMonthNavigation('next');
    });

    // Initialize charts when page loads
    updateYearDisplay();
    initializeCharts();
}); 