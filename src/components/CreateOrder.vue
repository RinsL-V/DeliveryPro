<template>
  <div class="min-h-screen bg-gradient-to-br from-DAE9E8 to-85AFB5 py-8 px-4 relative">
    <div class="noise-overlay-section"></div>

    <div class="container mx-auto max-w-2xl">
      <div class="bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden">

        <button
          @click="$emit('back')"
          class="flex items-center text-393B3C hover:text-85AFB5 mb-6"
        >
          ← Назад
        </button>

        <h2 class="text-3xl font-bold text-393B3C mb-8">
          Создать заказ доставки
        </h2>

        <form @submit.prevent="createOrder" class="space-y-6">

          <!-- Адреса -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
              <label>Адрес отправления *</label>

              <textarea
                v-model="form.from_address"
                @blur="validateAddress('from_address')"
                rows="3"
                class="input"
              ></textarea>

              <p v-if="errors.from_address" class="error">
                {{ errors.from_address }}
              </p>
            </div>

            <div>
              <label>Адрес доставки *</label>

              <textarea
                v-model="form.to_address"
                @blur="validateAddress('to_address')"
                rows="3"
                class="input"
              ></textarea>

              <p v-if="errors.to_address" class="error">
                {{ errors.to_address }}
              </p>
            </div>

          </div>


          <!-- Посылка -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
              <label>Тип посылки *</label>

              <select v-model="form.package_type" class="input">
                <option value="">Выберите</option>
                <option>Документы</option>
                <option>Малая посылка</option>
                <option>Средняя посылка</option>
                <option>Крупная посылка</option>
                <option>Хрупкий груз</option>
                <option>Продукты</option>
              </select>

              <p v-if="errors.package_type" class="error">
                {{ errors.package_type }}
              </p>
            </div>


            <!-- ВЕС -->
            <div>
              <label>Вес (кг) *</label>

              <input
                v-model="form.weight"
                @input="handleWeightInput"
                type="text"
                placeholder="1.5"
                class="input"
              />

              <p v-if="errors.weight" class="error">
                {{ errors.weight }}
              </p>

              <p v-if="weightCategory" class="text-xs text-gray-500 mt-1">
                Категория: {{ weightCategory }}
              </p>

            </div>


            <!-- Габариты -->
            <div>
              <label>Габариты</label>

              <input
                v-model="form.dimensions"
                @blur="validateDimensions"
                placeholder="30x20x10"
                class="input"
              />

              <p v-if="errors.dimensions" class="error">
                {{ errors.dimensions }}
              </p>

            </div>

          </div>


          <!-- Описание -->
          <div>
            <label>Описание</label>

            <textarea
              v-model="form.description"
              rows="3"
              class="input"
            ></textarea>
          </div>


          <!-- Кнопки -->
          <div class="flex gap-4 pt-4">

            <button
              type="button"
              @click="$emit('back')"
              class="flex-1 border py-3 rounded-lg"
            >
              Отмена
            </button>

            <button
              type="submit"
              :disabled="loading"
              class="flex-1 bg-1AF9D5 py-3 rounded-lg"
            >
              {{ loading ? 'Создание...' : 'Создать заказ' }}
            </button>

          </div>


          <!-- Сообщение -->
          <div
            v-if="message"
            :class="success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
            class="p-4 rounded-lg"
          >
            {{ message }}
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const emit = defineEmits(['order-created','back'])

const form = ref({
  from_address: '',
  to_address: '',
  package_type: '',
  weight: '',
  dimensions: '',
  description: ''
})

const errors = ref({})

const loading = ref(false)
const message = ref('')
const success = ref(false)



/* ---------------- ВЕС ---------------- */

function handleWeightInput(e){

  let value = e.target.value

  value = value.replace(',', '.')
  value = value.replace(/[^0-9.]/g,'')

  const parts = value.split('.')
  if(parts.length > 2){
    value = parts[0] + '.' + parts[1]
  }

  form.value.weight = value
  validateWeight()
}


function validateWeight(){

  const weight = parseFloat(form.value.weight)

  if(!form.value.weight){
    errors.value.weight = 'Введите вес'
    return false
  }

  if(isNaN(weight)){
    errors.value.weight = 'Вес должен быть числом'
    return false
  }

  if(weight < 0.1){
    errors.value.weight = 'Минимум 0.1 кг'
    return false
  }

  if(weight > 50){
    errors.value.weight = 'Максимум 50 кг'
    return false
  }

  errors.value.weight = ''
  return true
}


/* -------- категория веса (бонус) -------- */

const weightCategory = computed(()=>{

  const w = parseFloat(form.value.weight)

  if(!w) return ''

  if(w <= 1) return 'Лёгкая посылка'

  if(w <= 10) return 'Стандартная'

  return 'Тяжёлая'

})



/* ---------------- АДРЕС ---------------- */

function validateAddress(field){

  const value = form.value[field]

  if(!value || value.length < 5){
    errors.value[field] = 'Введите полный адрес'
    return false
  }

  const hasNumber = /\d/.test(value)

  if(!hasNumber){
    errors.value[field] = 'Адрес должен содержать номер дома'
    return false
  }

  errors.value[field] = ''
  return true
}



/* ---------------- ГАБАРИТЫ ---------------- */

function validateDimensions(){

  if(!form.value.dimensions){
    errors.value.dimensions = ''
    return true
  }

  const regex = /^\d+x\d+x\d+$/

  if(!regex.test(form.value.dimensions)){
    errors.value.dimensions = 'Формат: 30x20x10'
    return false
  }

  errors.value.dimensions = ''
  return true
}



/* ---------------- ОБЩАЯ ПРОВЕРКА ---------------- */

function validateForm(){

  let valid = true

  if(!validateAddress('from_address')) valid = false
  if(!validateAddress('to_address')) valid = false
  if(!validateWeight()) valid = false
  if(!validateDimensions()) valid = false

  if(!form.value.package_type){
    errors.value.package_type = 'Выберите тип'
    valid = false
  } else {
    errors.value.package_type = ''
  }

  return valid
}



/* ---------------- СОЗДАНИЕ ЗАКАЗА ---------------- */

const createOrder = async ()=>{

  if(!validateForm()) return

  try{

    loading.value = true
    message.value = ''

    const user = JSON.parse(localStorage.getItem('user_data'))

    const response = await fetch('http://localhost:8000/api/orders.php',{
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({
        ...form.value,
        user_id: user.id
      })
    })

    const data = await response.json()

    if(data.success){

      success.value = true
      message.value = 'Заказ создан #' + data.order_id

      form.value = {
        from_address:'',
        to_address:'',
        package_type:'',
        weight:'',
        dimensions:'',
        description:''
      }

      setTimeout(()=>{
        emit('order-created')
      },2000)

    } else {

      success.value = false
      message.value = data.message || 'Ошибка'

    }

  }catch(e){

    success.value = false
    message.value = 'Ошибка соединения'

  }finally{
    loading.value = false
  }

}

</script>

<style scoped>

.input{
width:100%;
padding:12px;
border:1px solid #DAE6E6;
border-radius:8px;
}

.error{
color:#ef4444;
font-size:13px;
margin-top:4px;
}

</style>