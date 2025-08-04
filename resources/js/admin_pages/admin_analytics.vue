<template>
  <div class="analytics-dashboard">
    <!-- Dashboard Controls -->
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
        <select v-model="selectedYear" @change="updateCharts">
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>
      
      <div class="control-group">
        <label>Chart Type:</label>
        <select v-model="selectedChartType" @change="updateCharts">
          <option value="line">Line Chart</option>
          <option value="bar">Bar Chart</option>
          <option value="pie">Pie Chart</option>
          <option value="doughnut">Doughnut</option>
          <option value="radar">Radar</option>
        </select>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="summary-card" :style="cardStyle(0)">
        <div class="card-icon">💰</div>
        <div class="card-content">
          <h3>Total Revenue</h3>
          <p>{{ formatCurrency(totalRevenue) }}</p>
          <p :style="revenueChange >= 0 ? positiveStyle : negativeStyle">
            {{ revenueChange >= 0 ? '↑' : '↓' }} {{ Math.abs(revenueChange) }}% 
            <span class="change-text">{{ revenueChange >= 0 ? 'increase' : 'decrease' }}</span>
          </p>
        </div>
      </div>

      <div class="summary-card" :style="cardStyle(1)">
        <div class="card-icon">📦</div>
        <div class="card-content">
          <h3>Total Orders</h3>
          <p>{{ totalOrders }}</p>
          <p :style="orderChange >= 0 ? positiveStyle : negativeStyle">
            {{ orderChange >= 0 ? '↑' : '↓' }} {{ Math.abs(orderChange) }}% 
            <span class="change-text">{{ orderChange >= 0 ? 'increase' : 'decrease' }}</span>
          </p>
        </div>
      </div>

      <div class="summary-card" :style="cardStyle(2)">
        <div class="card-icon">👥</div>
        <div class="card-content">
          <h3>Active Customers</h3>
          <p>{{ activeCustomers }}</p>
          <p :style="customerChange >= 0 ? positiveStyle : negativeStyle">
            {{ customerChange >= 0 ? '↑' : '↓' }} {{ Math.abs(customerChange) }}% 
            <span class="change-text">{{ customerChange >= 0 ? 'growth' : 'decline' }}</span>
          </p>
        </div>
      </div>

      <div class="summary-card" :style="cardStyle(3)">
        <div class="card-icon">📊</div>
        <div class="card-content">
          <h3>Avg. Order Value</h3>
          <p>{{ formatCurrency(avgOrderValue) }}</p>
          <p :style="aovChange >= 0 ? positiveStyle : negativeStyle">
            {{ aovChange >= 0 ? '↑' : '↓' }} {{ Math.abs(aovChange) }}% 
            <span class="change-text">{{ aovChange >= 0 ? 'increase' : 'decrease' }}</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Main Charts Row -->
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

    <!-- Secondary Charts Row -->
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

    <!-- Delivery Performance -->
    <div class="chart-row">
      <div class="chart-container">
        <h2>Delivery Performance</h2>
        <div class="chart-wrapper">
          <canvas ref="deliveryChart"></canvas>
        </div>
        <div class="performance-metrics">
          <div v-for="(metric, index) in deliveryMetrics" :key="index" :style="metricStyle(index)">
            <div>{{ metric.value }}</div>
            <div>{{ metric.label }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import { useOrderProcessingStore } from '@/stores/OrderProcessingStore';
import { useAdminProductStore } from '@/stores/adminProducts';
import { useAllCustomerStore } from '@/stores/AllCustomerStore';
import { Chart, registerables } from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';
import ChartDataLabels from 'chartjs-plugin-datalabels';

// Register Chart.js components
Chart.register(...registerables, zoomPlugin, ChartDataLabels);

export default {
  name: 'AnalyticsDashboard',
  setup() {
    const orderStore = useOrderProcessingStore();
    const productStore = useAdminProductStore();
    const customerStore = useAllCustomerStore();

    // Refs
    const selectedPeriod = ref('month');
    const selectedYear = ref(new Date().getFullYear());
    const selectedChartType = ref('line');
    const salesChart = ref(null);
    const ordersChart = ref(null);
    const customersChart = ref(null);
    const productsChart = ref(null);
    const deliveryChart = ref(null);

    // Color schemes
    const colorSchemes = [
      ['#4f46e5', '#6366f1', '#818cf8', '#a5b4fc', '#c7d2fe'], // Indigo
      ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5'], // Emerald
      ['#ec4899', '#f472b6', '#f9a8d4', '#fbcfe8', '#fce7f3'], // Pink
      ['#f59e0b', '#fbbf24', '#fcd34d', '#fde68a', '#fef3c7']  // Amber
    ];

    // Initialize stores
    onMounted(async () => {
      await Promise.all([
        orderStore.fetchAllOrders(),
        productStore.fetchProducts(),
        customerStore.fetchAllCustomers()
      ]);
      initCharts();
    });

    // Computed properties
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
        noDate: 0
      };
      
      orderStore.orders.forEach(order => {
        if (order.status === 'Completed') {
          if (order.delivered_on_time === true) {
            performance.onTime++;
          } else if (order.delivered_on_time === false) {
            performance.late++;
          } else {
            performance.noDate++;
          }
        }
      });
      
      return performance;
    });

    const deliveryMetrics = computed(() => [
      { label: 'On Time', value: deliveryPerformance.value.onTime },
      { label: 'Late', value: deliveryPerformance.value.late },
      { label: 'No Date', value: deliveryPerformance.value.noDate }
    ]);

    const groupedSalesData = computed(() => {
      if (!orderStore.orders.length) return [];
      
      const filtered = orderStore.orders.filter(order => {
        const orderYear = new Date(order.order_datetime).getFullYear();
        return orderYear === selectedYear.value;
      });

      if (selectedPeriod.value === 'day') {
        return groupByDay(filtered);
      } else if (selectedPeriod.value === 'week') {
        return groupByWeek(filtered);
      } else if (selectedPeriod.value === 'month') {
        return groupByMonth(filtered);
      } else {
        return groupByYear(filtered);
      }
    });

    // Change calculations
    const revenueChange = computed(() => calculatePercentageChange('revenue'));
    const orderChange = computed(() => calculatePercentageChange('orders'));
    const customerChange = computed(() => calculatePercentageChange('customers'));
    const aovChange = computed(() => calculatePercentageChange('aov'));

    // Methods
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

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(value);
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

    const groupByYear = (orders) => {
      const yearsMap = new Map();
      
      orders.forEach(order => {
        const date = new Date(order.order_datetime);
        const yearKey = date.getFullYear().toString();
        
        const existing = yearsMap.get(yearKey) || {
          year: yearKey,
          revenue: 0,
          orders: 0
        };
        
        existing.revenue += order.total_payment;
        existing.orders += 1;
        yearsMap.set(yearKey, existing);
      });
      
      return Array.from(yearsMap.values()).sort((a, b) => a.year.localeCompare(b.year));
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
          // Simplified - in a real app you'd track customer counts per period
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
      const isPieLike = ['pie', 'doughnut', 'radar'].includes(selectedChartType.value);
      const labels = groupedSalesData.value.map(item => {
        if (selectedPeriod.value === 'day') return item.date;
        if (selectedPeriod.value === 'week') return item.week;
        if (selectedPeriod.value === 'month') return item.month;
        return item.year;
      });
      
      return {
        type: isPieLike ? 'line' : selectedChartType.value,
        data: {
          labels,
          datasets: [{
            label: 'Revenue',
            data: groupedSalesData.value.map(item => item.revenue),
            backgroundColor: colorSchemes[0][0],
            borderColor: colorSchemes[0][0],
            borderWidth: 2,
            fill: selectedChartType.value === 'area',
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: { display: true, text: 'Sales Revenue Trend', font: { size: 16 } },
            legend: { position: 'top' },
            tooltip: {
              callbacks: {
                label: (context) => `Revenue: ${formatCurrency(context.raw)}`
              }
            },
            zoom: {
              zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
              pan: { enabled: true, mode: 'xy' }
            }
          },
          scales: !isPieLike ? {
            y: {
              beginAtZero: false,
              ticks: {
                callback: (value) => formatCurrency(value)
              }
            }
          } : {}
        }
      };
    };

    const getOrdersChartConfig = () => {
      const isPieLike = ['pie', 'doughnut', 'radar'].includes(selectedChartType.value);
      const labels = groupedSalesData.value.map(item => {
        if (selectedPeriod.value === 'day') return item.date;
        if (selectedPeriod.value === 'week') return item.week;
        if (selectedPeriod.value === 'month') return item.month;
        return item.year;
      });
      
      return {
        type: isPieLike ? 'line' : selectedChartType.value,
        data: {
          labels,
          datasets: [{
            label: 'Orders',
            data: groupedSalesData.value.map(item => item.orders),
            backgroundColor: colorSchemes[1][0],
            borderColor: colorSchemes[1][0],
            borderWidth: 2,
            fill: selectedChartType.value === 'area',
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: { display: true, text: 'Order Count Trend', font: { size: 16 } },
            legend: { position: 'top' },
            tooltip: {
              callbacks: {
                label: (context) => `${context.raw} orders`
              }
            },
            zoom: {
              zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
              pan: { enabled: true, mode: 'xy' }
            }
          },
          scales: !isPieLike ? {
            y: { beginAtZero: true, ticks: { precision: 0 } }
          } : {}
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
                label: (context) => `${formatCurrency(context.raw)} (${topCustomers.value[context.dataIndex].orders} orders)`
              }
            },
            datalabels: {
              anchor: 'end',
              align: 'top',
              formatter: (value) => formatCurrency(value),
              color: '#4b5563',
              font: { weight: 'bold' }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => formatCurrency(value)
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
          labels: topProducts.value.map(p => p.name),
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
            legend: { position: 'right' },
            tooltip: {
              callbacks: {
                label: (context) => {
                  const product = topProducts.value[context.dataIndex];
                  return [
                    `Product: ${product.name}`,
                    `Revenue: ${formatCurrency(product.revenue)}`,
                    `Quantity Sold: ${product.quantity}`
                  ];
                }
              }
            },
            datalabels: {
              formatter: (value, context) => {
                const product = topProducts.value[context.dataIndex];
                return `${product.name}\n${formatCurrency(value)}`;
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
        type: 'polarArea',
        data: {
          labels: ['On Time', 'Late', 'No Date'],
          datasets: [{
            data: [deliveryPerformance.value.onTime, deliveryPerformance.value.late, deliveryPerformance.value.noDate],
            backgroundColor: [colorSchemes[1][0], colorSchemes[3][0], '#6b7280'],
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
          },
          scales: {
            r: {
              pointLabels: { display: false },
              ticks: { display: false }
            }
          }
        }
      };
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

    const metricStyle = (index) => {
      const styles = [
        { bg: '#d1fae5', text: '#065f46', border: '#10b981' },
        { bg: '#fee2e2', text: '#b91c1c', border: '#ef4444' },
        { bg: '#e5e7eb', text: '#1f2937', border: '#6b7280' }
      ];
      return {
        backgroundColor: styles[index].bg,
        color: styles[index].text,
        border: `1px solid ${styles[index].border}`
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
      formatCurrency,
      cardStyle,
      metricStyle,
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
}

.control-group label {
  font-weight: 500;
  font-size: 0.875rem;
}

.control-group select {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: white;
  font-size: 0.875rem;
  min-width: 120px;
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
}

.card-icon {
  font-size: 1.5rem;
  margin-right: 1rem;
}

.card-content h3 {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.card-content p {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.card-content .change-text {
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: normal;
}

.chart-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.chart-container {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1rem;
}

.chart-container h2 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.chart-wrapper {
  position: relative;
  height: 300px;
  width: 100%;
}

.performance-metrics {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  margin-top: 1rem;
}

.performance-metrics > div {
  padding: 0.5rem;
  border-radius: 0.25rem;
  text-align: center;
}

.performance-metrics > div > div:first-child {
  font-weight: 700;
  font-size: 1.125rem;
}

.performance-metrics > div > div:last-child {
  font-size: 0.75rem;
}

@media (max-width: 768px) {
  .dashboard-controls {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .control-group {
    width: 100%;
  }
  
  .control-group select {
    width: 100%;
  }
  
  .chart-row {
    grid-template-columns: 1fr;
  }
  
  .summary-cards {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 480px) {
  .summary-cards {
    grid-template-columns: 1fr;
  }
}
</style>