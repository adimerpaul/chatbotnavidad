<template>
  <q-page class="q-pa-md">
    <q-table
      :rows="numeros"
      :columns="columns"
      row-key="phone"
      flat bordered dense
      title="Atención al Cliente"
      :filter="filter"
      :rows-per-page-options="[0]"
    >
      <template v-slot:top-right>
        <q-btn label="Actualizar" icon="refresh" @click="obtenerNumeros" color="primary" class="q-mr-sm" />
        <q-input outlined dense v-model="filter" label="Buscar" class="q-ml-sm">
          <template v-slot:append><q-icon name="search" /></template>
        </q-input>
      </template>

      <template v-slot:body-cell-en_atencion="props">
        <q-td :props="props">
          <q-badge :color="props.row.en_atencion ? 'green' : 'grey'" align="middle">
            {{ props.row.en_atencion ? 'Sí' : 'No' }}
          </q-badge>
        </q-td>
      </template>

      <template v-slot:body-cell-acciones="props">
        <q-td :props="props">
          <q-btn
            v-if="!props.row.en_atencion"
            label="Atender"
            icon="person_add"
            color="primary"
            dense
            @click="agregar(props.row.phone)"
          />
          <q-btn
            v-else
            label="Quitar"
            icon="person_remove"
            color="negative"
            dense
            @click="quitar(props.row.phone)"
          />
        </q-td>
      </template>
    </q-table>
  </q-page>
</template>

<script>
export default {
  name: 'AtencionClientePage',
  data () {
    return {
      numeros: [],
      filter: '',
      columns: [
        { name: 'phone', label: 'Teléfono', field: 'phone', align: 'left' },
        { name: 'en_atencion', label: 'En Atención', field: 'en_atencion', align: 'center' },
        { name: 'acciones', label: 'Acciones', field: 'acciones', align: 'center' }
      ]
    }
  },
  mounted () {
    this.obtenerNumeros()
  },
  methods: {
    obtenerNumeros () {
      this.$axios.get('/atencion-clientes/number')
        .then(res => {
          this.numeros = res.data
        })
        .catch(() => {
          this.$alert.error('Error al cargar los números')
        })
    },
    agregar (phone) {
      this.$axios.post('/atencion-manual', {
        phone: phone,
        hora_atencion: new Date().toISOString().slice(0, 19).replace('T', ' ')
      }).then(() => {
        this.$alert.success('Agregado a atención manual')
        this.obtenerNumeros()
      }).catch(() => {
        this.$alert.error('Error al agregar')
      })
    },
    quitar (phone) {
      this.$axios.delete(`/atencion-manual/${phone}`)
        .then(() => {
          this.$alert.success('Quitado de atención manual')
          this.obtenerNumeros()
        })
        .catch(() => {
          this.$alert.error('Error al quitar')
        })
    }
  }
}
</script>
