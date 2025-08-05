<template>
  <BaseLayout>
    <div class="flex gap-5 h-auto grow">
      <div class=" w-48 bg-gray-800 text-white flex flex-col">
        <nav class="">
          <ul class="space-y-1 p-4">

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.contract.index') }"
                :href="route('admin.contract.index')">
              Договоры
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.payment.index') }"
                :href="route('admin.payment.index')">
              Платежи
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.organization.index') }"
                :href="route('admin.organization.index')">
              Организации
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.service.index') }"
                :href="route('admin.service.index')">
              Услуги
              </Link>
            </li>
            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.user.index') }"
                :href="route('admin.time-check.index')">
              Кадры
              </Link>
            </li>
            <!-- <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.department.index') }"
                :href="route('admin.department.index')">
              Отделы
              </Link>
            </li> -->

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.sale-department.info') }"
                :href="route('admin.sale-department.info')">
              Отдел продаж
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.projects-department.info') }"
                :href="route('admin.projects-department.info')">
              Отдел Сопровождения
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.advertising-department.info') }"
                :href="route('admin.advertising-department.info')">
                Отдел Рекламы
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.settings.main') }"
                :href="route('admin.settings.main')">
              Настройки
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.document-generator.templates') }"
                :href="route('admin.document-generator.templates')">
              Генератор документов
              </Link>
            </li>

          </ul>
        </nav>

      </div>

      <section style="flex: 1;">
        <slot />
      </section>
    </div>
  </BaseLayout>
</template>

<script>
import BaseLayout from './BaseLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
  name: "AdminLayout",
  components: { BaseLayout },

  methods: {
    isActive(routeName) {
      const page = usePage();
      const currentRoute = page.props.ziggy.location;
      const targetRoute = route(routeName);

      return currentRoute === targetRoute || currentRoute.startsWith(targetRoute + '/');
    }
  },
};
</script>