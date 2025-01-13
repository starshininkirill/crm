<template>
   <div>
      <label v-if="label" class="block text-sm font-medium leading-6 text-gray-900">
         {{ label }}
      </label>
      <div class="mt-1">
         <input 
            :type="type" 
            :name="name" 
            :placeholder="placeholder" 
            :value="modelValue" 
            :required="required"
            v-bind="$attrs"
            @input="$emit('update:modelValue', $event.target.value)" 
            class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
      </div>
   </div>
</template>

<script>
export default {
   inheritAttrs: false,
   props: {
      modelValue: { 
         type: [String, Number],
         default: ''
      },
      type: {
         type: String,
         default: 'text'
      },
      name: {
         type: String,
         required: true
      },
      placeholder: {
         type: String,
         default: ''
      },
      label: {
         type: String,
         default: ''
      },
      required: {
         type: Boolean,
         default: false
      }
   }
};
</script>