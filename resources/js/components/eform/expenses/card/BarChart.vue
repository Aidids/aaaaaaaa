<template>
  <Chart type="bar" :height="height" :data="chartData"
         :options="chartOption"/>
</template>

<script>
import Chart from "primevue/chart";

export default {
  components: {Chart},
  props: {
    breakdownData: {
      type: Object,
      default: {}
    },
    responsive: {
      type: Boolean,
      default: true,
    },
    height: {
      type: Number,
      default: 250
    },
  },
  data() {
    return {
      chartData: {},
      chartOption: {
        responsive: true,
        maintainAspectRatio: true,
      },
      barChartDefaultData: [
        {
          label: 'Meal Allowance',
          value: [0, 0, 0],
          color: '--pink-300',
        },
        {
          label: 'Offshore',
          value: [0, 0, 0],
          color: '--pink-600',
        },
        {
          label: 'Outstation (Local)',
          value: [0, 0, 0],
          color: '--red-500',
        },
        {
          label: 'Outstation (Oversea)',
          value: [0, 0, 0],
          color: '--orange-500',
        },
        {
          label: 'Others',
          value: [0, 0, 0],
          color: '--yellow-500',
        },
        {
          label: 'Mileage',
          value: [0, 0, 0],
          color: '--cyan-500',
        },
        {
          label: 'Fuel',
          value: [0, 0, 0],
          color: '--blue-300',
        },
        {
          label: 'Parking',
          value: [0, 0, 0],
          color: '--blue-600',
        },
        {
          label: 'Toll',
          value: [0, 0, 0],
          color: '--indigo-500',
        },
        {
          label: 'Public Transport',
          value: [0, 0, 0],
          color: '--purple-500',
        },
        {
          label: 'Accommodation',
          value: [0, 0, 0],
          color: '--green-500',
        },
        {
          label: 'Refreshment',
          value: [0, 0, 0],
          color: '--teal-300',
        },
        {
          label: 'Others',
          value: [0, 0, 0],
          color: '--teal-600'
        }
      ]
    }
  },
  mounted() {
    this.chartData = this.setChartData()
    this.chartOption = this.setChartOptions()
  },
  methods: {
    mapInputToOutput() {
      const output = this.barChartDefaultData;

      if (this.breakdownData.allowance) {
        this.breakdownData.allowance.forEach((entry) => {
          if (entry.allowance_type === 'Meal Allowance') {
            output[0].value[0] = entry.total_amount;
          } else if (entry.allowance_type === 'Offshore') {
            output[1].value[0] = entry.total_amount;
          } else if (entry.allowance_type === 'Outstation') {
            output[2].value[0] = entry.total_amount;
          } else if (entry.allowance_type === 'Oversea') {
            output[3].value[0] = entry.total_amount;
          } else if (entry.allowance_type === 'Others') {
            output[4].value[0] = entry.total_amount;
          }
        });
      }

      if (this.breakdownData.transport) {
        this.breakdownData.transport.forEach((entry) => {
          if (entry.transport_type === 'Mileage') {
            output[5].value[1] = entry.total_amount;
          } else if (entry.transport_type === 'Fuel') {
            output[6].value[1] = entry.total_amount;
          } else if (entry.transport_type === 'Parking') {
            output[7].value[1] = entry.total_amount;
          } else if (entry.transport_type === 'Toll') {
            output[8].value[1] = entry.total_amount;
          } else if (entry.transport_type === 'Public Transport') {
            output[9].value[1] = entry.total_amount;
          }
        });
      }

      if (this.breakdownData.expenses) {
        this.breakdownData.expenses.forEach((entry) => {
          if (entry.expense_type === 'Accommodation') {
            output[10].value[2] = entry.total_amount;
          } else if (entry.expense_type === 'Refreshment') {
            output[11].value[2] = entry.total_amount;
          } else if (entry.expense_type === 'Others') {
            output[12].value[2] = entry.total_amount;
          }
        });
      }
      return output;
    },

    setChartData() {
      const documentStyle = getComputedStyle(document.body);

      const dataset = this.mapInputToOutput()
      const data = dataset.map((data) => {

        return {
          barPercentage: 0.8,
          categoryPercentage: 1,
          label: data.label,
          data: data.value,
          backgroundColor: [documentStyle.getPropertyValue(data.color)],
          borderWidth: 1,
          borderRadius: {
            topRight: 5,
            topLeft: 5,
            bottomRight: 5,
            bottomLeft: 5,
          },
          borderSkipped: false,
          borderColor: 'transparent',
        };
      });

      return {
        labels: ['Allowance', 'Transport', 'Expenses'],
        datasets: data,
      };
    },

    setChartOptions() {
      return {
        responsive: this.responsive,
        maintainAspectRatio: false,
        animation: false,
        scales: {
          x: {
            stacked: true,
            grace: '20%'
          },
          y: {
            stacked: true,
            grid: {
              display: false
            },
            beginAtZero: true
          },
        },
        indexAxis: 'y',
        plugins: {
          legend: {
            padding: 20,
            labels: {
              boxWidth: 10,
              usePointStyle: true,
            }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.dataset.label || '';

                if (label) {
                  label += ': ';
                }

                if (context.parsed !== null) {
                  label += 'RM' + context.parsed.x.toFixed(2);
                }
                return label;
              }
            }
          },
        }
      };
    },

  }
}
</script>


