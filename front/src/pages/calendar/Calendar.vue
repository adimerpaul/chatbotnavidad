<template>
  <q-page class="q-pa-xs">
    <q-card flat bordered>
<!--      <q-card-section class="text-h6 text-center">-->
<!--        Calendario de Horarios-->
<!--      </q-card-section>-->
      <q-separator />
      <q-card-section class="q-pa-xs">
        <div class="row">
          <div class="col-12 col-md-4">
            <q-select
              outlined dense emit-value map-options
              v-model="selectedDoctor"
              :options="doctores"
              option-label="name"
              option-value="id"
              label="Seleccionar doctor"
              @update:model-value="loadEvents"
            />
          </div>
        </div>
        <full-calendar :options="calendarOptions" />
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction'; // para arrastrar/seleccionar
import esLocale from '@fullcalendar/core/locales/es';

export default defineComponent({
  name: 'CalendarPage',
  components: {
    FullCalendar
  },
  data() {
    return {
      doctores: [],
      selectedDoctor: null,
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        locale: esLocale,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [],
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00'
      }
    }
  },
  mounted() {
    // this.loadEvents();
    this.getDoctores();
  },
  methods: {
    getDoctores() {
      this.$axios.get('/doctores-select').then(res => {
        this.doctores = res.data;
      });
    },
    loadEvents(doctorId) {
      this.$axios.get(`/doctor-horarios/${doctorId}`).then(res => {
        this.calendarOptions.events = res.data;
      }).catch(() => {
        this.$alert.error('No se pudo cargar los horarios');
      });
    }
  }
});
</script>

<style>
@import "@fullcalendar/common/main.css";
</style>
