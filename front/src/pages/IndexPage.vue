<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <div class="col-xs-12 col-sm-6 col-md-3"  v-for="(card, index) in cards" :key="index">
        <q-card class=" bg-primary text-white q-pa-none">
          <q-card-section class="text-center">
            <q-icon :name="card.icon" size="42px" />
            <div class="text-h6">{{ card.label }}</div>
            <div class="text-h4">{{ card.value }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>
    <q-separator spaced />
    <div class="row q-mt-md">
      <div class="col-12">
        <apexchart type="line" height="350" :options="chartOptions" :series="series" />
      </div>
    </div>
<!--    <div class="q-mt-md">-->

<!--    </div>-->
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

export default defineComponent({
  name: 'IndexPage',
  components: {
    apexchart: VueApexCharts
  },
  data() {
    return {
      cards: [],
      chartOptions: {
        chart: {
          id: 'respuestas-por-fecha'
        },
        xaxis: {
          type: 'category',  // <-- esta línea es la clave
          categories: []
        },
        title: {
          text: 'Respuestas del mes',
          align: 'left'
        }
      },
      series: [
        {
          name: 'Respuestas',
          data: []
        }
      ]
    };
  },
  mounted() {
    this.getResumen();
  },
  methods: {
    getResumen() {
      this.$axios.get('reportes/resumen')
        .then(res => {
          const data = res.data;

          // Cards
          this.cards = [
            { label: 'Telefonos', value: data.total_historial, icon: 'phone' },
            { label: 'Respondidas', value: data.total_respuestas, icon: 'chat_bubble' },
            { label: 'Preguntas', value: data.total_preguntas, icon: 'help_outline' },
            { label: 'Doctores', value: data.total_doctores, icon: 'medical_services' },
          ];

          // Gráfico
          const fechas = data.respuestas_por_fecha.map(d => d.fecha);
          const cantidades = data.respuestas_por_fecha.map(d => d.cantidad);

          // this.chartOptions.xaxis.categories = fechas;
          this.chartOptions = {
            ...this.chartOptions,
            xaxis: {
              ...this.chartOptions.xaxis,
              categories: fechas
            }
          };
          this.series[0].data = cantidades;
        })
        .catch(err => {
          this.$alert.error(err.response?.data?.message || 'Error al obtener datos del resumen');
        });
    }
  }
});
</script>
