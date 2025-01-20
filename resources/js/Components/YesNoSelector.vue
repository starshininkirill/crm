<template>
    <div class="flex flex-col">
      <label :for="name" class="block text-sm font-medium leading-6 text-gray-900">
        {{ label }}
      </label>
      <div class="flex  space-x-4">
        <label class="inline-flex items-center">
          <input
            type="radio"
            :name="name"
            :value="1"
            :checked="localValue == 1" 
            v-model="localValue"
            class="form-radio text-indigo-600"
            @change="updateValue"
          />
          <span class="ml-2">Да</span>
        </label>
        <label class="inline-flex items-center">
          <input
            type="radio"
            :name="name"
            :value="0"
            v-model="localValue"
            class="form-radio text-indigo-600"
            @change="updateValue"
            :checked="localValue == 0" 
          />
          <span class="ml-2">Нет</span>
        </label>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      modelValue: {
        type: Number,
        default: null,
      },
      name: {
        type: String,
        required: true,
      },
      label: {
        type: String,
        required: true,
      },
    },
    computed: {
      localValue: {
        get() {
          return this.modelValue;
        },
        set(value) {
          this.$emit('update:modelValue', value);
        },
      },
    },
  };
  </script>
  