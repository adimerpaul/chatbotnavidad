<template>
  <q-page class="q-pa-xs">
    <q-card flat bordered>
      <q-card-section class="text-h6 text-center">
        Calendario de Horarios
      </q-card-section>
      <q-separator />
      <q-card-section class="q-pa-xs">
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
    };
  },
  mounted() {
    // this.loadEvents();
  },
  methods: {
    async loadEvents() {
      try {
        const res = await this.$axios.get('/doctor-horarios'); // ruta que devolverá los horarios
        const events = res.data.map(item => {
          return {
            title: item.doctor,
            start: item.start,
            end: item.end,
            daysOfWeek: item.days, // opcional si quieres repetir por días
            allDay: false
          };
        });
        this.calendarOptions.events = events;
      } catch (err) {
        console.error(err);
        this.$alert.error('Error al cargar horarios');
      }
    }
  }
});
</script>

<style>
@import "@fullcalendar/common/main.css";
</style>
