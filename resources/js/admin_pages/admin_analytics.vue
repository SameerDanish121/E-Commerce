
<template>
    <div class="analytics-dashboard">
        <div class="dashboard-controls">
            <div class="control-group">
                <label>Time Period:</label>
                <select v-model="selectedPeriod" @change="updateCharts">
                    <option value="day">Daily</option>
                    <option value="week">Weekly</option>
                    <option value="month">Monthly</option>
                    <option value="year">Yearly</option>
                </select>
            </div>
            <div class="control-group">
                <label>Year:</label>
                <select v-model="selectedYear" @change="updateCharts" v-if="selectedPeriod !== 'year'">
                    <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                </select>
                <span v-else>Last 5 Years Comparison</span>
            </div>
            <div class="control-group">
                <label>Chart Type:</label>
                <select v-model="selectedChartType" @change="updateCharts">
                    <option value="line">Line Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
            </div>
        </div>

        <div class="summary-cards">
            <div class="summary-card" :style="cardStyle(0)">
                <div class="card-icon">💰</div>
                <div class="card-content">
                    <h3>Total Revenue</h3>
                    <p>{{ formatCompactCurrency(totalRevenue) }}</p>
                    <p :style="revenueChange >= 0 ? positiveStyle : negativeStyle">
                        <i :class="revenueChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                        {{ formatPercentage(revenueChange) }}
                        <span class="change-text">{{ revenueChange >= 0 ? 'increase' : 'decrease' }}</span>
                    </p>
                </div>
            </div>

            <div class="summary-card" :style="cardStyle(1)">
                <div class="card-icon">📦</div>
                <div class="card-content">
                    <h3>Total Orders</h3>
                    <p>{{ formatCompactNumber(totalOrders) }}</p>
                    <p :style="orderChange >= 0 ? positiveStyle : negativeStyle">
                        <i :class="orderChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                        {{ formatPercentage(orderChange) }}
                        <span class="change-text">{{ orderChange >= 0 ? 'increase' : 'decrease' }}</span>
                    </p>
                </div>
            </div>

            <div class="summary-card" :style="cardStyle(2)">
                <div class="card-icon">👥</div>
                <div class="card-content">
                    <h3>Active Customers</h3>
                    <p>{{ formatCompactNumber(activeCustomers) }}</p>
                    <p :style="customerChange >= 0 ? positiveStyle : negativeStyle">
                        <i :class="customerChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                        {{ formatPercentage(customerChange) }}
                        <span class="change-text">{{ customerChange >= 0 ? 'growth' : 'decline' }}</span>
                    </p>
                </div>
            </div>

            <div class="summary-card" :style="cardStyle(3)">
                <div class="card-icon">📊</div>
                <div class="card-content">
                    <h3>Avg. Order Value</h3>
                    <p>{{ formatCompactCurrency(avgOrderValue) }}</p>
                    <p :style="aovChange >= 0 ? positiveStyle : negativeStyle">
                        <i :class="aovChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                        {{ formatPercentage(aovChange) }}
                        <span class="change-text">{{ aovChange >= 0 ? 'increase' : 'decrease' }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="chart-row">
            <div class="chart-container">
                <h2>Sales Revenue Trend</h2>
                <div class="chart-wrapper">
                    <canvas ref="salesChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <h2>Order Count Trend</h2>
                <div class="chart-wrapper">
                    <canvas ref="ordersChart"></canvas>
                </div>
            </div>
        </div>
        <div class="chart-row">
            <div class="chart-container">
                <h2>Top Customers by Spending</h2>
                <div class="chart-wrapper">
                    <canvas ref="customersChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <h2>Top Selling Products</h2>
                <div class="chart-wrapper">
                    <canvas ref="productsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="chart-row">
            <div class="chart-container full-width">
                <h2>Delivery Performance</h2>
                <div class="delivery-stats">
                    <div class="delivery-stat" v-for="(metric, index) in deliveryMetrics" :key="index">
                        <div class="stat-value">{{ metric.value }}</div>
                        <div class="stat-label" :class="metric.class">
                            <span v-if="metric.label === 'On Time'"><i class="fas fa-check-circle"></i></span>
                            <span v-else-if="metric.label === 'Late'"><i class="fas fa-exclamation-circle"></i></span>
                            <span v-else><i class="fas fa-question-circle"></i></span>
                            {{ metric.label }}
                        </div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas ref="deliveryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import { useOrderProcessingStore } from '../stores/OrderProcessingStore';
import { useAdminProductStore } from '../stores/AdminProductStore';
import { useAllCustomerStore } from '../stores/AllCustomerStore';
import { Chart, registerables } from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(...registerables, zoomPlugin, ChartDataLabels);

export default {
    name: 'AnalyticsDashboard',
    setup() {
        const orderStore = useOrderProcessingStore();
        const productStore = useAdminProductStore();
        const customerStore = useAllCustomerStore();
        const selectedPeriod = ref('day');
        const selectedYear = ref(new Date().getFullYear());
        const selectedChartType = ref('line');
        const salesChart = ref(null);
        const ordersChart = ref(null);
        const customersChart = ref(null);
        const productsChart = ref(null);
        const deliveryChart = ref(null);
        const colorSchemes = [
            ['#4f46e5', '#6366f1', '#818cf8', '#a5b4fc', '#c7d2fe'],
            ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5'],
            ['#ec4899', '#f472b6', '#f9a8d4', '#fbcfe8', '#fce7f3'], 
            ['#f59e0b', '#fbbf24', '#fcd34d', '#fde68a', '#fef3c7']
        ];
        onMounted(async () => {
            await Promise.all([
                orderStore.fetchAllOrders(),
                productStore.fetchProducts(),
                customerStore.fetchAllCustomers()
            ]);
            initCharts();
        });
        const availableYears = computed(() => {
            const years = new Set();
            orderStore.orders.forEach(order => {
                const year = new Date(order.order_datetime).getFullYear();
                years.add(year);
            });
            return Array.from(years).sort((a, b) => b - a);
        });

        const totalRevenue = computed(() => {
            return orderStore.orders.reduce((sum, order) => sum + order.total_payment, 0);
        });

        const totalOrders = computed(() => orderStore.orders.length);

        const activeCustomers = computed(() => {
            const customerIds = new Set();
            orderStore.orders.forEach(order => {
                if (order.customer?.customer_id) {
                    customerIds.add(order.customer.customer_id);
                }
            });
            return customerIds.size;
        });

        const avgOrderValue = computed(() => {
            return totalOrders.value > 0 ? totalRevenue.value / totalOrders.value : 0;
        });

        const topCustomers = computed(() => {
            const customerMap = new Map();

            orderStore.orders.forEach(order => {
                if (order.customer?.customer_id) {
                    const customerId = order.customer.customer_id;
                    const current = customerMap.get(customerId) || {
                        id: customerId,
                        name: order.customer.name,
                        total: 0,
                        orders: 0
                    };
                    current.total += order.total_payment;
                    current.orders += 1;
                    customerMap.set(customerId, current);
                }
            });

            return Array.from(customerMap.values())
                .sort((a, b) => b.total - a.total)
                .slice(0, 5);
        });

        const topProducts = computed(() => {
            const productMap = new Map();

            orderStore.orders.forEach(order => {
                if (order.products) {
                    order.products.forEach(product => {
                        const productId = product.product_id;
                        const current = productMap.get(productId) || {
                            id: productId,
                            name: product.product_name,
                            revenue: 0,
                            quantity: 0
                        };
                        current.revenue += product.price_at_purchase * product.quantity;
                        current.quantity += product.quantity;
                        productMap.set(productId, current);
                    });
                }
            });

            return Array.from(productMap.values())
                .sort((a, b) => b.revenue - a.revenue)
                .slice(0, 5);
        });

        const deliveryPerformance = computed(() => {
            const performance = {
                onTime: 0,
                late: 0,
                pending: 0
            };

            orderStore.orders.forEach(order => {
                if (order.status === 'Completed') {
                    if (order.delivered_on_time) {
                        performance.onTime++;
                    } else {
                        performance.late++;
                    }
                } else if (order.status === 'Pending' || order.status === 'Dispatched') {
                    performance.pending++;
                }
            });

            return performance;
        });

        const deliveryMetrics = computed(() => [
            {
                label: 'On Time',
                value: deliveryPerformance.value.onTime,
                class: 'on-time'
            },
            {
                label: 'Late',
                value: deliveryPerformance.value.late,
                class: 'late'
            },
            {
                label: 'Pending',
                value: deliveryPerformance.value.pending,
                class: 'pending'
            }
        ]);

        const groupedSalesData = computed(() => {
            if (!orderStore.orders.length) return [];

            if (selectedPeriod.value === 'year') {
                const currentYear = new Date().getFullYear();
                const yearsToShow = Array.from({length: 5}, (_, i) => currentYear - i).reverse();
                
                return yearsToShow.map(year => {
                    const yearOrders = orderStore.orders.filter(order => {
                        const orderYear = new Date(order.order_datetime).getFullYear();
                        return orderYear === year;
                    });
                    
                    const revenue = yearOrders.reduce((sum, order) => sum + order.total_payment, 0);
                    const orders = yearOrders.length;
                    
                    return {
                        year: year.toString(),
                        revenue,
                        orders
                    };
                });
            }

            const filtered = orderStore.orders.filter(order => {
                const orderYear = new Date(order.order_datetime).getFullYear();
                return orderYear === selectedYear.value;
            });

            if (selectedPeriod.value === 'day') {
                return groupByDay(filtered);
            } else if (selectedPeriod.value === 'week') {
                return groupByWeek(filtered);
            } else if (selectedPeriod.value === 'month') {
                // For monthly view, ensure all 12 months are shown
                const monthsData = groupByMonth(filtered);
                const currentYear = selectedYear.value;
                const allMonths = Array.from({length: 12}, (_, i) => {
                    const month = (i + 1).toString().padStart(2, '0');
                    const monthKey = `${currentYear}-${month}`;
                    const existingMonth = monthsData.find(m => m.month === monthKey);
                    
                    return existingMonth || {
                        month: monthKey,
                        revenue: 0,
                        orders: 0
                    };
                });
                
                return allMonths;
            }
        });
        const revenueChange = computed(() => calculatePercentageChange('revenue'));
        const orderChange = computed(() => calculatePercentageChange('orders'));
        const customerChange = computed(() => calculatePercentageChange('customers'));
        const aovChange = computed(() => calculatePercentageChange('aov'));
        const initCharts = () => {
            if (salesChart.value) {
                charts.sales = createChart(salesChart.value, getSalesChartConfig());
            }
            if (ordersChart.value) {
                charts.orders = createChart(ordersChart.value, getOrdersChartConfig());
            }
            if (customersChart.value) {
                charts.customers = createChart(customersChart.value, getCustomersChartConfig());
            }
            if (productsChart.value) {
                charts.products = createChart(productsChart.value, getProductsChartConfig());
            }
            if (deliveryChart.value) {
                charts.delivery = createChart(deliveryChart.value, getDeliveryChartConfig());
            }
        };

        const updateCharts = () => {
            if (charts.sales) {
                charts.sales.destroy();
                charts.sales = createChart(salesChart.value, getSalesChartConfig());
            }
            if (charts.orders) {
                charts.orders.destroy();
                charts.orders = createChart(ordersChart.value, getOrdersChartConfig());
            }
            if (charts.customers) {
                charts.customers.destroy();
                charts.customers = createChart(customersChart.value, getCustomersChartConfig());
            }
            if (charts.products) {
                charts.products.destroy();
                charts.products = createChart(productsChart.value, getProductsChartConfig());
            }
            if (charts.delivery) {
                charts.delivery.destroy();
                charts.delivery = createChart(deliveryChart.value, getDeliveryChartConfig());
            }
        };

        const charts = {
            sales: null,
            orders: null,
            customers: null,
            products: null,
            delivery: null
        };

        const createChart = (canvas, config) => {
            return new Chart(canvas, config);
        };

        const formatCompactCurrency = (value) => {
            if (value >= 1000000000) {
                return '$' + (value / 1000000000).toFixed(1) + 'B';
            } else if (value >= 1000000) {
                return '$' + (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 1000) {
                return '$' + (value / 1000).toFixed(1) + 'K';
            }
            return '$' + value.toFixed(2);
        };

        const formatCompactNumber = (value) => {
            if (value >= 1000000) {
                return (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 1000) {
                return (value / 1000).toFixed(1) + 'K';
            }
            return value.toString();
        };

        const formatPercentage = (value) => {
            return Math.abs(value).toFixed(2) + '%';
        };

        const groupByDay = (orders) => {
            const daysMap = new Map();

            orders.forEach(order => {
                const date = new Date(order.order_datetime);
                const dayKey = date.toISOString().split('T')[0];

                const existing = daysMap.get(dayKey) || {
                    date: dayKey,
                    revenue: 0,
                    orders: 0
                };

                existing.revenue += order.total_payment;
                existing.orders += 1;
                daysMap.set(dayKey, existing);
            });

            return Array.from(daysMap.values()).sort((a, b) => new Date(a.date) - new Date(b.date));
        };

        const groupByWeek = (orders) => {
            const weeksMap = new Map();

            orders.forEach(order => {
                const date = new Date(order.order_datetime);
                const year = date.getFullYear();
                const weekNumber = getWeekNumber(date);
                const weekKey = `${year}-W${weekNumber.toString().padStart(2, '0')}`;

                const existing = weeksMap.get(weekKey) || {
                    week: weekKey,
                    revenue: 0,
                    orders: 0
                };

                existing.revenue += order.total_payment;
                existing.orders += 1;
                weeksMap.set(weekKey, existing);
            });

            return Array.from(weeksMap.values()).sort((a, b) => a.week.localeCompare(b.week));
        };

        const groupByMonth = (orders) => {
            const monthsMap = new Map();

            orders.forEach(order => {
                const date = new Date(order.order_datetime);
                const monthKey = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}`;

                const existing = monthsMap.get(monthKey) || {
                    month: monthKey,
                    revenue: 0,
                    orders: 0
                };

                existing.revenue += order.total_payment;
                existing.orders += 1;
                monthsMap.set(monthKey, existing);
            });

            return Array.from(monthsMap.values()).sort((a, b) => a.month.localeCompare(b.month));
        };

        const getWeekNumber = (date) => {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        };

        const calculatePercentageChange = (metric) => {
            if (groupedSalesData.value.length < 2) return 0;

            const currentPeriod = groupedSalesData.value[groupedSalesData.value.length - 1];
            const previousPeriod = groupedSalesData.value[groupedSalesData.value.length - 2];

            let currentValue, previousValue;

            switch (metric) {
                case 'revenue':
                    currentValue = currentPeriod.revenue;
                    previousValue = previousPeriod.revenue;
                    break;
                case 'orders':
                    currentValue = currentPeriod.orders;
                    previousValue = previousPeriod.orders;
                    break;
                case 'customers':
                    return 0;
                case 'aov':
                    currentValue = currentPeriod.orders > 0 ? currentPeriod.revenue / currentPeriod.orders : 0;
                    previousValue = previousPeriod.orders > 0 ? previousPeriod.revenue / previousPeriod.orders : 0;
                    break;
                default:
                    return 0;
            }

            if (previousValue === 0) return 0;
            return ((currentValue - previousValue) / previousValue) * 100;
        };

        const getSalesChartConfig = () => {
            const labels = groupedSalesData.value.map(item => {
                if (selectedPeriod.value === 'day') return formatDateLabel(item.date);
                if (selectedPeriod.value === 'week') return `Week ${item.week.split('-W')[1]}`;
                if (selectedPeriod.value === 'month') return formatMonthLabel(item.month);
                return item.year;
            });

            return {
                type: selectedChartType.value,
                data: {
                    labels,
                    datasets: [{
                        label: 'Revenue',
                        data: groupedSalesData.value.map(item => item.revenue),
                        backgroundColor: selectedChartType.value === 'bar' ? colorSchemes[0][0] : 'rgba(79, 70, 229, 0.2)',
                        borderColor: colorSchemes[0][0],
                        borderWidth: 2,
                        fill: selectedChartType.value === 'line',
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: colorSchemes[0][0],
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Sales Revenue Trend', font: { size: 16 } },
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Revenue: ${formatCompactCurrency(context.raw)}`,
                                title: (context) => {
                                    const item = groupedSalesData.value[context[0].dataIndex];
                                    if (selectedPeriod.value === 'day') return formatDateFull(item.date);
                                    if (selectedPeriod.value === 'week') return `Week ${item.week.split('-W')[1]}, ${item.week.split('-')[0]}`;
                                    if (selectedPeriod.value === 'month') return formatMonthFull(item.month);
                                    return `Year ${item.year}`;
                                }
                            }
                        },
                        datalabels: {
                            color: '#000',
                            anchor: 'start',
                            align: 'top',
                            formatter: (value) => formatCompactCurrency(value),
                            font: {
                                weight: 'bold',
                                size: 8
                            }
                        }
                    },
                    scales: {
                     
                        y: {
                            beginAtZero: false,
                            min: 0,
                            ticks: {
                                callback: (value) => formatCompactCurrency(value),
                                stepSize: 4000,   
                            },
                            grid: {
                                display: false 
                            }
                        },

                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            };
        };

        const getOrdersChartConfig = () => {
            const labels = groupedSalesData.value.map(item => {
                if (selectedPeriod.value === 'day') return formatDateLabel(item.date);
                if (selectedPeriod.value === 'week') return `Week ${item.week.split('-W')[1]}`;
                if (selectedPeriod.value === 'month') return formatMonthLabel(item.month);
                return item.year;
            });

            return {
                type: selectedChartType.value,
                data: {
                    labels,
                    datasets: [{
                        label: 'Orders',
                        data: groupedSalesData.value.map(item => item.orders),
                        backgroundColor: selectedChartType.value === 'bar' ? colorSchemes[1][0] : 'rgba(16, 185, 129, 0.2)',
                        borderColor: colorSchemes[1][0],
                        borderWidth: 2,
                        fill: selectedChartType.value === 'line',
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: colorSchemes[1][0],
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Order Count Trend', font: { size: 16 } },
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${formatCompactNumber(context.raw)} orders`,
                                title: (context) => {
                                    const item = groupedSalesData.value[context[0].dataIndex];
                                    if (selectedPeriod.value === 'day') return formatDateFull(item.date);
                                    if (selectedPeriod.value === 'week') return `Week ${item.week.split('-W')[1]}, ${item.week.split('-')[0]}`;
                                    if (selectedPeriod.value === 'month') return formatMonthFull(item.month);
                                    return `Year ${item.year}`;
                                }
                            }
                        }, datalabels: {
                            color: '#000',
                            anchor: 'start',
                            align: 'top',
                            formatter: (value) =>formatCompactNumber(value),
                            font: {
                                weight: 'bold',
                                size: 8
                            }
                        }
                    },

                    scales: {
                         y: {
                            beginAtZero: false,
                            min: 0,
                            ticks: {
                                 stepSize: 4,     
                                precision: 0
                            },
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            };
        };

        const getCustomersChartConfig = () => {
            return {
                type: 'bar',
                data: {
                    labels: topCustomers.value.map(c => c.name),
                    datasets: [{
                        label: 'Total Spending',
                        data: topCustomers.value.map(c => c.total),
                        backgroundColor: colorSchemes[0].slice(0, topCustomers.value.length),
                        borderColor: colorSchemes[0].slice(0, topCustomers.value.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Top Customers by Spending', font: { size: 16 } },
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${formatCompactCurrency(context.raw)} (${topCustomers.value[context.dataIndex].orders} orders)`
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            formatter: (value) => formatCompactCurrency(value),
                            color: '#4b5563',
                            font: { weight: 'bold' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => formatCompactCurrency(value)
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            };
        };

        const getProductsChartConfig = () => {
            return {
                type: 'doughnut',
                data: {
                    labels: topProducts.value.map(p => truncateProductName(p.name)),
                    datasets: [{
                        data: topProducts.value.map(p => p.revenue),
                        backgroundColor: colorSchemes[2].slice(0, topProducts.value.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Revenue by Product', font: { size: 16 } },
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const product = topProducts.value[context.dataIndex];
                                    return [
                                        `Product: ${product.name}`,
                                        `Revenue: ${formatCompactCurrency(product.revenue)}`,
                                        `Quantity Sold: ${formatCompactNumber(product.quantity)}`
                                    ];
                                }
                            }
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                return formatCompactCurrency(value);
                            },
                            color: '#fff',
                            font: { weight: 'bold', size: 10 }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            };
        };

        const getDeliveryChartConfig = () => {
            return {
                type: 'doughnut',
                data: {
                    labels: deliveryMetrics.value.map(m => m.label),
                    datasets: [{
                        data: deliveryMetrics.value.map(m => m.value),
                        backgroundColor: [
                            colorSchemes[1][0],
                            colorSchemes[3][0],
                            '#6b7280'        
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Delivery Performance', font: { size: 16 } },
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.label}: ${context.raw} orders`
                            }
                        }
                    }
                }
            };
        };

        const formatDateLabel = (dateString) => {
            const date = new Date(dateString);
            return date.getDate() + '/' + (date.getMonth() + 1);
        };

        const formatDateFull = (dateString) => {
            return new Date(dateString).toLocaleDateString();
        };

        const formatMonthLabel = (monthString) => {
            const [year, month] = monthString.split('-');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return monthNames[parseInt(month) - 1];
        };

        const formatMonthFull = (monthString) => {
            const [year, month] = monthString.split('-');
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        };

        const truncateProductName = (name) => {
            if (name.length > 15) {
                return name.substring(0, 12) + '...';
            }
            return name;
        };

        const cardStyle = (index) => {
            const colors = [
                { border: '#4f46e5', bg: 'linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%)' },
                { border: '#10b981', bg: 'linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%)' },
                { border: '#6366f1', bg: 'linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%)' },
                { border: '#ec4899', bg: 'linear-gradient(135deg, #ffffff 0%, #fdf2f8 100%)' }
            ];
            return {
                borderLeft: `4px solid ${colors[index].border}`,
                background: colors[index].bg
            };
        };

        const positiveStyle = { color: '#10b981' };
        const negativeStyle = { color: '#ef4444' };

        return {
            selectedPeriod,
            selectedYear,
            selectedChartType,
            availableYears,
            totalRevenue,
            totalOrders,
            activeCustomers,
            avgOrderValue,
            revenueChange,
            orderChange,
            customerChange,
            aovChange,
            topCustomers,
            topProducts,
            deliveryPerformance,
            deliveryMetrics,
            salesChart,
            ordersChart,
            customersChart,
            productsChart,
            deliveryChart,
            formatCompactCurrency,
            formatCompactNumber,
            formatPercentage,
            cardStyle,
            positiveStyle,
            negativeStyle,
            updateCharts
        };
    }
};
</script>

<style scoped>
.analytics-dashboard {
    padding: 1rem;
    font-family: 'Inter', sans-serif;
    color: #1f2937;
    max-width: 100%;
    overflow-x: hidden;
    box-sizing: border-box;
}

.dashboard-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.control-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1 1 auto;
    min-width: 200px;
}

.control-group label {
    font-weight: 500;
    font-size: 0.875rem;
    white-space: nowrap;
}

.control-group select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: white;
    font-size: 0.875rem;
    min-width: 120px;
    width: 100%;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.summary-card {
    padding: 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    min-width: 0; /* Prevent overflow */
}

.card-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.card-content {
    min-width: 0; /* Prevent text overflow */
}

.card-content h3 {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-content p {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-content .change-text {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: normal;
}

.card-content i {
    margin-right: 0.25rem;
}

.chart-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.chart-container {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    min-width: 0; /* Prevent overflow */
}

.chart-container.full-width {
    grid-column: 1 / -1;
}

.chart-container h2 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chart-wrapper {
    position: relative;
    height: 300px;
    width: 100%;
    min-width: 0; /* Prevent overflow */
}

.delivery-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.delivery-stat {
    text-align: center;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.stat-label i {
    font-size: 1rem;
}

.stat-label.on-time {
    color: #065f46;
}

.stat-label.late {
    color: #b91c1c;
}

.stat-label.pending {
    color: #4b5563;
}

@media (max-width: 768px) {
    .dashboard-controls {
        flex-direction: column;
        align-items: stretch;
    }

    .control-group {
        width: 100%;
        min-width: 0;
    }

    .control-group select {
        width: 100%;
    }

    .summary-cards {
        grid-template-columns: 1fr 1fr;
    }

    .chart-row {
        grid-template-columns: 1fr;
    }

    .chart-wrapper {
        height: 250px;
    }
}

@media (max-width: 480px) {
    .summary-cards {
        grid-template-columns: 1fr;
    }

    .chart-container {
        padding: 0.75rem;
    }

    .chart-wrapper {
        height: 220px;
    }

    .delivery-stats {
        grid-template-columns: 1fr;
    }
}
</style>