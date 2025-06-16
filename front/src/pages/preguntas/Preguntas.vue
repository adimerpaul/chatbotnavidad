<template>
  <q-page class="q-pa-md">
    <q-table
      :rows="preguntas"
      :columns="columns"
      dense
      flat
      bordered
      title="Preguntas y Respuestas"
      :filter="filter"
      :rows-per-page-options="[0]"
    >
      <template v-slot:top-right>
        <q-btn label="Nueva" icon="add" color="green" @click="preguntaNueva" class="q-mr-sm" />
        <q-btn label="Actualizar" icon="refresh" @click="preguntasGet" color="secondary" />
        <q-input dense outlined v-model="filter" label="Buscar" class="q-ml-sm">
          <template v-slot:append><q-icon name="search" /></template>
        </q-input>
      </template>

      <template v-slot:body-cell-actions="props">
        <q-td :props="props">
          <q-btn dense flat icon="edit" color="primary" @click="preguntaEditar(props.row)" :loading="loading"/>
          <q-btn dense flat icon="delete" color="negative" @click="preguntaEliminar(props.row.id)" :loading="loading"/>
        </q-td>
      </template>

      <template v-slot:body-cell-activo="props">
        <q-td :props="props">
          <q-toggle v-model="props.row.activo" @update:model-value="toggleActivo(props.row)" color="green" :false-value="0" :true-value="1" />
        </q-td>
      </template>
      <template v-slot:body-cell-pregunta="props">
        <q-td :props="props">
          <div style="width: 250px; white-space: normal; word-wrap: break-word; line-height: 0.9;">
            {{ props.row.pregunta }}
          </div>
        </q-td>
      </template>
      <template v-slot:body-cell-respuesta="props">
        <q-td :props="props">
          <div style="width: 250px; white-space: normal; word-wrap: break-word; line-height: 0.9;">
            {{ props.row.respuesta }}
          </div>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="preguntaDialog">
      <q-card style="min-width: 400px">
        <q-card-section class="text-h6">{{ pregunta.id ? 'Editar' : 'Nueva' }} Pregunta</q-card-section>
        <q-separator />
        <q-card-section>
          <q-form @submit="preguntaGuardar">
            <q-input v-model="pregunta.pregunta" label="Pregunta" dense outlined :rules="[val => !!val || 'Campo requerido']" />
            <q-input v-model="pregunta.respuesta" label="Respuesta" type="textarea" autogrow outlined dense />
            <q-toggle v-model="pregunta.activo" label="Activa" :false-value="0" :true-value="1" color="green" class="q-mt-md" />
            <div class="text-right q-mt-md">
              <q-btn label="Cancelar" flat @click="preguntaDialog = false" color="negative" :loading="loading" />
              <q-btn label="Guardar" color="primary" type="submit" class="q-ml-sm" :loading="loading" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'PreguntasPage',
  data() {
    return {
      preguntas: [],
      pregunta: {},
      filter: '',
      loading: false,
      preguntaDialog: false,
      columns: [
        { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' },
        { name: 'pregunta', label: 'Pregunta', field: 'pregunta', align: 'left' },
        { name: 'respuesta', label: 'Respuesta', field: 'respuesta', align: 'left' },
        { name: 'activo', label: 'Activo', field: 'activo', align: 'center' },
      ]
    }
  },
  mounted() {
    this.preguntasGet()
  },
  methods: {
    preguntasGet() {
      this.loading = true
      this.$axios.get('preguntas').then(res => {
        this.preguntas = res.data
      }).catch(err => {
        this.$alert.error(err.response.data.message || 'Error al obtener preguntas')
      }).finally(() => {
        this.loading = false
      })
    },
    preguntaNueva() {
      this.pregunta = { pregunta: '', respuesta: '', activo: 1 }
      this.preguntaDialog = true
    },
    preguntaEditar(item) {
      this.pregunta = { ...item }
      this.preguntaDialog = true
    },
    preguntaGuardar() {
      this.loading = true
      if (this.pregunta.id) {
        this.$axios.put(`preguntas/${this.pregunta.id}`, this.pregunta).then(() => {
          this.$alert.success('Pregunta actualizada')
          this.preguntaDialog = false
          this.preguntasGet()
        }).catch(err => {
          this.$alert.error(err.response.data.message || 'Error al actualizar')
        })
      } else {
        this.$axios.post('preguntas', this.pregunta).then(() => {
          this.$alert.success('Pregunta creada')
          this.preguntaDialog = false
          this.preguntasGet()
        }).catch(err => {
          this.$alert.error(err.response.data.message || 'Error al crear')
        })
      }
    },
    preguntaEliminar(id) {
      this.$alert.dialog('¿Eliminar esta pregunta?').onOk(() => {
        this.loading = true
        this.$axios.delete(`preguntas/${id}`).then(() => {
          this.$alert.success('Pregunta eliminada')
          this.preguntasGet()
        }).catch(err => {
          this.$alert.error(err.response.data.message || 'Error al eliminar')
        })
      })
    },
    toggleActivo(item) {
      this.loading = true
      this.$axios.put(`preguntas/${item.id}`, { ...item }).then(() => {
        this.$alert.success('Estado actualizado')
      }).catch(err => {
        this.$alert.error('Error al actualizar estado')
        item.activo = !item.activo // revertir en caso de error
      }).finally(() => {
        this.loading = false
      })
    }
  }
}
</script>
