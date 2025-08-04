<template>
  <div class="analytics-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
      <h1>E-commerce Analytics</h1>
      <div class="header-controls">
        <select v-model="selectedPeriod" class="control-select">
          <option value="day">Daily</option>
          <option value="week">Weekly</option>
          <option value="month">Monthly</option>
          <option value="year">Yearly</option>
        </select>
        <select v-model="selectedYear" class="control-select">
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
        <select v-model="selectedChartType" class="control-select">
          <option value="line">Line Chart</option>
          <option value="bar">Bar Chart</option>
          <option value="pie">Pie Chart</option>
          <option value="doughnut">Doughnut Chart</option>
          <option value="radar">Radar Chart</option>
        </select>
      </div>
    </div>
    <div class="summary-cards">
      <div class="summary-card" style="border-left: 4px solid #4f46e5; background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%)">
        <div class="card-icon">💰</div>
        <div class="card-content">
          <h3>Total Revenue</h3>
          <p>{{ formatCurrency(totalRevenue) }}</p>
          <p :style="revenueChange >= 0 ? 'color: #10b981' : 'color: #ef4444'">
            {{ revenueChange >= 0 ? '↑' : '↓' }} {{ Math.abs(revenueChange) }}% 
            <span style="color: #6b7280">{{ revenueChange >= 0 ? 'increase' : 'decrease' }}</span>
          </p>
        </div>
      </div>

      <div class="summary-card" style="border-left: 4px solid #10b981; background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%)">
        <div class="card-icon">📦</div>
        <div class="card-content">
          <h3>Total Orders</h3>
          <p>{{ totalOrders }}</p>
          <p :style="orderChange >= 0 ? 'color: #10b981' : 'color: #ef4444'">
            {{ orderChange >= 0 ? '↑' : '↓' }} {{ Math.abs(orderChange) }}% 
            <span style="color: #6b7280">{{ orderChange >= 0 ? 'increase' : 'decrease' }}</span>
          </p>
        </div>
      </div>

      <div class="summary-card" style="border-left: 4px solid #6366f1; background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%)">
        <div class="card-icon">👥</div>
        <div class="card-content">
          <h3>Active Customers</h3>
          <p>{{ activeCustomers }}</p>
          <p :style="customerChange >= 0 ? 'color: #10b981' : 'color: #ef4444'">
            {{ customerChange >= 0 ? '↑' : '↓' }} {{ Math.abs(customerChange) }}% 
            <span style="color: #6b7280">{{ customerChange >= 0 ? 'growth' : 'decline' }}</span>
          </p>
        </div>
      </div>

      <div class="summary-card" style="border-left: 4px solid #ec4899; background: linear-gradient(135deg, #ffffff 0%, #fdf2f8 100%)">
        <div class="card-icon">📊</div>
        <div class="card-content">
          <h3>Avg. Order Value</h3>
          <p>{{ formatCurrency(avgOrderValue) }}</p>
          <p :style="aovChange >= 0 ? 'color: #10b981' : 'color: #ef4444'">
            {{ aovChange >= 0 ? '↑' : '↓' }} {{ Math.abs(aovChange) }}% 
            <span style="color: #6b7280">{{ aovChange >= 0 ? 'increase' : 'decrease' }}</span>
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

      <div class="chart-container">
        <h2>Delivery Performance</h2>
        <div class="chart-wrapper">
          <canvas ref="deliveryChart"></canvas>
        </div>
        <div class="performance-metrics">
          <div style="background-color: #d1fae5">
            <div style="color: #065f46">{{ deliveryPerformance.onTime }}</div>
            <div style="color: #047857; font-size: 0.75rem">On Time</div>
          </div>
          <div style="background-color: #fee2e2">
            <div style="color: #b91c1c">{{ deliveryPerformance.late }}</div>
            <div style="color: #dc2626; font-size: 0.75rem">Late</div>
          </div>
          <div style="background-color: #e5e7eb">
            <div style="color: #1f2937">{{ deliveryPerformance.noDate }}</div>
            <div style="color: #4b5563; font-size: 0.75rem">No Date</div>
          </div>
        </div>
      </div>
    </div>
    <div class="table-container">
      <h2>Recent Orders</h2>
      <div class="table-filters">
        <select v-model="tableFilters.status" class="filter-select">
          <option value="all">All Statuses</option>
          <option value="Pending">Pending</option>
          <option value="Dispatched">Dispatched</option>
          <option value="Completed">Completed</option>
          <option value="Canceled">Canceled</option>
        </select>
        <input v-model="tableFilters.customer" placeholder="Filter by customer" class="filter-input">
        <input v-model="tableFilters.date" type="date" class="filter-input">
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Status</th>
              <th>Amount</th>
              <th>Products</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in filteredOrders" :key="order.order_id">
              <td>#{{ order.order_id }}</td>
              <td>
                <div class="customer-cell">
                  <img :src="order.customer.profile_pic" alt="">
                  <div>
                    <div>{{ order.customer.name }}</div>
                    <div>{{ order.customer.phone_no }}</div>
                  </div>
                </div>
              </td>
              <td>{{ formatDate(order.order_datetime) }}</td>
              <td>
                <span :class="getStatusClass(order.status)">{{ order.status }}</span>
              </td>
              <td>{{ formatCurrency(order.total_payment) }}</td>
              <td>
                <div class="product-tags">
                  <span v-for="(product, index) in order.products" :key="index">
                    {{ product.product_name }} (×{{ product.quantity }})
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(...registerables, zoomPlugin, ChartDataLabels);
export default {
  name: 'AnalyticsDashboard',
  data() {
    return {
      selectedPeriod: 'month',
      selectedYear: new Date().getFullYear(),
      selectedChartType: 'line',
      availableYears: [2023, 2022, 2021],
      tableFilters: {
        status: 'all',
        customer: '',
        date: ''
      },
      ordersData: [
        {
          order_id: 1,
          order_datetime: '2023-05-15T10:30:00',
          status: 'Completed',
          total_payment: 125.99,
          delivered_on_time: true,
          customer: {
            customer_id: 1,
            name: 'John Doe',
            phone_no: '1234567890',
            profile_pic: 'https://randomuser.me/api/portraits/men/1.jpg'
          },
          products: [
            { product_id: 1, product_name: 'T-Shirt', quantity: 2, price_at_purchase: 25.99 },
            { product_id: 2, product_name: 'Jeans', quantity: 1, price_at_purchase: 74.00 }
          ]
        },
      ],
      salesTrendData: {
        day: [
          { date: '2023-05-01', revenue: 1200, orders: 15 },
          { date: '2023-05-02', revenue: 1800, orders: 20 },
          // More daily data
        ],
        month: [
          { month: '2023-01', revenue: 15000, orders: 120 },
          { month: '2023-02', revenue: 18000, orders: 150 },
          // More monthly data
        ]
      },
      topCustomers: [
        { id: 1, name: 'John Doe', total: 2500, orders: 12 },
        { id: 2, name: 'Jane Smith', total: 1800, orders: 8 },
        // More customers
      ],
      topProducts: [
        { id: 1, name: 'Premium T-Shirt', revenue: 5200, quantity: 120 },
        { id: 2, name: 'Designer Jeans', revenue: 4800, quantity: 80 },
        // More products
      ],
      deliveryPerformance: {
        onTime: 85,
        late: 10,
        noDate: 5
      },
      charts: {
        sales: null,
        orders: null,
        customers: null,
        products: null,
        delivery: null
      }
    };
  },
  computed: {
    totalRevenue() {
      return this.ordersData.reduce((sum, order) => sum + order.total_payment, 0);
    },
    totalOrders() {
      return this.ordersData.length;
    },
    activeCustomers() {
      const customerIds = new Set();
      this.ordersData.forEach(order => {
        if (order.customer?.customer_id) {
          customerIds.add(order.customer.customer_id);
        }
      });
      return customerIds.size;
    },
    avgOrderValue() {
      return this.totalOrders > 0 ? this.totalRevenue / this.totalOrders : 0;
    },
    revenueChange() {
      return 12.5; // Sample change percentage
    },
    orderChange() {
      return 8.3; // Sample change percentage
    },
    customerChange() {
      return 5.7; // Sample change percentage
    },
    aovChange() {
      return 3.2; // Sample change percentage
    },
    filteredOrders() {
      return this.ordersData.filter(order => {
        const matchesStatus = this.tableFilters.status === 'all' || order.status === this.tableFilters.status;
        const matchesCustomer = !this.tableFilters.customer || 
          order.customer.name.toLowerCase().includes(this.tableFilters.customer.toLowerCase());
        const matchesDate = !this.tableFilters.date || 
          order.order_datetime.startsWith(this.tableFilters.date);
        
        return matchesStatus && matchesCustomer && matchesDate;
      });
    },
    currentSalesData() {
      return this.salesTrendData[this.selectedPeriod] || this.salesTrendData.month;
    }
  },
  watch: {
    selectedPeriod() {
      this.updateCharts();
    },
    selectedChartType() {
      this.updateCharts();
    }
  },
  mounted() {
    this.initCharts();
    // In a real app, you would fetch data here:
    // this.fetchData();
  },
  beforeUnmount() {
    // Destroy charts when component is unmounted
    Object.values(this.charts).forEach(chart => {
      if (chart) chart.destroy();
    });
  },
  methods: {
    initCharts() {
      this.charts.sales = this.createChart(this.$refs.salesChart, this.getSalesChartConfig());
      this.charts.orders = this.createChart(this.$refs.ordersChart, this.getOrdersChartConfig());
      this.charts.customers = this.createChart(this.$refs.customersChart, this.getCustomersChartConfig());
      this.charts.products = this.createChart(this.$refs.productsChart, this.getProductsChartConfig());
      this.charts.delivery = this.createChart(this.$refs.deliveryChart, this.getDeliveryChartConfig());
    },
    updateCharts() {
      if (this.charts.sales) {
        this.charts.sales.destroy();
        this.charts.sales = this.createChart(this.$refs.salesChart, this.getSalesChartConfig());
      }
      if (this.charts.orders) {
        this.charts.orders.destroy();
        this.charts.orders = this.createChart(this.$refs.ordersChart, this.getOrdersChartConfig());
      }
    },
    createChart(canvas, config) {
      return new Chart(canvas, config);
    },
    getSalesChartConfig() {
      const isPieLike = ['pie', 'doughnut', 'radar'].includes(this.selectedChartType);
      const labels = this.currentSalesData.map(item => {
        if (this.selectedPeriod === 'day') return item.date;
        if (this.selectedPeriod === 'month') return item.month;
        return item.year;
      });
      
      return {
        type: isPieLike ? 'bar' : this.selectedChartType,
        data: {
          labels,
          datasets: [{
            label: 'Revenue',
            data: this.currentSalesData.map(item => item.revenue),
            backgroundColor: '#4f46e5',
            borderColor: '#4f46e5',
            borderWidth: 2,
            fill: this.selectedChartType === 'area',
            tension: 0.4
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
                label: (context) => `Revenue: ${this.formatCurrency(context.raw)}`
              }
            },
            zoom: {
              zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
              pan: { enabled: true, mode: 'xy' }
            }
          },
          scales: this.selectedChartType !== 'pie' && this.selectedChartType !== 'doughnut' ? {
            y: {
              beginAtZero: false,
              ticks: {
                callback: (value) => this.formatCurrency(value)
              }
            }
          } : {}
        }
      };
    },
    getOrdersChartConfig() {
      const isPieLike = ['pie', 'doughnut', 'radar'].includes(this.selectedChartType);
      const labels = this.currentSalesData.map(item => {
        if (this.selectedPeriod === 'day') return item.date;
        if (this.selectedPeriod === 'month') return item.month;
        return item.year;
      });
      
      return {
        type: isPieLike ? 'bar' : this.selectedChartType,
        data: {
          labels,
          datasets: [{
            label: 'Orders',
            data: this.currentSalesData.map(item => item.orders),
            backgroundColor: '#10b981',
            borderColor: '#10b981',
            borderWidth: 2,
            fill: this.selectedChartType === 'area',
            tension: 0.4
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
          scales: this.selectedChartType !== 'pie' && this.selectedChartType !== 'doughnut' ? {
            y: { beginAtZero: true, ticks: { precision: 0 } }
          } : {}
        }
      };
    },
    getCustomersChartConfig() {
      return {
        type: 'bar',
        data: {
          labels: this.topCustomers.map(c => c.name),
          datasets: [{
            label: 'Total Spending',
            data: this.topCustomers.map(c => c.total),
            backgroundColor: '#6366f1',
            borderColor: '#6366f1',
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
                label: (context) => `${this.formatCurrency(context.raw)} (${this.topCustomers[context.dataIndex].orders} orders)`
              }
            },
            datalabels: {
              anchor: 'end',
              align: 'top',
              formatter: (value) => this.formatCurrency(value),
              color: '#4b5563'
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => this.formatCurrency(value)
              }
            }
          }
        },
        plugins: [ChartDataLabels]
      };
    },
    getProductsChartConfig() {
      return {
        type: 'doughnut',
        data: {
          labels: this.topProducts.map(p => p.name),
          datasets: [{
            data: this.topProducts.map(p => p.revenue),
            backgroundColor: [
              '#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'
            ],
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
                  const product = this.topProducts[context.dataIndex];
                  return [
                    `Product: ${product.name}`,
                    `Revenue: ${this.formatCurrency(product.revenue)}`,
                    `Quantity Sold: ${product.quantity}`
                  ];
                }
              }
            },
            datalabels: {
              formatter: (value, context) => {
                const product = this.topProducts[context.dataIndex];
                return `${product.name}\n${this.formatCurrency(value)}`;
              },
              color: '#fff',
              font: { weight: 'bold', size: 10 }
            }
          }
        },
        plugins: [ChartDataLabels]
      };
    },
    getDeliveryChartConfig() {
      return {
        type: 'polarArea',
        data: {
          labels: ['On Time', 'Late', 'No Date'],
          datasets: [{
            data: [this.deliveryPerformance.onTime, this.deliveryPerformance.late, this.deliveryPerformance.noDate],
            backgroundColor: ['#10b981', '#ef4444', '#6b7280'],
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
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(value);
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString();
    },
    getStatusClass(status) {
      const classes = {
        'Pending': 'status-pending',
        'Dispatched': 'status-dispatched',
        'Completed': 'status-completed',
        'Canceled': 'status-canceled'
      };
      return classes[status] || '';
    }
  }
};
</script>

<style scoped>
.analytics-dashboard {
  padding: 1rem;
  font-family: 'Inter', sans-serif;
  color: #1f2937;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.dashboard-header h1 {
  font-size: 1.5rem;
  font-weight: 700;
}

.header-controls {
  display: flex;
  gap: 0.5rem;
}

.control-select, .filter-select, .filter-input {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: white;
  font-size: 0.875rem;
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

.table-container {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.table-container h2 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.table-filters {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.table-wrapper {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 0.75rem;
  background-color: #f9fafb;
  font-size: 0.75rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
}

td {
  padding: 1rem;
  border-top: 1px solid #e5e7eb;
  font-size: 0.875rem;
}

.customer-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.customer-cell img {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 9999px;
  object-fit: cover;
}

.product-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
}

.product-tags span {
  background-color: #e0e7ff;
  color: #4f46e5;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
}

.status-pending {
  background-color: #fef3c7;
  color: #92400e;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-dispatched {
  background-color: #dbeafe;
  color: #1e40af;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-completed {
  background-color: #dcfce7;
  color: #166534;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-canceled {
  background-color: #fee2e2;
  color: #991b1b;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

@media (max-width: 768px) {
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .header-controls {
    width: 100%;
    flex-wrap: wrap;
  }
  
  .control-select {
    flex-grow: 1;
    min-width: 120px;
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
  
  .table-filters {
    flex-direction: column;
  }
}
</style>