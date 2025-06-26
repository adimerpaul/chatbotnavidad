<template>
  <q-page class="q-pa-xs">
    <q-card flat bordered>
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
              :loading="loading"
            />
          </div>
          <div class="col-12 col-md-3 flex flex-center">
            <q-btn
              icon="refresh"
              color="primary"
              label="Actualizar eventos"
              @click="loadEvents(selectedDoctor)"
              :disable="!selectedDoctor"
              no-caps
              dense
              :loading ="loading"
            />
          </div>
          <div class="col-12 col-md-5 flex flex-center">
            <q-btn
              icon="picture_as_pdf"
              color="red"
              label="Reporte PDF"
              @click="descargarReporte"
              :disable="!selectedDoctor"
              no-caps
              dense
              :loading="loading"
            />
          </div>
        </div>
        <div class="q-pa-sm scroll q-mt-md">
          <full-calendar ref="calendar" :options="calendarOptions" />
        </div>
      </q-card-section>
    </q-card>
    <q-dialog v-model="show">
      <q-card style="min-width: 400px">
        <q-card-section class="text-h6">
          {{ editando ? 'Editar cita' : 'Reservar cita' }} - {{ fecha }}
        </q-card-section>

        <q-separator />

        <q-form @submit.prevent="guardarCita">
          <q-card-section>
            <q-input
              v-model="cliente"
              label="Nombre del Cliente"
              outlined dense
              :rules="[val => !!val || 'Este campo es obligatorio']"
            />
            <q-input
              v-model="hora"
              type="time"
              label="Hora"
              outlined dense
              class="q-mt-sm"
              :rules="[val => !!val || 'Seleccione una hora válida']"
            />
            <q-select
              v-model="duracion"
              :options="[15, 20, 25, 30]"
              label="Duración (minutos)"
              outlined dense
              class="q-mt-sm"
              :rules="[val => !!val || 'Seleccione duración']"
            />
            <q-input
              v-model="observacion"
              type="textarea"
              label="Observación"
              outlined dense
              class="q-mt-sm"
            />
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Cerrar" color="grey" v-close-popup :loading="loading" />
            <q-btn
              flat
              label="Cancelar cita"
              color="negative"
              v-if="editando"
              @click="cancelarCita"
              :loading="loading"
            />
            <q-btn
              flat
              label="Guardar"
              color="primary"
              type="submit"
              :loading="loading"
            />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';
import moment from "moment";

export default defineComponent({
  name: 'CalendarPage',
  components: {
    FullCalendar
  },
  data() {
    return {
      loading: false,
      doctores: [],
      selectedDoctor: null,
      show: false,
      fecha: '',
      cliente: '',
      hora: '10:00',
      duracion: 20,
      observacion: '',
      editando: false,
      idCita: null,
      calendarOptions: {
        height: 'auto',
        // slotDuration: '00:15:00',
        // slotLabelInterval: '00:30:00',
        dateClick: this.reservarDia,
        eventClick: this.editarCita,
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        locale: esLocale,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        allDaySlot: false,
        events: [],
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00'
      }
    }
  },
  mounted() {
    this.getDoctores();
    // if (this.$q.screen.lt.md) {
    //   this.calendarOptions.initialView = 'timeGridDay';
    // }
  },
  // watch: {
  //   '$q.screen.name'(val) {
  //     if (val === 'xs' || val === 'sm') {
  //       const api = this.$refs.calendar.getApi();
  //       api.changeView('timeGridDay');
  //     }
  //   }
  // },
  methods: {
    descargarReporte() {
      // dilao para fecha
      this.$q.dialog({
        title: 'Generar reporte diario',
        // colocar el nombre del edoctos
        message: 'Doctor: ' + (this.doctores.find(d => d.id === this.selectedDoctor)?.name || 'No seleccionado'),
        prompt: {
          type: 'date',
          model: moment().format('YYYY-MM-DD'),
        },
        cancel: true,
        persistent: true
      }).onOk((fecha) => {
        this.loading = true;
        this.$axios.post('/appointments/reporte-diario', {
          doctor_id: this.selectedDoctor,
          fecha: fecha
        }, { responseType: 'blob' })
          .then(res => {
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `reporte_citas_${this.selectedDoctor}_${this.fecha}.pdf`);
            document.body.appendChild(link);
            link.click();
            link.remove();
          })
          .catch(() => {
            this.$q.notify({ type: 'negative', message: 'No se pudo generar el reporte' });
          })
          .finally(() => {
            this.loading = false;
          });
      });
    },
    editarCita(info) {
      const evento = info.event;

      this.editando = true;
      this.idCita = evento.extendedProps.id;
      this.fecha = evento.startStr.split('T')[0];
      this.hora = evento.startStr.split('T')[1]?.substring(0, 5) || '10:00';
      this.cliente = evento.title;
      this.duracion = evento.extendedProps.duracion || 20;
      this.observacion = evento.extendedProps.observacion || '';
      this.show = true;
    },
    reservarDia(info) {
      if (!this.selectedDoctor) {
        this.$alert.error('Debe seleccionar un doctor primero');
        return;
      }
      this.fecha = info.dateStr.split('T')[0];
      this.hora = info.dateStr.split('T')[1]?.substring(0, 5) || '10:00';
      this.duracion = 15;
      this.cliente = '';
      this.observacion = '';
      this.editando = false;
      this.idCita = null;
      this.show = true;
    },

    guardarCita() {
      const payload = {
        doctor_id: this.selectedDoctor,
        cliente: this.cliente,
        fecha: this.fecha,
        hora: this.hora,
        duracion: this.duracion,
        observacion: this.observacion
      };

      const url = this.editando
        ? `/appointments/${this.idCita}`
        : '/appointments';

      const method = this.editando ? 'put' : 'post';

      this.loading = true;
      this.$axios[method](url, payload).then(() => {
        this.$q.notify({ type: 'positive', message: this.editando ? 'Cita actualizada' : 'Cita registrada' });
        this.show = false;
        this.loadEvents(this.selectedDoctor);
      }).catch((res) => {
        if (res.response?.status === 409 && res.response?.data?.ocupados) {
          const ocupados = res.response.data.ocupados
            .map(cita => {
              const inicio = moment(cita.fecha_inicio).format('HH:mm');
              const fin = moment(cita.fecha_fin).format('HH:mm');
              return `${inicio} - ${fin}`;
            })
            .join('<br>');
          this.$q.dialog({
            title: 'Horario ocupado',
            message: `El horario seleccionado ya está ocupado.<br><br><b>Horarios ocupados:</b><br>${ocupados}`,
            html: true
          });
        } else {
          this.$alert.error(res.response?.data?.message || 'Error al guardar la cita');
        }
      }).finally(() => {
        this.loading = false;
      });
    },

    cancelarCita() {
      this.$q.dialog({
        title: '¿Cancelar cita?',
        message: '¿Está seguro que desea cancelar esta cita?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true;
        this.$axios.put(`/appointments/${this.idCita}/cancelar`).then(() => {
          this.$q.notify({ type: 'warning', message: 'Cita cancelada' });
          this.show = false;
          this.loadEvents(this.selectedDoctor);
        }).finally(() => {
          this.loading = false;
        });
      });
    },
    getDoctores() {
      this.loading = true;
      this.$axios.get('/doctores-select').then(res => {
        this.doctores = res.data;
      }).finally(() => {
        this.loading = false;
      });
    },
    loadEvents(doctorId) {
      const calendarApi = this.$refs.calendar.getApi();
      const view = calendarApi.view;
      const tipoVista = view.type;
      const inicio = view.currentStart.toISOString().split('T')[0];
      const fin = view.currentEnd.toISOString().split('T')[0];
      this.loading = true;
      this.$axios.get(`/doctor-horarios/${doctorId}`, {
        params: {
          tipo: tipoVista,
          desde: inicio,
          hasta: fin
        }
      }).then(res => {
        // No hacer map. FullCalendar puede renderizar ambos tipos si se devuelven correctamente
        this.calendarOptions.events = res.data;
      }).catch(() => {
        this.$alert.error('No se pudo cargar los horarios');
      }).finally(() => {
        this.loading = false;
      });
    }
  }
});
</script>

<style>
@import "@fullcalendar/common/main.css";
.fc {
  max-width: 100%;
  overflow-x: auto;
}
</style>
