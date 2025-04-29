<template>
   <div>
      <label v-if="label" class="label">
         {{ label }}
         <Info v-if="info" :text="info" />
      </label>
      <div class="mt-1">
         <input 
            :type="type" 
            :name="name" 
            :placeholder="placeholder" 
            :value="modelValue" 
            :required="required"
            :maxlength="maxLen"
            v-bind="$attrs" 
            @input="handleInput"
            class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
         />
      </div>
   </div>
</template>

<script>
import Info from './Info.vue';

export default {
   components: {
      Info,
   },
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
      },
      info: {
         type: String,
      },
      maxLen: {
         type: String,
         default: null
      }
   },
   methods: {
      handleInput(event) {
         let value = event.target.value;
         
         if (this.maxLen && value.length > this.maxLen) {
            value = value.slice(0, this.maxLen);

            event.target.value = value;
         }
         
         this.$emit('update:modelValue', value);
      }
   }
};
</script>