<template>
  <q-page class="q-pa-md">
    <q-table :rows="doctores" :columns="columns" title="Doctores" dense flat bordered wrap-cells
             :filter="filter" :rows-per-page-options="[0]">
      <template v-slot:top-right>
        <q-btn label="Nuevo" icon="add_circle_outline" color="positive" @click="doctorNuevo" class="q-mr-sm" :loading="loading" no-caps/>
        <q-btn label="Actualizar" icon="refresh" color="primary" @click="doctoresGet" :loading="loading" no-caps/>
        <q-input v-model="filter" label="Buscar" dense outlined>
          <template v-slot:append><q-icon name="search" /></template>
        </q-input>
      </template>
      <template v-slot:body-cell-horarios="props">
        <q-td :props="props">
          <div v-if="props.row.schedules?.length">
            <div
              v-for="(s, index) in props.row.schedules"
              :key="index"
              class="text-caption text-grey-8"
            >
              <q-icon name="schedule" size="14px" class="q-mr-xs" />
              {{ s.days }}: {{ s.available_from }} - {{ s.available_to }}
            </div>
          </div>
          <div v-else class="text-grey">Sin horarios</div>
<!--          <pre>{{ props.row }}</pre>-->
        </q-td>
      </template>

      <template v-slot:body-cell-actions="props">
        <q-td :props="props">
          <q-btn-dropdown label="Opciones" dense no-caps color="primary" size="10px">
            <q-list>
              <q-item clickable @click="doctorEditar(props.row)" v-close-popup>
                <q-item-section avatar><q-icon name="edit" /></q-item-section>
                <q-item-section><q-item-label>Editar</q-item-label></q-item-section>
              </q-item>
              <q-item clickable @click="horariosGestionar(props.row)" v-close-popup>
                <q-item-section avatar><q-icon name="event" /></q-item-section>
                <q-item-section><q-item-label>Horarios</q-item-label></q-item-section>
              </q-item>
              <q-item clickable @click="doctorEliminar(props.row.id)" v-close-popup>
                <q-item-section avatar><q-icon name="delete" /></q-item-section>
                <q-item-section><q-item-label>Eliminar</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
    </q-table>

    <!-- Dialogo Doctor -->
    <q-dialog v-model="dialogDoctor" persistent>
      <q-card style="width: 400px;">
        <q-card-section>
          <div class="text-h6">{{ doctor.id ? 'Editar' : 'Nuevo' }} Doctor</div>
        </q-card-section>
        <q-card-section>
          <q-form @submit="doctor.id ? doctorActualizar() : doctorGuardar()">
            <q-input v-model="doctor.name" label="Nombre" dense outlined :rules="[val => !!val || 'Campo requerido']"/>
            <q-input v-model="doctor.specialty" label="Especialidad" dense outlined :rules="[val => !!val || 'Campo requerido']"/>
            <div class="text-right">
              <q-btn label="Cancelar" color="negative" @click="dialogDoctor = false" no-caps/>
              <q-btn label="Guardar" color="primary" type="submit" class="q-ml-sm" no-caps :loading="loading"/>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Dialogo Horarios -->
    <q-dialog v-model="dialogHorarios" persistent>
      <q-card style="width: 600px; max-width: 90vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Horarios - {{ doctor.name }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="dialogHorarios = false" />
        </q-card-section>
        <q-card-section>
          <q-form @submit="guardarHorario()">
            <div class="row q-col-gutter-sm">
              <div class="col-4">
                <q-input v-model="horario.available_from" label="Desde" type="time" dense outlined required />
              </div>
              <div class="col-4">
                <q-input v-model="horario.available_to" label="Hasta" type="time" dense outlined required />
              </div>
              <div class="col-4">
                <q-input v-model="horario.days" label="Días" dense outlined required />
              </div>
              <div class="col-4">
                <q-input v-model.number="horario.price" label="Precio (Bs)" type="number" dense outlined required />
              </div>
              <div class="col-8 flex items-center justify-end">
                <q-btn color="primary" label="Guardar horario" icon="add" type="submit" dense no-caps />
              </div>
            </div>
          </q-form>

          <q-table :rows="doctor.schedules || []" :columns="columnsHorarios" dense flat bordered row-key="id" class="q-mt-md"
                   :rows-per-page-options="[0]"
          >
            <template v-slot:body-cell-actions="props">
              <q-td :props="props">
                <q-btn icon="delete" color="negative" dense flat round @click="eliminarHorario(props.row.id)" />
              </q-td>
            </template>
            <template v-slot:body-cell-price="props">
              <q-td :props="props">
                <q-input
                  v-model.number="props.row.price"
                  type="number"
                  dense
                  outlined
                  debounce="800"
                  @update:model-value="actualizarPrecio(props.row)"
                  style="width: 90px"
                />
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      doctores: [],
      doctor: {},
      horario: {
        available_from: '',
        available_to: '',
        days: '',
        price: '',
      },
      dialogDoctor: false,
      dialogHorarios: false,
      loading: false,
      filter: '',
      columns: [
        { name: 'actions', label: 'Acciones', align: 'center' },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left' },
        { name: 'specialty', label: 'Especialidad', field: 'specialty', align: 'left' },
        { name: 'horarios', label: 'Horarios', field: 'schedules', align: 'left' }
      ],
      columnsHorarios: [
        { name: 'available_from', label: 'Desde', field: 'available_from', align: 'left' },
        { name: 'available_to', label: 'Hasta', field: 'available_to', align: 'left' },
        { name: 'days', label: 'Días', field: 'days', align: 'left' },
        { name: 'price', label: 'Precio', field: 'price', align: 'right' },
        { name: 'actions', label: 'Acciones', align: 'center' }
      ],
    }
  },
  mounted() {
    this.doctoresGet()
  },
  methods: {
    actualizarPrecio(horario) {
      this.loading = true;
      this.$axios.put(`doctor-schedules/${horario.id}`, {
        price: horario.price
      }).then(() => {
        this.$alert.success('Precio actualizado');
      }).catch(err => {
        this.$alert.error(err.response?.data?.message || 'Error al actualizar precio');
      }).finally(() => {
        this.loading = false;
      });
    },
    doctoresGet() {
      this.loading = true
      this.$axios.get('doctors').then(res => {
        this.doctores = res.data.map(doc => ({
          ...doc,
          schedules: (doc.schedules || []).map(s => ({
            id: s.id,
            available_from: s.available_from,
            available_to: s.available_to,
            days: s.days,
            price: s.price
          }))
        }))
      }).catch(err => {
        this.$alert.error(err.response?.data?.message || 'Error al obtener doctores')
      }).finally(() => this.loading = false)
    },
    doctorNuevo() {
      this.doctor = { name: '', specialty: '', schedules: [] }
      this.dialogDoctor = true
    },
    doctorEditar(doctor) {
      this.doctor = { ...doctor }
      this.dialogDoctor = true
    },
    doctorGuardar() {
      this.loading = true
      this.$axios.post('doctors', this.doctor).then(() => {
        this.dialogDoctor = false
        this.$alert.success('Doctor creado')
        this.doctoresGet()
      }).catch(err => {
        this.$alert.error(err.response.data.message)
      }).finally(() => this.loading = false)
    },
    doctorActualizar() {
      this.loading = true
      this.$axios.put(`doctors/${this.doctor.id}`, this.doctor).then(() => {
        this.dialogDoctor = false
        this.$alert.success('Doctor actualizado')
        this.doctoresGet()
      }).catch(err => {
        this.$alert.error(err.response.data.message)
      }).finally(() => this.loading = false)
    },
    doctorEliminar(id) {
      this.$alert.dialog('¿Deseas eliminar este doctor?').onOk(() => {
        this.loading = true
        this.$axios.delete(`doctors/${id}`).then(() => {
          this.$alert.success('Doctor eliminado')
          this.doctoresGet()
        }).catch(err => {
          this.$alert.error(err.response.data.message)
        }).finally(() => this.loading = false)
      })
    },
    horariosGestionar(doctor) {
      this.doctor = { ...doctor }
      this.horario = {
        available_from: '',
        available_to: '',
        days: '',
        price: '',
      }
      this.dialogHorarios = true
    },
    guardarHorario() {
      this.loading = true
      this.$axios.post(`doctors/${this.doctor.id}/schedules`, this.horario).then(() => {
        this.$alert.success('Horario guardado')
        this.doctoresGet()
        this.dialogHorarios = false
      }).catch(err => {
        this.$alert.error(err.response.data.message)
      }).finally(() => this.loading = false)
    },
    eliminarHorario(id) {
      this.loading = true
      this.$axios.delete(`doctor-schedules/${id}`).then(() => {
        this.$alert.success('Horario eliminado')
        this.doctoresGet()
        this.dialogHorarios = false
      }).catch(err => {
        this.$alert.error(err.response.data.message)
      }).finally(() => this.loading = false)
    }
  }
}
</script>
